# Aqar Production Deployment Guide

Every environment-specific value the application actually reads, extracted from the real `.env.example`/`config/*.php`/code — not a generic Laravel checklist. Nothing below is speculative: each entry was verified against the code path that consumes it. No real secrets appear in this repo or this document; every "production value" is a description of what to put there, never an actual credential.

Companion documents: `docs/RELEASE-CHECKLIST.md` (local QA vs. deploy-day QA), `docs/AQAR-DEMO-READINESS-AUDIT.md` (the audit that drove this pass), `docs/AQAR-FINAL-RELEASE-QA.md` (the verification record for this pass).

---

## 1. Core application identity

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `APP_NAME` | `"Novixa Aqar"` | Keep as-is, or the real product name if it changes | Optional | `.env` | Used in `MAIL_FROM_NAME`, page titles | Check page `<title>` / email "From" name |
| `APP_ENV` | `local` | `production` | **Required** | `.env` | Gates seeders (`UserSeeder`/`DemoWorkflowSeeder` refuse to run if `production`), affects Laravel's default error verbosity | `php artisan env` should print `production` |
| `APP_DEBUG` | `true` | `false` | **Required, critical** | `.env` | `true` exposes full stack traces (file paths, query bindings, internal class names) to any visitor who triggers an error | Force a 500 (e.g. malformed request) and confirm a generic Laravel error page, not a stack trace |
| `APP_KEY` | generated locally via `php artisan key:generate` | **A fresh key, generated on/for the production environment** — never reuse the dev key | **Required, critical** | `.env` (never commit) | Encrypts sessions, `LeaseAgreement.content` (`Crypt::encryptString`), signed URLs. Losing/rotating it invalidates all active sessions and makes existing encrypted lease-agreement content unreadable | `php artisan key:generate --show` locally to preview, then set the real one on the server and never reuse it elsewhere |
| `APP_URL` | `http://127.0.0.1:8000` (must match whatever host actually serves `php artisan serve`/Octane locally) | The exact production origin, e.g. `https://aqar.example.com` — scheme, host, and (if non-standard) port must match precisely | **Required, critical** | `.env` | Used for canonical/OG tags, sitemap URLs, and (previously) media URLs. As of this pass, media URLs are relative (`ASSET_URL`, see below) specifically so an `APP_URL` mismatch can no longer break images — but canonical/OG/sitemap correctness still depends on it being exactly right | Load the homepage, View Source, confirm `<link rel="canonical">` and `og:url` show the real domain, not `localhost` |
| `ASSET_URL` | unset (defaults to relative `/storage`) | Leave unset unless media is served from a separate asset host/CDN/subdomain | Optional | `.env` | `config/filesystems.php`'s `public` disk now defaults to a **relative** `/storage` URL (this pass's fix for the CSP/APP_URL image-loading bug) — only set this if you deliberately move media off the app's own origin | Load a property page, confirm photos render (Network tab: 200 on `/storage/...`, no CSP console errors) |

---

## 2. Database

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `DB_CONNECTION` | `mysql` | `mysql` | Required | `.env` | Only MySQL is exercised/supported by this codebase's migrations | — |
| `DB_HOST` / `DB_PORT` | `127.0.0.1` / `3306` | Your managed MySQL host/port | Required | `.env` | — | `php artisan migrate:status` connects without error |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | `aqar` / `root` / (blank) | Real production database name/credentials — least-privilege user, not root | **Required, critical** | `.env` (never commit) | — | Same as above; confirm the app user does NOT have `DROP`/`GRANT` privileges beyond what migrations need |
| `docker-compose.yml`'s Mysql service defaults | `DB_DATABASE:-aqar` / `DB_USERNAME:-aqar` | Explicitly set `DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` in your deploy env — don't rely on the compose file's fallback defaults in production | Required if using `docker-compose.yml` | shell env / `.env` passed to compose | The compose fallbacks are dev-convenience defaults, not production credentials | `docker compose config` and confirm the resolved values are your real ones, not the fallbacks |

**Migrations & seeding strategy:**
- Run `php artisan migrate --force` on deploy (the `--force` flag is required in a non-interactive/production context). Never run `migrate:fresh` against a production database with real data — it drops every table.
- **Do not run `UserSeeder`, `DemoWorkflowSeeder`, or `DatabaseSeeder` in production.** Both seeders explicitly self-check `app()->environment('production')` and refuse to run — this is enforced in code, not just a convention, but don't rely on that as your only safeguard: never invoke `db:seed`/`migrate:fresh --seed` as part of a production deploy pipeline at all. These seeders exist purely to bootstrap the local/demo dataset.
- **PHP `memory_limit` for seeding** (demo/staging environments only, per the above — never production): `PropertySeeder`'s bundled-photo media-conversion step needs more than PHP's stock 128M CLI default. `DatabaseSeeder::run()` now raises this to 512M for the duration of the seed itself (`ini_set`), so this is handled automatically for any environment that *does* run the demo seeders (a staging/demo box, not production) — no manual action needed unless your hosting's `ini_set` is disabled by policy (rare `disable_functions` hardening), in which case set `memory_limit = 512M` in that environment's own `php.ini` for the CLI SAPI.

---

## 3. Mail

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `MAIL_MAILER` | `smtp` (points at Mailpit) | `smtp` (a real provider), `ses`, `resend`, etc. | Required if any email is part of the deploy (contact form, notifications, password reset) | `.env` | — | Trigger the contact form; confirm `ContactMessageReceived` arrives at the configured inbox |
| `MAIL_HOST` / `MAIL_PORT` / `MAIL_USERNAME` / `MAIL_PASSWORD` / `MAIL_ENCRYPTION` | Mailpit (`mailpit:1025`, no auth) | Your real SMTP provider's values | Required (same condition) | `.env` (never commit password) | — | Same as above |
| `MAIL_FROM_ADDRESS` | `info@darhadhramaut.ye` | The real sending address for this domain (must be a domain you control — check SPF/DKIM if deliverability matters) | Required | `.env` | — | Check the "From" header on a received email |
| `CONTACT_INBOX_EMAIL` | unset (falls back to `MAIL_FROM_ADDRESS`) | The real staff inbox that should receive contact-form submissions, if different from `MAIL_FROM_ADDRESS` | Optional (has a safe fallback) | `.env` | `ContactController` reads this specifically for routing contact-form notifications | Submit the public contact form, confirm it lands in the intended inbox |

---

## 4. Payments (Stripe)

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `STRIPE_KEY` / `STRIPE_SECRET` | blank | Real Stripe publishable/secret keys (live mode, not test mode, for a real launch) | **Only if a payment flow is part of the demo/launch script** | `.env` (never commit secret key) | Confirmed via code: no payment UI is force-shown if these are unset — the app doesn't crash without them, a payment flow simply can't be exercised | Attempt a real test transaction (Stripe test mode first, then live) |
| `STRIPE_WEBHOOK_SECRET` | blank | Real webhook signing secret from the Stripe dashboard for this endpoint | Required **only if** Stripe keys above are configured | `.env` (never commit) | Payment webhooks won't verify/record without it | Trigger a Stripe test webhook, confirm it's accepted (not 400) |

---

## 5. Queue, cache, and Horizon

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `QUEUE_CONNECTION` | `sync` (zero-dependency; jobs run inline, immediately, no worker needed) | `redis` (recommended) if any queued/async behavior matters at production scale (portal sync jobs, notification dispatch under load) | Recommended, not strictly required — `sync` still "works," just blocks the request thread | `.env` | Horizon (installed) never actually runs jobs while this is `sync` | `php artisan horizon:status` (if switched to redis) shows running |
| `CACHE_DRIVER` | `file` | `redis` (recommended) for a multi-server/production deployment | Recommended | `.env` | `file` cache doesn't share across multiple app servers; also `Property::boot()` calls `Cache::flush()` on every create/update/delete, which is fine on either driver but scales better with Redis | — |
| `REDIS_HOST` / `REDIS_PORT` / `REDIS_PASSWORD` | `127.0.0.1` / `6379` / `null` | Your managed Redis instance | Required **if** `QUEUE_CONNECTION`/`CACHE_DRIVER` are switched to `redis` | `.env` | — | `redis-cli -h <host> ping` from the app server |
| Horizon process | not running locally by default | A supervised, always-running process (`php artisan horizon`) — the Docker image's `WITH_HORIZON` container mode handles this if using the provided `Dockerfile`/`docker-compose.yml` | Required **if** `QUEUE_CONNECTION=redis` | Process supervisor (systemd/Supervisor/the Docker `horizon` container mode) | Queued jobs silently never run without a worker process | `php artisan horizon:status`; check the Horizon dashboard for active supervisors |

---

## 6. Real-time (Reverb / broadcasting)

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `BROADCAST_DRIVER` | `log` (no-op — broadcasts are written to the log, not actually pushed anywhere) | `reverb`, **only if** a real-time feature is part of the launch scope | Optional | `.env` | No live/real-time feature was confirmed reachable in this audit pass — verify your specific deploy's feature scope before enabling | — |
| `REVERB_APP_ID` / `REVERB_APP_KEY` / `REVERB_APP_SECRET` | blank | Generate real values (`php artisan reverb:install` or manually) | Required **if** `BROADCAST_DRIVER=reverb` | `.env` (never commit secret) | — | `php artisan reverb:start` runs without error; a WebSocket client can connect |
| `REVERB_HOST` / `REVERB_PORT` / `REVERB_SCHEME` | `localhost` / `8080` / `http` | Your real Reverb-serving host/port, `https` if behind TLS | Required (same condition) | `.env` | — | — |
| Reverb process | not running locally by default | A supervised, always-running process — the Docker image's `WITH_REVERB` container mode handles this | Required (same condition) | Process supervisor / Docker `reverb` container mode | — | — |

---

## 7. Storage / media

| Variable | Local dev value | Production value | Required? | Configure in | Why | Verify after deploy |
|---|---|---|---|---|---|---|
| `FILESYSTEM_DISK` | `local` (backed by `storage/app`, served publicly via the `public` disk + `storage:link` symlink) | `local` is fine for a single-server deploy; switch to `s3` only if you need multi-server/CDN-backed media | Optional | `.env` | No code path requires S3 — confirmed via the audit | — |
| `storage:link` | run once during local setup | **Must run once per deployment** (`php artisan storage:link`, or let the Docker `start-container` script do it automatically — it already calls this on every container start) | **Required, critical** | Deploy script / container entrypoint | Without the symlink, every property photo 404s even though the file exists on disk | `Test-Path public/storage` (or `ls -la public/storage` on Linux) resolves to a real target; load a property page and confirm a photo actually renders |
| `AWS_*` (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`) | blank, unused | Only fill in if you deliberately switch `FILESYSTEM_DISK` to `s3` | Optional | `.env` (never commit secret) | — | — |

---

## 8. Domain, DNS, TLS

| Item | Required? | Why | Verify after deploy |
|---|---|---|---|
| DNS A/AAAA (or CNAME) record pointing the production domain at the app server/load balancer | Required | Standard | `dig`/`nslookup` the domain resolves correctly |
| TLS certificate (Let's Encrypt or equivalent) covering the production domain | Required | `SecurityHeaders` middleware sends `Strict-Transport-Security` only when `$request->secure()` — confirm the app is actually reached over HTTPS in production so this header (and cookie `Secure` flags) take effect | Browser padlock; `curl -I https://<domain>` shows a valid cert chain |
| `SESSION_SECURE_COOKIE` | Should be `true` in production once served over HTTPS (currently `false` in `.env.example`, a correct zero-dependency local default) | Prevents the session cookie from ever being sent over plain HTTP | Inspect the `Set-Cookie` header for `Secure` |

---

## 9. Build & process management

| Step | Command | Notes |
|---|---|---|
| Install PHP dependencies | `composer install --no-dev --optimize-autoloader` | `--no-dev` excludes `laravel/pint`, `laravel/sail`, `mockery`, etc. — dev-only tooling |
| Install & build frontend assets | `npm ci && npm run build` | Produces `public/build/*`; confirmed this pass to succeed cleanly (one advisory: the main JS bundle is ~1.3MB uncompressed / 364KB gzipped, over Vite's 500KB warning threshold — not a blocker, but worth code-splitting in a future pass) |
| Generate app key (first deploy only) | `php artisan key:generate --force` | Only if `APP_KEY` isn't already set for this environment |
| Run migrations | `php artisan migrate --force` | See Section 2 — never `migrate:fresh` against real data |
| Cache config/routes/views | `php artisan optimize` (or `config:cache`, `route:cache`, `view:cache` individually) | The provided `Dockerfile`'s `start-container` script already runs `php artisan optimize` on every container start |
| Storage symlink | `php artisan storage:link` | See Section 7; idempotent, safe to run every deploy |
| Scheduler | A cron entry (or the Docker image's `scheduler` container mode / `supercronic` config) running `php artisan schedule:run` every minute | Required for the daily sitemap regeneration (`sitemap:generate`) and any portal-sync commands to actually fire on schedule |
| Queue workers | See Section 5 (Horizon) | Only required if `QUEUE_CONNECTION=redis` |
| PHP runtime | Octane via RoadRunner (`OCTANE_SERVER=roadrunner`) — confirmed this is what's actually installed (`composer.json`'s `spiral/roadrunner-*`); do **not** set `OCTANE_SERVER=swoole`, the extension isn't installed | `php artisan octane:start` (or the Docker image's `http` container mode, which wraps this via Supervisor) |
| PHP version | `^8.4\|^8.5` per `composer.json`; the provided `Dockerfile` defaults to PHP 8.5 | Confirm the actual production host's PHP version before first deploy — this exact class of mismatch (dev machine resolving to 8.4.0 against Composer's `>=8.4.1` floor) has already caused one real local blocker in this project's history |

---

## 10. Post-deploy smoke checklist

Run through this on the actual production URL after every deploy, not just locally:

1. Homepage loads, correct brand name/title in both locales, property photos actually render (not broken image icons).
2. Log in as an admin-equivalent account; confirm the Filament admin panel loads with zero console errors.
3. Submit the public contact form; confirm the notification email arrives.
4. `View Source` on the homepage: canonical/OG URLs show the real production domain.
5. `/sitemap.xml` and `/robots.txt` both serve, and the sitemap's URLs use the real domain.
6. Confirm `APP_DEBUG` is off by deliberately triggering an error (e.g. a malformed request) and confirming a generic error page.
7. Confirm HTTPS is enforced/working and the padlock is valid.
