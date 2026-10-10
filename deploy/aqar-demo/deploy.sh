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
#   1. pull image, start/verify MySQL
#   2. migrate (one-off container) — first deploy also seeds the demo data
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
# Per-site registry login (the workflow logs in with this same path), so two
# sites deploying at once never log each other out mid-pull.
export DOCKER_CONFIG="$APP_DIR/.docker"
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

# Arguments shared by every container started from the app image.
run_args=(
  --env-file "$APP_DIR/app.env"
  --env-file "$APP_DIR/secrets.env"
  -v "$SITE-storage:/var/www/html/storage/app"
  -v "$SITE-logs:/var/www/html/storage/logs"
  --network "$INTERNAL"
)

# ------------------------------------------------------------- 1. pull + db
# Tags are per-commit (sha-…), so an image already present is the right one.
log "image $IMAGE"
docker image inspect "$IMAGE" >/dev/null 2>&1 || docker pull -q "$IMAGE"

log "database"
docker compose -p "$SITE" -f compose.yml up -d --wait db

# ---------------------------------------------------------- 2. migrate/seed
log "running migrations"
docker run --rm "${run_args[@]}" "$IMAGE" php artisan migrate --force --no-interaction \
  || die "migrations failed — live release untouched"

if [ ! -f "$APP_DIR/.seeded" ]; then
  log "first deploy: seeding demo data"
  docker run --rm "${run_args[@]}" "$IMAGE" php artisan db:seed --force --no-interaction \
    || die "seeding failed — live release untouched (re-run after fixing; see logs above)"
  date -u +%FT%TZ > "$APP_DIR/.seeded"
fi

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
docker create --name "$NEW" --label "$LABEL" --label "novixa.$SITE.image=$IMAGE" \
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

log "pruning old images (keeping $KEEP_IMAGES)"
REPO="${IMAGE%:*}"
docker image ls "$REPO" --format '{{.CreatedAt}}\t{{.Repository}}:{{.Tag}}' \
  | sort -r | tail -n +$((KEEP_IMAGES + 1)) | cut -f2 \
  | while read -r img; do
      [ "$img" = "$IMAGE" ] && continue
      docker image rm "$img" >/dev/null 2>&1 && echo "    removed $img" || true
    done

log "released $IMAGE as $NEW"
