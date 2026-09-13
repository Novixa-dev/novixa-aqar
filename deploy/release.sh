#!/usr/bin/env bash
#
# Novixa Aqar — server-side release script (Dexal cPanel, no root).
#
# Invoked over SSH by .github/workflows/deploy-demo.yml with an already
# built artifact. Performs an immutable, zero-downtime release:
#
#   ~/app/releases/<timestamp>/   full application code, one per release
#   ~/app/shared/.env             persisted across releases
#   ~/app/shared/storage/         persisted across releases (uploads, logs)
#   ~/app/current  ->  releases/<timestamp>      atomically swapped
#   ~/public_html/*  ->  ../app/current/public/* per-file symlinks
#
# The web root points at `current`, never at a specific release, so the
# switch is a single atomic rename(2) — no request ever observes a
# half-written release.
#
# Usage: release.sh <artifact.tar.gz> [health-url]
set -euo pipefail

ARTIFACT="${1:?usage: release.sh <artifact.tar.gz> [health-url]}"
HEALTH_URL="${2:-https://aqar-demo.dexal.net/}"

APP_ROOT="$HOME/app"
RELEASES="$APP_ROOT/releases"
SHARED="$APP_ROOT/shared"
CURRENT="$APP_ROOT/current"
WEBROOT="$HOME/public_html"
KEEP_RELEASES=3
LOCK="$APP_ROOT/.deploy.lock"

PHP="$(command -v php)"
TS="$(date +%Y%m%d%H%M%S)"
NEW="$RELEASES/$TS"

log()  { printf '\n\033[1m==> %s\033[0m\n' "$*"; }
warn() { printf '\033[33m!! %s\033[0m\n' "$*"; }
die()  { printf '\033[31mXX %s\033[0m\n' "$*" >&2; exit 1; }

# Only one deploy at a time. A second concurrent run would race on the
# `current` symlink and could leave the webroot pointing at a release
# that the other run is still writing.
exec 9>"$LOCK"
flock -n 9 || die "another deploy is in progress (lock: $LOCK)"

PREVIOUS=""
if [ -L "$CURRENT" ]; then
  PREVIOUS="$(readlink "$CURRENT")"   # e.g. releases/20260912045702
fi
log "previous release: ${PREVIOUS:-<none>}"

# --------------------------------------------------------------- unpack
log "unpacking artifact into $NEW"
[ -f "$ARTIFACT" ] || die "artifact not found: $ARTIFACT"
mkdir -p "$NEW"
tar -xzf "$ARTIFACT" -C "$NEW"
[ -f "$NEW/artisan" ] || die "artifact looks wrong: no artisan in $NEW"
[ -d "$NEW/vendor" ] || die "artifact looks wrong: no vendor/ in $NEW"
[ -d "$NEW/public/build" ] || warn "no public/build in artifact — frontend assets may be missing"

# ------------------------------------------------- wire shared state in
log "wiring shared .env and storage"
mkdir -p "$SHARED/storage/app/public" \
         "$SHARED/storage/framework/cache/data" \
         "$SHARED/storage/framework/sessions" \
         "$SHARED/storage/framework/views" \
         "$SHARED/storage/logs"
[ -f "$SHARED/.env" ] || die "missing $SHARED/.env — create it once before the first automated deploy"

rm -rf "$NEW/storage"
ln -s ../../shared/storage "$NEW/storage"
ln -sfn ../../shared/.env "$NEW/.env"
mkdir -p "$NEW/bootstrap/cache"
chmod -R 775 "$NEW/bootstrap/cache"

# public/storage -> ../storage/app/public (storage:link, but declared here
# so it survives a fresh release without an extra artisan call)
ln -sfn ../storage/app/public "$NEW/public/storage"

# ----------------------------------------------------------- migrations
# Runs BEFORE the switch: if the schema change fails, the live release is
# untouched and nothing has been swapped.
#
# NOTE: schema changes are deliberately NOT rolled back automatically on a
# failed health check further down. `migrate:rollback` can destroy data,
# and an automated rollback of a partially applied migration is more
# dangerous than a human looking at it. Code rolls back automatically;
# schema does not.
log "running migrations"
if ! (cd "$NEW" && "$PHP" artisan migrate --force --no-interaction); then
  warn "migration failed — discarding $NEW, live release untouched"
  rm -rf "$NEW"
  die "deploy aborted during migrations"
fi

# ------------------------------------------------- warm caches (pre-cut)
# Built before the switch so the release is fully warm the instant it goes
# live — no first-request compile penalty for a real visitor.
log "warming caches"
(
  cd "$NEW"
  "$PHP" artisan config:cache
  "$PHP" artisan route:cache
  "$PHP" artisan view:cache
  "$PHP" artisan event:cache
) || { rm -rf "$NEW"; die "cache warm failed — live release untouched"; }

# ------------------------------------------------------- atomic switch
# ln -sfn is unlink+symlink (briefly no symlink at all). Creating a temp
# symlink and mv -T'ing it over is a single rename(2): atomic.
log "switching current -> releases/$TS"
ln -s "releases/$TS" "$APP_ROOT/.current.tmp.$$"
mv -T "$APP_ROOT/.current.tmp.$$" "$CURRENT"

sync_webroot() {
  # public_html must contain a symlink per entry in the release's public/,
  # because cPanel owns public_html itself (it also holds .well-known for
  # SSL validation and the cPanel-managed PHP handler .htaccess, neither
  # of which may be destroyed). Re-synced every deploy so files added to
  # or removed from public/ are reflected.
  local pub="$CURRENT/public"
  for item in "$pub"/*; do
    [ -e "$item" ] || continue
    local name
    name="$(basename "$item")"
    # .htaccess is merged/managed on the server, never overwritten from
    # the release (it carries the cPanel PHP-version handler).
    [ "$name" = ".htaccess" ] && continue
    ln -sfn "../app/current/public/$name" "$WEBROOT/$name"
  done
  # Drop webroot symlinks whose target no longer exists in this release.
  for link in "$WEBROOT"/*; do
    [ -L "$link" ] || continue
    [ -e "$link" ] || rm -f "$link"
  done
}

log "re-syncing public_html symlinks"
sync_webroot

# ---------------------------------------------------------- health check
log "health check: $HEALTH_URL"
HEALTH=""
for attempt in 1 2 3 4 5; do
  HEALTH="$(curl -sS -o /dev/null -w '%{http_code}' --max-time 25 "$HEALTH_URL" || echo 000)"
  [ "$HEALTH" = "200" ] && break
  warn "attempt $attempt returned $HEALTH — retrying"
  sleep 4
done

if [ "$HEALTH" != "200" ]; then
  if [ -n "$PREVIOUS" ] && [ -d "$APP_ROOT/$PREVIOUS" ]; then
    warn "health check failed ($HEALTH) — rolling back to $PREVIOUS"
    ln -s "$PREVIOUS" "$APP_ROOT/.current.tmp.rb.$$"
    mv -T "$APP_ROOT/.current.tmp.rb.$$" "$CURRENT"
    sync_webroot
    ROLLED="$(curl -sS -o /dev/null -w '%{http_code}' --max-time 25 "$HEALTH_URL" || echo 000)"
    warn "after rollback the site returns $ROLLED"
    warn "SCHEMA WAS NOT ROLLED BACK — any migration this deploy applied is still in place. Review before redeploying."
    die "deploy rolled back (new release kept at $NEW for inspection)"
  fi
  die "health check failed ($HEALTH) and there is no previous release to roll back to"
fi

log "health check OK (200)"

# --------------------------------------------------------------- prune
log "pruning old releases (keeping $KEEP_RELEASES)"
KEEPING="$(basename "$(readlink "$CURRENT")")"
# shellcheck disable=SC2012
ls -1 "$RELEASES" | sort -r | tail -n +$((KEEP_RELEASES + 1)) | while read -r old; do
  [ "$old" = "$KEEPING" ] && continue
  echo "    removing $old"
  rm -rf "${RELEASES:?}/$old"
done

rm -f "$ARTIFACT"

log "released $TS — disk: $(du -sh "$APP_ROOT" 2>/dev/null | cut -f1)"
