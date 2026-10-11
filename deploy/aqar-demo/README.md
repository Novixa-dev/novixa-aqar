# Deployment — Novixa Aqar demos on the Novixa VPS

| Branch | Site | Workflow | Site config |
|---|---|---|---|
| `main` | <https://aqar-demo.novixa.dev> | `deploy-aqar-demo.yml` | `deploy/aqar-demo/app.env` |
| `ui-improves` | <https://aqar-demo2.novixa.dev> | `deploy-aqar-demo2.yml` | `deploy/aqar-demo2/app.env` |

Each site is fully separate on the server (`/opt/<site>`, its own MySQL,
volumes, secrets and containers); they share only the scripts in this
directory, which derive every name from the site directory. This is
separate from the Dexal (cPanel) pipeline in [`../README.md`](../README.md),
which is unchanged.

**Adding another site** (e.g. `aqar-demo3`): add its DNS record in
Cloudflare, run `server-setup.sh aqar-demo3` on the server, copy
`deploy/aqar-demo2/app.env` and the workflow with the names changed.

```
push ──▶ GitHub Actions: test ─▶ build image ─▶ docker save | ssh docker load ─▶ deploy.sh on 161.97.75.64
```

Both workflows have the same jobs:

| Job | Does | Gates deploy |
|---|---|---|
| `test` | `php artisan test` (PHP 8.5, SQLite) | yes |
| `deploy` | checks the secrets and SSH first → `composer install` (for Filament CSS) → `npm ci && npm run build` → builds the image → streams it to the server (`docker save \| ssh docker load`, no registry) → uploads `compose.yml`, `app.env`, `nginx.conf`, `deploy.sh` → runs `deploy.sh` → checks the public URL | — |

No container registry is involved, so there are no package permissions to
configure and no registry credentials on the server.

`workflow_dispatch` has a `skip_tests` toggle for emergencies only.

## How it runs on the server

The VPS is also the mail server (mailcow), whose nginx owns ports 80/443.
The demo plugs into it exactly like novixa.dev does:

```
Cloudflare (proxied) ─▶ mailcow nginx :443 ── aqar-edge network ─▶ aqar-demo-app-<ts> :8000 (Octane)
                         data/conf/nginx/aqar-demo.conf                 │ aqar-demo-internal (no internet)
                                                                        ▼
                                                                  aqar-demo-db (MySQL 8)
```

| Path on the server | What |
|---|---|
| `/opt/<site>/app.env` | non-secret config, from this repo on every deploy |
| `/opt/<site>/secrets.env` | `APP_KEY`, `DB_PASSWORD` — generated once, never leaves the server |
| `/opt/<site>/db.env` | MySQL passwords — generated once |
| `/opt/<site>/.seeded` | demo-data version currently seeded (see below) |
| `/opt/<site>/.db-name` | database the live release uses (`aqar`, then `aqar_v<version>`) |
| volumes `<site>-db-data`, `<site>-storage`, `<site>-logs` | survive every deploy |

`deploy.sh` order: migrate (one-off container) → first deploy seeds →
start the new release **beside** the old one → wait for 200 on `/` and the
panel login pages → install nginx site (`nginx -t` first, restored on
failure) → switch traffic → verify through nginx → remove the old release.
Any failure before the switch leaves the old release serving; a failure
after it re-attaches the old release. Schema is never rolled back
automatically.

## Resetting the demo data (zero downtime)

Each site's `app.env` carries `DEMO_SEED_VERSION`. When the deployed value
differs from the one recorded in `/opt/<site>/.seeded`, `deploy.sh`:

1. creates a fresh database `aqar_v<version>` beside the live one,
2. migrates and seeds it with a one-off container,
3. starts the new release against it and switches traffic as usual,
4. drops the old database only after the switch succeeded.

The live site keeps serving the old data throughout. Bump the value
whenever the seeders change in a way the demo should show (version 2 moved
the demo logins to `@novixa.dev`). Visitors' changes on the demo are reset.

## One-time setup

1. **Server** (as root, once):

   ```bash
   scp deploy/aqar-demo/server-setup.sh root@161.97.75.64:/root/
   ssh root@161.97.75.64 'bash /root/server-setup.sh aqar-demo  "<CI public key line>"'
   ssh root@161.97.75.64 'bash /root/server-setup.sh aqar-demo2 "<CI public key line>"'
   ```

   Creates the key-only `aqar` user, `/opt/<site>` + secrets, the
   `aqar-edge` network (joined to mailcow's nginx live — no mail restart —
   and persisted in mailcow's `docker-compose.override.yml`), and a copy of
   the `*.novixa.dev` Cloudflare Origin certificate for this site.

2. **GitHub → Settings → Secrets and variables → Actions**:

   | Secret | Value |
   |---|---|
   | `AQAR_DEMO_SSH_KEY` | the CI private key (whole file, including `BEGIN`/`END`) |
   | `AQAR_DEMO_KNOWN_HOSTS` | `161.97.75.64 ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIPlYRXIwubAfrs8ZNnuWAh1qSkbHJvmedqjHap1Ox18+` |

3. **Cloudflare**: `aqar-demo` A record → `161.97.75.64`, proxied (already
   done). SSL/TLS mode **Full (strict)** works with the origin certificate.

## Operating it

```bash
# logs of the live release
ssh root@161.97.75.64 'docker logs -f $(docker ps -qf label=novixa.aqar-demo.role=app)'

# reset the demo data: bump DEMO_SEED_VERSION in deploy/<site>/app.env and push
```
