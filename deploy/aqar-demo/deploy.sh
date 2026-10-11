#!/usr/bin/env bash
#
# Novixa Aqar demo — zero-downtime release for <site>.novixa.dev.
#
# Runs on the VPS as the `aqar` user from /opt/<site>, invoked by
# .github/workflows/deploy-<site>.yml after the image has been pushed and
# this directory's files (compose.yml, app.env, nginx.conf) uploaded.
#
# Everything is derived from the directory name, so one script serves every
# demo site: /opt/aqar-demo → aqar-demo.novixa.dev, /opt/aqar-demo2 →
# aqar-demo2.novixa.dev, each with its own containers, database and volumes.
#
#   /opt/<site>/deploy.sh <image-ref>   e.g. ghcr.io/novixa-dev/novixa-aqar:sha-abc123
#
# Order is chosen so that a failure at any step leaves the live site serving
# the previous release:
#
#   1. check the image is loaded, start/verify MySQL
#   2. migrate (one-off container) — first deploy also seeds the demo data;
#      a changed DEMO_SEED_VERSION seeds a fresh database beside the live one
#   3. start the new release next to the old one, wait until it answers 200
#   4. install nginx site (nginx -t first; previous file restored on failure)
#   5. verify end-to-end through mailcow's nginx with the new release attached
#   6. drain: detach the old release from nginx, wait out DNS TTL, remove it
#
# Schema is not rolled back automatically: migrate:rollback can destroy data.
set -euo pipefail

IMAGE="${1:?usage: deploy.sh <image-ref>}"
APP_DIR="$(cd "$(dirname "$0")" && pwd)"
SITE="$(basename "$APP_DIR")"
HOST="$SITE.novixa.dev"
EDGE=aqar-edge                       # shared with mailcow's nginx
INTERNAL="$SITE-internal"
ALIAS="$SITE-app"
LABEL="novixa.$SITE.role=app"
MAILCOW_NGINX_DIR=/opt/mailcow-dockerized/data/conf/nginx
KEEP_IMAGES=3

cd "$APP_DIR"
export SITE

log()  { printf '\n\033[1m==> %s\033[0m\n' "$*"; }
warn() { printf '\033[33m!! %s\033[0m\n' "$*"; }
die()  { printf '\033[31mXX %s\033[0m\n' "$*" >&2; exit 1; }

exec 9>"$APP_DIR/.deploy.lock"
flock -n 9 || die "another deploy is in progress"

for f in compose.yml app.env nginx.conf db.env secrets.env; do
  [ -f "$f" ] || die "missing $APP_DIR/$f (run server-setup.sh / check the upload step)"
done

NGINX_CTR="$(docker ps -q --filter label=com.docker.compose.service=nginx-mailcow | head -n1)"
[ -n "$NGINX_CTR" ] || die "mailcow nginx container is not running"

# Which database the live release uses. "aqar" (compose's MYSQL_DATABASE)
# until the first demo reseed; see step 2.
DB_NAME="$(cat "$APP_DIR/.db-name" 2>/dev/null || echo aqar)"
SEED_VERSION="$(sed -n 's/^DEMO_SEED_VERSION=//p' app.env | tr -dc 'A-Za-z0-9_')"
SEEDED="$(cat "$APP_DIR/.seeded" 2>/dev/null || true)"

# Arguments shared by every container started from the app image. The
# database is passed explicitly (-e wins over --env-file) so a reseed can
# point the new release at a different database than the live one.
base_args=(
  --env-file "$APP_DIR/app.env"
  --env-file "$APP_DIR/secrets.env"
  -v "$SITE-storage:/var/www/html/storage/app"
  -v "$SITE-logs:/var/www/html/storage/logs"
  --network "$INTERNAL"
)

db_root() { # SQL on stdin, as MySQL root inside the db container
  docker exec -i "$SITE-db" sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" 2>/dev/null'
}

# ------------------------------------------------------------- 1. pull + db
# The workflow streams the image in over SSH (docker save | docker load)
# before calling this script; tags are per-commit, so present = correct.
log "image $IMAGE"
docker image inspect "$IMAGE" >/dev/null 2>&1 || die "image $IMAGE is not loaded on this host"

log "database"
docker compose -p "$SITE" -f compose.yml up -d --wait db

# ---------------------------------------------------------- 2. migrate/seed
OLD_DB=""
if [ -z "$SEEDED" ]; then
  # First deploy of this site: migrate and seed the database compose made.
  log "first deploy: migrating and seeding demo data into $DB_NAME"
  docker run --rm "${base_args[@]}" -e DB_DATABASE="$DB_NAME" "$IMAGE" \
    sh -c 'php artisan migrate --force --no-interaction && php artisan db:seed --force --no-interaction' \
    || die "first seed failed — nothing is live yet; fix and re-run"
  echo "${SEED_VERSION:-1}" > "$APP_DIR/.seeded"
  echo "$DB_NAME" > "$APP_DIR/.db-name"
elif [ -n "$SEED_VERSION" ] && [ "$SEEDED" != "$SEED_VERSION" ]; then
  # Demo data changed (DEMO_SEED_VERSION bumped in app.env). Build a fresh,
  # fully seeded database BESIDE the live one; the live release keeps
  # serving the old database until the switch in step 5, so visitors never
  # see an empty or half-seeded site. The old database is dropped only
  # after a successful switch.
  OLD_DB="$DB_NAME"
  DB_NAME="aqar_v$SEED_VERSION"
  [ "$DB_NAME" != "$OLD_DB" ] || die "DEMO_SEED_VERSION=$SEED_VERSION would reseed the live database in place"
  log "demo data v$SEEDED -> v$SEED_VERSION: seeding fresh database $DB_NAME"
  printf 'DROP DATABASE IF EXISTS `%s`; CREATE DATABASE `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON `%s`.* TO `aqar`@`%%`;\n' \
    "$DB_NAME" "$DB_NAME" "$DB_NAME" | db_root || die "could not create $DB_NAME"
  docker run --rm "${base_args[@]}" -e DB_DATABASE="$DB_NAME" "$IMAGE" \
    sh -c 'php artisan migrate --force --no-interaction && php artisan db:seed --force --no-interaction' \
    || die "reseed into $DB_NAME failed — live release untouched on $OLD_DB"
else
  log "running migrations on $DB_NAME"
  docker run --rm "${base_args[@]}" -e DB_DATABASE="$DB_NAME" "$IMAGE" php artisan migrate --force --no-interaction \
    || die "migrations failed — live release untouched"
fi

run_args=("${base_args[@]}" -e DB_DATABASE="$DB_NAME")

# ------------------------------------------------------- 3. start new release
OLD_CTRS="$(docker ps -aq --filter "label=$LABEL")"
NEW="$SITE-app-$(date +%Y%m%d%H%M%S)"

log "starting $NEW"
# --user 1000 (the image's `octane` user) from the very start: the runtime
# hands the container stdout/stderr pipes owned by the user it starts as.
# Starting as root and dropping to 1000 inside (start-container's su-exec
# path) leaves supervisord unable to reopen /dev/stdout — octane then dies
# with "EACCES making dispatchers". The one-off migrate/seed containers above
# stay root so start-container still fixes storage/app ownership.
docker create --name "$NEW" --label "$LABEL" --label "novixa.$SITE.image=$IMAGE" --label "novixa.$SITE.db=$DB_NAME" \
  --user 1000:1000 --restart unless-stopped --memory 1536m \
  --log-driver json-file --log-opt max-size=10m --log-opt max-file=5 \
  "${run_args[@]}" "$IMAGE" >/dev/null
docker start "$NEW" >/dev/null

discard_new() {
  warn "last log lines of $NEW:"
  docker logs --tail 60 "$NEW" 2>&1 || true
  docker rm -f "$NEW" >/dev/null 2>&1 || true
}

probe() { # probe <path> — HTTP status from inside the new container
  docker exec "$NEW" curl -s -o /dev/null -w '%{http_code}' --max-time 20 \
    -H "Host: $HOST" -H 'X-Forwarded-Proto: https' "http://127.0.0.1:8000$1" 2>/dev/null || echo 000
}

log "waiting for $NEW to answer"
code=000
for _ in $(seq 1 60); do
  [ "$(docker inspect -f '{{.State.Running}}' "$NEW" 2>/dev/null)" = true ] || { discard_new; die "$NEW exited during boot — live release untouched"; }
  code="$(probe /)"
  [ "$code" = 200 ] && break
  sleep 3
done
[ "$code" = 200 ] || { discard_new; die "$NEW never returned 200 on / (last: $code) — live release untouched"; }

for path in /admin/login /app/login /landlord/login /tenant/login; do
  c="$(probe "$path")"
  [ "$c" = 200 ] || { discard_new; die "$NEW returned $c on $path — live release untouched"; }
  echo "    $path -> $c"
done

# --------------------------------------------------------------- 4. nginx site
log "nginx site"
# nginx.conf is a template shared by every site: __SITE__ → this site.
SITE_CONF="$MAILCOW_NGINX_DIR/$SITE.conf"
sed "s/__SITE__/$SITE/g" nginx.conf > "$APP_DIR/.nginx.conf.rendered"
if ! cmp -s "$APP_DIR/.nginx.conf.rendered" "$SITE_CONF"; then
  cp "$SITE_CONF" "$APP_DIR/.nginx.conf.prev"
  cat "$APP_DIR/.nginx.conf.rendered" > "$SITE_CONF"   # keep ownership/mode set by server-setup.sh
  if ! docker exec "$NGINX_CTR" nginx -t >/dev/null 2>&1; then
    cat "$APP_DIR/.nginx.conf.prev" > "$SITE_CONF"
    docker exec "$NGINX_CTR" nginx -t 2>&1 | tail -5 || true
    discard_new
    die "new nginx site failed nginx -t — previous file restored, live release untouched"
  fi
  docker exec "$NGINX_CTR" nginx -s reload
  echo "    installed and reloaded"
else
  echo "    unchanged"
fi

# ------------------------------------------ 5. attach new release + verify
log "attaching $NEW to $EDGE"
docker network connect --alias "$ALIAS" "$EDGE" "$NEW"

edge_probe() { # through mailcow's nginx, exactly as Cloudflare reaches it
  curl -sk -o /dev/null -w '%{http_code}' --max-time 20 \
    --resolve "$HOST:443:127.0.0.1" "https://$HOST$1" 2>/dev/null || echo 000
}

# Detach the old releases first so every request now goes to $NEW, then wait
# out nginx's 5s resolver TTL before judging it.
for c in $OLD_CTRS; do
  docker network disconnect "$EDGE" "$c" >/dev/null 2>&1 || true
done
sleep 7

code=000
for _ in 1 2 3 4 5; do
  code="$(edge_probe /)"
  [ "$code" = 200 ] && break
  sleep 3
done

if [ "$code" != 200 ]; then
  warn "through nginx the site returned $code — rolling back"
  for c in $OLD_CTRS; do
    docker network connect --alias "$ALIAS" "$EDGE" "$c" >/dev/null 2>&1 || true
  done
  discard_new
  die "deploy rolled back; previous release is serving again"
fi
echo "    https://$HOST/ -> 200 via nginx"

# --------------------------------------------------------------- 6. retire old
if [ -n "$OLD_CTRS" ]; then
  log "removing previous release(s)"
  docker rm -f $OLD_CTRS >/dev/null
fi

echo "$IMAGE" > "$APP_DIR/current-image"

if [ -n "$OLD_DB" ]; then
  echo "$DB_NAME" > "$APP_DIR/.db-name"
  echo "$SEED_VERSION" > "$APP_DIR/.seeded"
  log "dropping previous demo database $OLD_DB"
  printf 'DROP DATABASE IF EXISTS `%s`;\n' "$OLD_DB" | db_root || warn "could not drop $OLD_DB (harmless; drop it by hand)"
fi

log "pruning old images (keeping $KEEP_IMAGES)"
REPO="${IMAGE%:*}"
docker image ls "$REPO" --format '{{.CreatedAt}}\t{{.Repository}}:{{.Tag}}' \
  | sort -r | tail -n +$((KEEP_IMAGES + 1)) | cut -f2 \
  | while read -r img; do
      [ "$img" = "$IMAGE" ] && continue
      docker image rm "$img" >/dev/null 2>&1 && echo "    removed $img" || true
    done

log "released $IMAGE as $NEW"
