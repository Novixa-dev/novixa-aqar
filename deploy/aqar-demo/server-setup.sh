#!/usr/bin/env bash
#
# Novixa Aqar demo — one-time server preparation for <site>.novixa.dev.
#
# Run once per site, as root, on the VPS (161.97.75.64). Idempotent:
# re-running only fills in what is missing and never regenerates a secret.
#
#   bash server-setup.sh aqar-demo  "<CI public key line>"
#   bash server-setup.sh aqar-demo2 "<CI public key line>"
#
# What it does:
#   * creates the `aqar` deploy user (docker group, SSH key only — no password)
#   * creates /opt/<site> and its secrets (db.env, secrets.env), once
#   * creates the `aqar-edge` network and joins mailcow's nginx to it, both
#     live (no restart, mail is never interrupted) and in mailcow's
#     docker-compose.override.yml so it survives mailcow updates
#   * gives the site its own copy of the *.novixa.dev Cloudflare Origin cert
set -euo pipefail

SITE="${1:?usage: server-setup.sh <site> [CI public key line]}"
CI_PUBKEY="${2:-}"
case "$SITE" in aqar-demo|aqar-demo[0-9]*) ;; *) echo "unexpected site name: $SITE" >&2; exit 1;; esac
APP_DIR="/opt/$SITE"
MAILCOW=/opt/mailcow-dockerized
NGINX_CONF_DIR="$MAILCOW/data/conf/nginx"
EDGE=aqar-edge
USER_NAME=aqar

log() { printf '\n==> %s\n' "$*"; }
die() { printf 'XX %s\n' "$*" >&2; exit 1; }

[ "$(id -u)" = 0 ] || die "run as root"
command -v docker >/dev/null || die "docker is not installed"
[ -d "$NGINX_CONF_DIR" ] || die "mailcow not found at $MAILCOW"

# ------------------------------------------------------------ deploy user
log "deploy user: $USER_NAME"
if ! id "$USER_NAME" >/dev/null 2>&1; then
  useradd --system --create-home --home-dir "/home/$USER_NAME" --shell /bin/bash "$USER_NAME"
fi
usermod -aG docker "$USER_NAME"
passwd -l "$USER_NAME" >/dev/null   # key-only
install -d -m 700 -o "$USER_NAME" -g "$USER_NAME" "/home/$USER_NAME/.ssh"
AUTH="/home/$USER_NAME/.ssh/authorized_keys"
touch "$AUTH"
if [ -n "$CI_PUBKEY" ] && ! grep -qxF "$CI_PUBKEY" "$AUTH"; then
  echo "$CI_PUBKEY" >> "$AUTH"
fi
chown "$USER_NAME:$USER_NAME" "$AUTH"; chmod 600 "$AUTH"

# --------------------------------------------------------- app directory
log "app directory: $APP_DIR"
install -d -m 750 -o "$USER_NAME" -g "$USER_NAME" "$APP_DIR"

rand() { openssl rand -base64 48 | tr -dc 'A-Za-z0-9' | head -c "$1"; }

if [ ! -f "$APP_DIR/db.env" ] || [ ! -f "$APP_DIR/secrets.env" ]; then
  [ -f "$APP_DIR/db.env" ] && die "db.env exists but secrets.env does not — refusing to generate a mismatched DB password"
  DB_PASS="$(rand 32)"
  ROOT_PASS="$(rand 32)"
  APP_KEY="base64:$(openssl rand -base64 32)"
  umask 077
  printf 'MYSQL_ROOT_PASSWORD=%s\nMYSQL_PASSWORD=%s\n' "$ROOT_PASS" "$DB_PASS" > "$APP_DIR/db.env"
  printf 'APP_KEY=%s\nDB_PASSWORD=%s\n' "$APP_KEY" "$DB_PASS" > "$APP_DIR/secrets.env"
  umask 022
  log "generated db.env and secrets.env (kept on this server only)"
fi
chown "$USER_NAME:$USER_NAME" "$APP_DIR/db.env" "$APP_DIR/secrets.env"
chmod 600 "$APP_DIR/db.env" "$APP_DIR/secrets.env"

# ------------------------------------------------------- edge network
log "network $EDGE"
docker network inspect "$EDGE" >/dev/null 2>&1 || docker network create "$EDGE" >/dev/null

NGINX_CTR="$(docker ps -q --filter label=com.docker.compose.service=nginx-mailcow | head -n1)"
[ -n "$NGINX_CTR" ] || die "mailcow nginx container is not running"
if ! docker inspect -f '{{json .NetworkSettings.Networks}}' "$NGINX_CTR" | grep -q "\"$EDGE\""; then
  docker network connect "$EDGE" "$NGINX_CTR"   # live, no restart
fi

# Persist it across mailcow updates (which recreate the nginx container).
OVR="$MAILCOW/docker-compose.override.yml"
if ! grep -q "$EDGE" "$OVR" 2>/dev/null; then
  [ -f "$OVR" ] && cp -a "$OVR" "$OVR.bak.$(date +%Y%m%d%H%M%S)"
  if [ -f "$OVR" ] && grep -q "novixa-edge" "$OVR"; then
    # Known shape written by Novixa's server-setup.sh: add a sibling entry
    # under nginx-mailcow.networks and under top-level networks.
    python3 - "$OVR" "$EDGE" <<'PY'
import sys
path, edge = sys.argv[1], sys.argv[2]
lines = open(path).read().split("\n")
out = []
for line in lines:
    out.append(line)
    if line == "      novixa-edge: {}":
        out.append(f"      {edge}: {{}}")
    if line == "    external: true" and out[-2] == "  novixa-edge:":
        out += [f"  {edge}:", "    external: true"]
text = "\n".join(out)
assert text.count(edge) == 2, "override.yml has an unexpected shape; edit it by hand"
open(path, "w").write(text)
PY
  elif [ ! -f "$OVR" ]; then
    cat > "$OVR" <<EOF
services:
  nginx-mailcow:
    networks:
      $EDGE: {}

networks:
  $EDGE:
    external: true
EOF
  else
    die "$OVR has an unexpected shape — add the $EDGE network to nginx-mailcow by hand"
  fi
  (cd "$MAILCOW" && docker compose config -q) || die "override.yml no longer validates — restore the .bak copy"
fi

# --------------------------------------------- nginx site + certificate
log "nginx site files"
install -d -m 750 -o "$USER_NAME" -g root "$NGINX_CONF_DIR/$SITE"
for f in origin.pem origin.key; do
  if [ ! -s "$NGINX_CONF_DIR/$SITE/$f" ]; then
    [ -s "$NGINX_CONF_DIR/novixa/$f" ] || die "no Cloudflare Origin cert at $NGINX_CONF_DIR/novixa/$f to copy"
    install -m 600 -o "$USER_NAME" -g "$USER_NAME" "$NGINX_CONF_DIR/novixa/$f" "$NGINX_CONF_DIR/$SITE/$f"
  fi
done
# deploy.sh (running as $USER_NAME) owns and rewrites this one file only.
[ -f "$NGINX_CONF_DIR/$SITE.conf" ] || : > "$NGINX_CONF_DIR/$SITE.conf"
chown "$USER_NAME:root" "$NGINX_CONF_DIR/$SITE.conf"
chmod 640 "$NGINX_CONF_DIR/$SITE.conf"

log "done — $USER_NAME can now deploy into $APP_DIR"
