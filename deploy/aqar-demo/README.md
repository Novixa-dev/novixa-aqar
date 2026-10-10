# Deployment — Novixa Aqar → aqar-demo.novixa.dev (Novixa VPS)

Pushing to **`main`** deploys <https://aqar-demo.novixa.dev>. This is
separate from the Dexal (cPanel) pipeline in [`../README.md`](../README.md),
which is unchanged.

```
main ──push──▶ GitHub Actions ──▶ ghcr.io/novixa-dev/novixa-aqar:sha-… ──ssh──▶ 161.97.75.64
```

Workflow: `.github/workflows/deploy-aqar-demo.yml`

| Job | Does | Gates deploy |
|---|---|---|
| `test` | `php artisan test` (PHP 8.5, SQLite) | yes |
| `build` | `composer install` (for Filament CSS) → `npm ci && npm run build` → Docker image pushed to GHCR | yes |
| `deploy` | uploads `compose.yml`, `app.env`, `nginx.conf`, `deploy.sh`, runs `deploy.sh`, checks the public URL | — |

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
| `/opt/aqar-demo/app.env` | non-secret config, from this repo on every deploy |
| `/opt/aqar-demo/secrets.env` | `APP_KEY`, `DB_PASSWORD` — generated once, never leaves the server |
| `/opt/aqar-demo/db.env` | MySQL passwords — generated once |
| `/opt/aqar-demo/.seeded` | present once demo data was seeded (delete it to seed again) |
| volumes `aqar-demo-db-data`, `aqar-demo-storage`, `aqar-demo-logs` | survive every deploy |

`deploy.sh` order: migrate (one-off container) → first deploy seeds →
start the new release **beside** the old one → wait for 200 on `/` and the
panel login pages → install nginx site (`nginx -t` first, restored on
failure) → switch traffic → verify through nginx → remove the old release.
Any failure before the switch leaves the old release serving; a failure
after it re-attaches the old release. Schema is never rolled back
automatically.

## One-time setup

1. **Server** (as root, once):

   ```bash
   scp deploy/aqar-demo/server-setup.sh root@161.97.75.64:/root/
   ssh root@161.97.75.64 'bash /root/server-setup.sh "<CI public key line>"'
   ```

   Creates the key-only `aqar` user, `/opt/aqar-demo` + secrets, the
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

# reset the demo data from scratch (destroys everything in the demo DB)
ssh root@161.97.75.64 'cd /opt/aqar-demo && docker compose down && docker volume rm aqar-demo-db-data && rm .seeded'
# …then re-run the workflow
```
