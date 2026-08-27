# Novixa Aqar — Deployment Environment Inventory

**Phase:** Deployment Intelligence, Phase 1 (repository inventory only — no Laravel Cloud application created, no infrastructure changed).
**Purpose:** a single authoritative technical reference answering "what exactly does Aqar need to run correctly outside my laptop?" — for use in the next phase, when Laravel Cloud is actually configured.
**Method:** direct inspection of the actual repository (`composer.json`/`.lock`, `package.json`, every `config/*.php`, `.env.example`, `Dockerfile`, `docker-compose.yml`, `.github/workflows/`, and a repository-wide search for every `env()` call, not just `.env.example`), plus non-destructive local command runs. Every fact below is either **verified** (a command was run, or code was read directly) or explicitly marked **TO VERIFY IN NEXT PHASE** (a genuinely Laravel-Cloud-specific unknown this phase cannot answer).

---

## 1. Application Identity

| Fact | Value |
|---|---|
| Company | Novixa |
| Product | Aqar / Novixa Aqar (نوفيكسا عقار) |
| Repository | `Novixa-dev/novixa-aqar` |
| GitHub URL | `https://github.com/Novixa-dev/novixa-aqar` |
| Default branch | `main` |
| Local checked-out branch | `feature/aqar-productization` (identical commit to `main` — see §2) |
| Current commit (both branches) | `9c702f2a1f1a856ca476f8c9b93237f272790a74` |
| Deployment target (this effort) | Laravel Cloud |
| Current deployment stage | Not yet deployed anywhere beyond local dev — this document is preparation for the first Laravel Cloud environment (staging/demo) |

**Readiness status — unchanged from the last verified assessment, not re-evaluated in this phase:**

```
READY FOR DEMO / STAGING
```
```
NOT YET PRODUCTION READY
```

This status comes from `docs/AQAR_PROGRESS.md` and `docs/REPOSITORY_RELEASE_AUDIT.md` (live-browser-verified core workflows; production readiness pending the environment/security checklist in `docs/PRODUCTION-DEPLOYMENT.md`). This document does not change or re-verify that status — it is application-behavior intelligence for *deploying* the already-assessed application, which is a distinct concern from *whether that application's features work correctly*.

---

## 2. Git Inventory (as of this phase)

```
git status                  → clean (no uncommitted changes)
git branch --show-current   → feature/aqar-productization
git rev-parse HEAD          → 9c702f2a1f1a856ca476f8c9b93237f272790a74
```

| Ref | Points to | Notes |
|---|---|---|
| `main` (local) | `9c702f2` | |
| `main` (`origin/main`) | `9c702f2` | **Confirmed identical to local `main`** via `git fetch` + `git rev-parse` comparison — no drift |
| `feature/aqar-productization` | `9c702f2` | Same commit as `main` |
| `archive/pre-cleanup-full-history` | (local only) | Pre-git-history-rebuild safety net |
| `archive/upstream-liberu-main` | (local only) | Original upstream Liberu history, preserved |
| `archive/upstream-worktree-agent` | (local only) | Leftover accidental branch, preserved |
| Remote branches beyond `main` | 10× `origin/dependabot/*` | GitHub-platform-generated (Dependabot auto-opened these against `.github/dependabot.yml`), **not application code**, not relevant to deployment |
| Tags | none, local or remote | |
| Remotes | `origin` → `https://github.com/Novixa-dev/novixa-aqar.git` only | |

**The three `archive/*` branches exist locally only and must remain local-only** — they carry the original upstream Liberu Real Estate git history/authorship and were deliberately never pushed. Nothing in this phase changes that.

No `git push` of any kind was performed in this phase.

---

## 3. PHP / Laravel Version Inventory

**Declared requirement** (`composer.json`): `"php": "^8.4|^8.5"`

**Actually installed locally** (verified: `php -v`):
```
PHP 8.4.16 (cli) (built: Dec 17 2025) — Zend Engine v4.4.16, OPcache enabled
```

**Composer:** `2.9.3` (verified: `composer --version`)

**Exact resolved package versions** (from `composer.lock`, not just the `^` range in `composer.json`):

| Package | Resolved version |
|---|---|
| `laravel/framework` | `v13.24.0` |
| `laravel/octane` | `v2.18.0` |
| `laravel/horizon` | `v5.48.2` |
| `laravel/reverb` | `v1.11.0` |
| `laravel/sanctum` | `v4.3.3` |
| `laravel/jetstream` | `v5.5.3` |
| `livewire/livewire` | `v4.3.5` |
| `filament/filament` | `v5.7.6` |
| `spatie/laravel-permission` | `6.25.0` |
| `spatie/laravel-medialibrary` | `11.23.4` |
| `spiral/roadrunner-http` | `v3.6.0` |
| `stripe/stripe-php` | `v20.3.1` |
| `docusign/esign-client` | `^8.0` (see `composer.json`) |

**`composer check-platform-reqs`** (verified, run against the actual local `vendor/` dir):

All extensions **success** except:
```
ext-pcntl   → missing (laravel/horizon requires it)
ext-posix   → missing (laravel/horizon requires it)
```

**This is expected and not a bug** — `pcntl`/`posix` are POSIX-only PHP extensions, unavailable on Windows (this local dev machine), which is why local Horizon usage is limited. **Laravel Cloud runs Linux**, where both extensions are standard — this gap should not exist on Cloud, but **verify Laravel Cloud's PHP runtime image includes `pcntl` and `posix` before relying on Horizon there** (flagged in §26).

- **Octane:** used, `OCTANE_SERVER=roadrunner` (config default, `config/octane.php`). The composer dependency is `spiral/roadrunner-http`, **not** the Swoole PHP extension — `.env.example` has an explicit comment warning that `OCTANE_SERVER=swoole` would fatally error since Swoole isn't installed. **This matters for Laravel Cloud**: confirm which Octane server Laravel Cloud's PHP runtime actually supports/expects (§26).
- **Horizon:** installed and configured (`config/horizon.php` exists), but `QUEUE_CONNECTION=sync` by default (§11) — Horizon only matters if the queue driver is switched to `redis`.
- **Reverb:** installed (`config/reverb.php` exists) but **not actually wired to the frontend** — see §13.
- **Livewire:** `v4.3.5`, used extensively (public site interactivity).
- **Filament:** `v5.7.6`, 9 panels (Admin/Staff/App/Tenant/Landlord/Buyer/Seller/Contractor/Agent).

---

## 4. Node / Frontend Version Inventory

**Locally installed** (verified): `node -v` → `v24.15.0`; `npm -v` → `11.12.1`

**No `engines` field in `package.json`** — the repository itself does not declare a required Node version anywhere. Whatever Node version is actually installed locally (v24.15.0) is the *de facto* version this was last built/tested against, not a stated contract. **Laravel Cloud's default Node version should be confirmed compatible with Vite 8 / this dependency set** (§26).

| Fact | Value |
|---|---|
| Frontend build tool | Vite `^8.0.14` (`vite.config.js`) |
| Frontend framework | Alpine.js `^3.14.1` + Livewire (no React/Vue) |
| CSS framework | Tailwind CSS `^4.1.17` (`@tailwindcss/vite` plugin) |
| Package manager | npm (`package-lock.json`, lockfileVersion 3) |
| Build command | `npm run build` → `vite build` |
| Dev command | `npm run dev` → `vite` |
| Build-time env vars | **None required.** `vite.config.js` does not read any custom `import.meta.env`/`VITE_*` variable — it only configures the Laravel plugin (input entrypoints, HMR refresh paths) and a static-copy plugin for `resources/images/*`. `.env.example` declares `VITE_PUSHER_*` vars, but nothing in `resources/js/` actually consumes them (see §13) |
| Output directory | `public/build/` (tracked in git in this repository — see note below) |

**`npm run build` — verified this phase:**
```
✓ 110 modules transformed
public/build/assets/theme-R53IERnP.css   624.73 kB │ gzip: 64.78 kB
public/build/assets/app-*.js           1,311.02 kB │ gzip: 364.00 kB
✓ built in 22.82s
```
Succeeded cleanly. One pre-existing, unrelated advisory: some chunks exceed 500kB after minification (a code-splitting suggestion, not an error).

**Note:** `public/build/` is currently a *tracked* directory in this repository (committed build output), which is unusual for a project also building via CI/CD or a Cloud platform. **Confirm in the next phase whether Laravel Cloud runs its own `npm run build` during deployment** (in which case the tracked `public/build/` becomes redundant/stale-risk) **or expects pre-built assets already in the repo** (in which case the committed build must always be kept in sync, which is a manual-discipline risk). See Risk Register §24.

---

## 5. Database Inventory

**Application default** (`config/database.php`): `'default' => env('DB_CONNECTION', 'mysql')` — **MySQL**, confirmed as the actually-used driver throughout this project's development and QA history (`docs/AQAR_PROGRESS.md`, `docs/REPOSITORY_RELEASE_AUDIT.md`).

**Local dev database, verified live this phase** (`php artisan db:show`):
```
MySQL 8.4.3
Database: liberu_real_estate   (local dev DB name — cosmetic legacy naming, already
                                 documented as a known non-blocking item; .env.example's
                                 own default is DB_DATABASE=aqar)
Tables: 361 (across all schemas on this shared local MySQL server —
             this server also hosts unrelated local projects, e.g. a
             "bindowal_academy_db" schema. Aqar's own tables are a subset.)
```

**Test database** (`phpunit.xml`): SQLite, in-memory —
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```
This is why the test suite is fully isolated from any real database and safe to run anywhere, but it also means **MySQL-specific SQL syntax used anywhere in a migration will silently never be tested** unless the test suite is actually run (this exact failure mode occurred and was fixed in a prior session — `docs/AQAR_TASKS.md` P15-8 — a raw `ALTER TABLE ... MODIFY` broke on SQLite despite being valid MySQL).

**Postgres:** `pdo_pgsql`/`pgsql` PHP extensions are installed in the Docker image (§18) "just in case," but **nothing in `config/database.php` or the codebase actually uses a `pgsql` connection** — Postgres support exists at the infrastructure/extension level only, unused.

**Redis:** connection details are configured (`REDIS_HOST`/`REDIS_PORT`/`REDIS_PASSWORD` in `.env.example`) but **Redis is not the default driver for cache, session, or queue** (see §5 defaults below and §17). It becomes required only if those drivers are explicitly switched to `redis`.

**Migrations:** standard Laravel migrations, no unusual database-specific features detected (no raw stored procedures, no custom SQL functions). One historical gotcha already fixed: migrations must use portable `Schema::table()->change()`, not raw MySQL-only `ALTER ... MODIFY`, because of the SQLite test environment (§ above).

**Seeding:** see §22 (Demo Data Inventory) for full detail. **`DatabaseSeeder`'s 16-seeder chain does not, as a whole, refuse to run in production** — only 2 of the 16 seeders (`UserSeeder`, `DemoWorkflowSeeder`) individually self-check `app()->environment('production')`. Flagged as a risk in §24.

**Recommendation for the Laravel Cloud database:** **MySQL 8.x**, matching what this application has actually been developed and tested against end-to-end (the live-verified QA history in `docs/AQAR_PROGRESS.md` is MySQL-based; the SQLite test path is deliberately isolated to `phpunit`/CI only and was never intended as an alternative *runtime* database). Do not switch to Postgres for the real deployment merely because the extension happens to be present in the Docker image — that would be an unverified, untested configuration change.

---

## 6. Environment Variable Inventory

This inventory covers **every** variable actually referenced via `env(...)` anywhere in `app/`, `config/`, `bootstrap/`, `routes/`, and `database/` (a full repository grep was performed, not just `.env.example`) — **over 150 distinct names**. To keep this usable, they're split into:

- **§6.1** — variables the application's *currently active* configuration and features genuinely depend on (the ones that matter for a working Aqar deployment)
- **§6.2** — variables that exist only because a third-party integration is *available but currently unconfigured/inactive* (all empty in `.env.example`, all fail gracefully — see per-integration notes)
- **§6.3** — variables that exist only because they're generic Laravel-framework config hooks for drivers this application does not currently use (e.g. Mailgun/Postmark/SQS/DynamoDB support that ships with every Laravel app by default, regardless of whether this project uses them)

No real secret values appear anywhere below. Secrets are marked `<SECRET — must be generated/provided manually>`.

### 6.1 Core — required for the application to function at all

| Variable | Required? | Secret? | Used by | `.env.example` default | Notes |
|---|---|---|---|---|---|
| `APP_NAME` | Yes | No | Framework, `MAIL_FROM_NAME` | `"Novixa Aqar"` | |
| `APP_ENV` | Yes | No | Framework (`local`/`staging`/`production`) | `local` | **Must be `staging` or `production` on Cloud** |
| `APP_KEY` | Yes | **Yes** | Encryption, session, all cookies | *(blank)* | `<SECRET — generate via `php artisan key:generate`>`. A **new** key must be generated per environment — never reuse the local dev key |
| `APP_DEBUG` | Yes | No | Error page verbosity | `true` | **Must be `false`** on any environment reachable by anyone but developers — leaking stack traces/`.env` values otherwise |
| `APP_URL` | Yes | No | Absolute URL generation, Sanctum stateful domains (self-adapting, see §16), Spatie Medialibrary URL base | `http://localhost` | **Must exactly match the real public host** (scheme+domain+port) — a documented, previously-live-reproduced bug class if it doesn't (broken property photo CSP) |
| `DB_CONNECTION` | Yes | No | `config/database.php` | `mysql` | Keep as `mysql` (§5) |
| `DB_HOST` | Yes | No | | `127.0.0.1` | Cloud-provided value |
| `DB_PORT` | Yes | No | | `3306` | Cloud-provided value |
| `DB_DATABASE` | Yes | No | | `aqar` | Cloud-provided value |
| `DB_USERNAME` | Yes | **Yes** | | `root` | Cloud-provided value |
| `DB_PASSWORD` | Yes | **Yes** | | *(blank)* | Cloud-provided value |
| `LOG_CHANNEL` | Yes | No | | `stack` | Fine as-is |
| `LOG_LEVEL` | Recommended | No | | `debug` | **Should be `warning`/`error` in production** to reduce noise/leakage risk |
| `SESSION_DRIVER` | Yes | No | | `cookie` | See §16 |
| `SESSION_LIFETIME` | Yes | No | | `120` | Minutes |
| `SESSION_DOMAIN` | Conditionally | No | | `null` | See §16 |
| `SESSION_SECURE_COOKIE` | Yes | No | | `false` | **Must be `true`** once served over HTTPS (Laravel Cloud will be HTTPS) |
| `SANCTUM_STATEFUL_DOMAINS` | Conditionally | No | Not in `.env.example` | *(auto-derives from `APP_URL`)* | See §16 — usually doesn't need manual setting |
| `FRONTEND_URL` | **Yes, once deployed** | No | `config/cors.php` `allowed_origins` | **Not in `.env.example` at all** — hardcoded fallback is `http://localhost` | **Real gap**: unless explicitly set, CORS will only ever allow `http://localhost` as an origin. Flagged in Risk Register §24 |
| `ASSET_URL` | Optional | No | `config/filesystems.php` public disk URL | *(unset → relative `/storage`)* | Only needed if media is ever served from a separate host/CDN — leave unset for standard same-origin |
| `FILESYSTEM_DISK` | Yes | No | | `local` | See §14 — **ephemeral storage risk** |
| `QUEUE_CONNECTION` | Yes | No | | `sync` | See §11 |
| `CACHE_DRIVER` | Yes | No | | `file` | See §17 |
| `BROADCAST_DRIVER` | Yes | No | | `log` | See §13 — not actually consumed by frontend |
| `OCTANE_SERVER` | Yes (if using Octane) | No | | `roadrunner` | **Not swoole** — no Swoole extension installed |
| `OCTANE_HTTPS` / `OCTANE_HOST` / `OCTANE_PORT` | Conditionally | No | | `false` / `0.0.0.0` / `8000` | Local/Docker-Octane specific; **TO VERIFY** whether Laravel Cloud's own runtime uses these at all or manages Octane itself |

### 6.1.1 Payments (Stripe) — real, active feature

| Variable | Required? | Secret? | Notes |
|---|---|---|---|
| `STRIPE_KEY` | Yes, for payments to work | **Yes** (publishable key — not fully secret, but not to be committed either) | |
| `STRIPE_SECRET` | Yes, for payments to work | **Yes** | |
| `STRIPE_WEBHOOK_SECRET` | Yes, for payment webhooks to verify | **Yes** | `.env.example`'s own comment: *"Required in production so Stripe payment webhooks verify and record payments."* |

### 6.1.2 Contact / mail routing

| Variable | Required? | Secret? | Notes |
|---|---|---|---|
| `CONTACT_INBOX_EMAIL` | Optional | No | Falls back to `MAIL_FROM_ADDRESS` (`config/services.php`) |
| `MAIL_MAILER` | Yes | No | `.env.example` default `smtp` |
| `MAIL_HOST` | Yes (once real mail is needed) | No | `.env.example` default is `mailpit` — **a local-dev-only mail-capture tool, not usable in staging** |
| `MAIL_PORT` / `MAIL_ENCRYPTION` | Yes | No | Provider-dependent |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | Provider-dependent | **Yes** | |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | Yes | No | `.env.example`: `info@darhadhramaut.ye` (demo-tenant address, not a real inbox) |

### 6.2 Third-party integrations — installed, configured, currently **inactive** (all keys blank)

Every one of these has a real `config/services.php` entry and real consuming code, but **zero has any value set anywhere in this repository** — each is an opt-in integration, not a deployment requirement. Portal-sync commands were specifically hardened (per a code comment in `routes/console.php`, referencing a previously-fixed `P0-5`) to no-op safely when their team-level settings/API key are absent, rather than erroring.

| Integration | Variables | Required for staging demo? |
|---|---|---|
| RightMove (portal sync) | `RIGHTMOVE_BASE_URI`, `RIGHTMOVE_API_KEY` | No |
| OnTheMarket (portal sync) | `ONTHEMARKET_BASE_URI`, `ONTHEMARKET_API_KEY`, `ONTHEMARKET_SYNC_FREQUENCY` | No |
| Zoopla (portal sync) | `ZOOPLA_BASE_URI`, `ZOOPLA_API_KEY`, `ZOOPLA_SYNC_FREQUENCY` | No |
| Jupix (property data) | `JUPIX_API_KEY`, `JUPIX_BASE_URL` | No |
| DocuSign (e-signature) | `DOCUSIGN_ACCOUNT_ID`, `DOCUSIGN_INTEGRATION_KEY`, `DOCUSIGN_SECRET_KEY`, `DOCUSIGN_BASE_PATH`, `DOCUSIGN_USER_ID`, `DOCUSIGN_PRIVATE_KEY_PATH` | No |
| Generic digital signature | `DIGITAL_SIGNATURE_API_KEY`, `DIGITAL_SIGNATURE_ENDPOINT` | No |
| Let's Safe | `LETS_SAFE_API_KEY`, `LETS_SAFE_API_URL` | No |
| CRM sync | `CRM_API_KEY`, `CRM_ENDPOINT`, `CRM_SYNC_INTERVAL` | No |
| Sage Online (accounting) | `SAGE_ONLINE_API_KEY`, `SAGE_ONLINE_ENDPOINT` | No |
| Xero (accounting) | `XERO_API_KEY`, `XERO_ENDPOINT` | No |
| WalkScore | `WALKSCORE_API_KEY`, `WALKSCORE_BASE_URI` | No (walkability scores are seeded/static in the demo, not live-fetched, without this key) |
| Neighborhood data | `NEIGHBORHOOD_DATA_API_KEY`, `NEIGHBORHOOD_DATA_BASE_URI` | No — **`.env.example`'s own default URI is `https://api.example.com`, a non-functional placeholder**, confirming this was never pointed at a real provider |
| Socialstream OAuth (social login) | `GOOGLE_CLIENT_ID`/`SECRET`, `FACEBOOK_CLIENT_ID`, `GITHUB_CLIENT_ID`, `GITLAB_CLIENT_ID`, `BITBUCKET_CLIENT_ID`, `LINKEDIN_CLIENT_ID`/`LINKEDIN_OPENID_CLIENT_ID`, `TWITTER_CLIENT_ID`, `SLACK_CLIENT_ID` | No — each provider is independently optional; the app works with standard email/password auth regardless |

### 6.3 Generic Laravel framework hooks — not used by this app's current configuration

These exist because Laravel's default config files always define them, as alternate-driver hooks. **None require a value** unless the corresponding driver is deliberately selected (none currently are):

`LOG_DEPRECATIONS_CHANNEL`, `LOG_PAPERTRAIL_HANDLER`, `LOG_STDERR_FORMATTER`, `PAPERTRAIL_URL`, `PAPERTRAIL_PORT`, `MAILGUN_DOMAIN`/`SECRET`/`ENDPOINT`, `POSTMARK_TOKEN`, `MAIL_URL`, `MAIL_SENDMAIL_PATH`, `MAIL_EHLO_DOMAIN`, `MAIL_LOG_CHANNEL`, `AWS_ENDPOINT`, `AWS_URL`, `MEMCACHED_HOST`/`PORT`/`USERNAME`/`PASSWORD`/`PERSISTENT_ID`, `REDIS_CLIENT`/`CLUSTER`/`DB`/`CACHE_DB`/`PREFIX`/`URL`/`USERNAME`/`TIMEOUT`, `DYNAMODB_CACHE_TABLE`/`ENDPOINT`, `SQS_PREFIX`/`QUEUE`/`SUFFIX`, `DATABASE_URL` (Laravel's alternate single-string DB connection — **note for §26**: some PaaS platforms inject this instead of individual `DB_*` vars), `DB_SOCKET`, `DB_ENCRYPT`, `DB_TRUST_SERVER_CERTIFICATE`, `DB_FOREIGN_KEYS`, `MYSQL_ATTR_SSL_CA` (relevant if Laravel Cloud's managed MySQL enforces SSL — **TO VERIFY**), `CACHE_PREFIX`, `QUEUE_FAILED_DRIVER`, `SESSION_CONNECTION`, `SESSION_STORE`, `BCRYPT_ROUNDS`, `SANCTUM_TOKEN_PREFIX`, `SOCIALSTREAM_SHOW_PROVIDER_IDS`, `ABLY_KEY` (Laravel default broadcasting-driver stub, unused — this app uses Reverb, not Ably), `HORIZON_DOMAIN`/`NAME`/`PATH` (Horizon dashboard route customization — only matters if Horizon dashboard is exposed, see §11), `MODULES_CACHE`/`DEVELOPMENT`/`LOAD_COMPOSER` (the `app/Modules/` plugin system's own config, `docs/MODULAR_ARCHITECTURE.md` — genuinely used but has safe defaults), `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK` (defaults to `filesystems.default`, i.e. `local` — same ephemeral-storage caveat as §14), `REVERB_APP_ACCEPT_CLIENT_EVENTS_FROM`/`ACTIVITY_TIMEOUT`/`MAX_CONNECTIONS`/`MAX_MESSAGE_SIZE`/`PING_INTERVAL`/`RATE_LIMIT*`/`SCALING_*`/`PULSE_INGEST_INTERVAL`/`TELESCOPE_INGEST_INTERVAL`/`SERVER`/`MAX_REQUEST_SIZE` (fine-grained Reverb tuning — only relevant if Reverb is actually enabled, see §13).

---

## 7. Secret Generation Methods

| Secret | Generation method | Notes |
|---|---|---|
| `APP_KEY` | `php artisan key:generate` | Laravel-native, run once per environment. **Do not copy the local dev key to staging/production.** |
| `DB_PASSWORD` | Provided by Laravel Cloud's managed MySQL provisioning | External |
| `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` | Stripe Dashboard (test-mode keys for staging, live-mode for production) | External — do not generate |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | Whatever SMTP/mail provider is chosen (not yet chosen — `mailpit` is dev-only) | External |
| All §6.2 integration keys | Each third-party provider's own dashboard | External, and **not needed unless that specific integration is being enabled** |
| `DOCKERHUB_USERNAME` / `DOCKERHUB_TOKEN` | Docker Hub account settings (GitHub *Actions* secret, not an app `.env` var) | Only needed if the `.github/workflows/main.yml` Docker-push job is kept enabled |
| `CODECOV_TOKEN` | Codecov project settings (GitHub *Actions* secret) | Only needed if coverage upload is kept enabled; the workflow already sets `fail_ci_if_error: false` so its absence does not fail CI |

**No secrets of any kind were generated, copied, or committed during this phase.**

---

## 8. Laravel Cloud Requirements (based on actual application architecture)

| Capability | Classification | Basis |
|---|---|---|
| PHP application runtime | **REQUIRED** | Core Laravel app |
| MySQL database | **REQUIRED** | §5 — the only database driver this app has ever been run/tested against as a real runtime DB |
| Redis | **RECOMMENDED, not required for initial staging** | §11/§17 — nothing in the default config (`QUEUE_CONNECTION=sync`, `CACHE_DRIVER=file`) requires it; becomes required only if queue/cache/Horizon are switched to Redis-backed drivers later |
| Queue worker process | **OPTIONAL for initial staging, RECOMMENDED for full behavior** | §11 — `sync` works out of the box (jobs run inline); real background jobs (lease reminders, email campaigns, portal syncs, CRM sync) need a worker + non-`sync` driver to run asynchronously |
| Scheduler | **RECOMMENDED** | §12 — 12+ scheduled tasks exist (portal syncs, sitemap, lease reminders, neighborhood data refresh); none are required for a user to browse/use the demo, but the "full" product behavior needs `schedule:run` running every minute somewhere |
| Horizon | **OPTIONAL** | §11 — only meaningful once queue driver is `redis`; its dashboard is also currently unreachable in non-local environments by design (empty allow-list, §11) until explicitly configured |
| Reverb / broadcasting | **NOT REQUIRED** | §13 — package installed, one backend event class exists, but zero frontend consumer; `BROADCAST_DRIVER=log` by default |
| Frontend build (Node/Vite) | **REQUIRED at build time** | §4 — `public/build/` must exist and be current; **TO VERIFY** whether Cloud builds this itself or expects it pre-committed |
| Object/persistent file storage | **REQUIRED before real use** | §14 — local disk storage is not safely persistent across redeploys; property photos/documents currently write to local disk only |
| Custom domain + SSL | **REQUIRED for a demo people will actually visit**, but Cloud's own default subdomain is enough to start | Standard |
| Environment variable management (Cloud UI) | **REQUIRED** | To supply everything in §6 that Cloud doesn't auto-provide |
| GitHub-integrated deployment | **RECOMMENDED** | Repository is already on GitHub at the target org; simplest path |
| Health checks | **RECOMMENDED** | `Dockerfile`'s own healthcheck already targets `php artisan octane:status` — **TO VERIFY** whether Laravel Cloud uses a different health-check convention |
| Scaling / autoscaling | **UNKNOWN — NEEDS VERIFICATION** | No evidence in the repo either way; a demo/staging app has no known load requirement yet |
| Logs / monitoring | **RECOMMENDED** | `LOG_CHANNEL=stack` is Cloud-agnostic; **TO VERIFY** whether Cloud expects a specific log channel/driver for its own log viewer |

---

## 9. Queue / Horizon Inventory

- **Default driver:** `QUEUE_CONNECTION=sync` (`.env.example`) — jobs execute inline within the request, no worker process needed. `.env.example`'s own comment: *"sync/file are zero-dependency defaults so a fresh install works without Redis running - switch both to `redis` for any real deployment, since Horizon (installed) never runs jobs while `QUEUE_CONNECTION=sync`."*
- **Real jobs exist** (`app/Jobs/`): `CheckLeaseNotifications`, `CheckPropertyAlerts`, `LeaseRenewalReminder`, `SendEmailCampaign`, `SyncActivityToCrm`, `SyncJupixProperties`, `SyncLeadToCrm`, `SyncOnTheMarketProperties`, `SyncRightMoveProperties`, `SyncZooplaProperties` — 10 job classes, all background enrichment/sync/notification work, none blocking core browsing/CRUD.
- **`failed_jobs` table migration exists** (`2019_08_19_000000_create_failed_jobs_table.php`) — ready if `QUEUE_FAILED_DRIVER=database` is used.
- **Worker command** (already defined in `docker-compose.yml`'s `worker` service): `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`.
- **Horizon command** (referenced in `Dockerfile`'s supervisor config, toggled via `WITH_HORIZON`): standard `php artisan horizon`.
- **Horizon dashboard access is deny-all by default in non-local environments** — `app/Providers/HorizonServiceProvider.php`'s `viewHorizon` gate checks `in_array($email, [])`, an empty array. **Must be edited (add authorized emails) before anyone can view the Horizon dashboard in staging**, even after Horizon itself is running.
- **Recommendation:** `sync` is fine for an initial staging deployment where the goal is "can people click around the demo." Switch to `QUEUE_CONNECTION=database` (simplest, no Redis needed) or `redis` (needed if Horizon's dashboard/metrics are wanted) once background jobs need to run asynchronously — genuinely needed only for portal-sync/email-campaign/lease-reminder features, not for the core demo experience.

---

## 10. Scheduler Inventory

`routes/console.php` defines the following recurring tasks (verified by reading the file in full):

| Task | Frequency |
|---|---|
| `portal-integrations:sync` | Every 15 minutes |
| `rightmove:sync-properties --frequency=hourly` | Hourly |
| `rightmove:sync-properties --frequency=daily` | Daily |
| `onthemarket:sync-properties` (hourly/daily/weekly variants) | As named |
| `zoopla:sync-properties` (hourly/daily/weekly variants) | As named |
| `LeaseRenewalReminder` job | Daily |
| `sitemap:generate` | Daily |
| Email campaign dispatch (inline closure) | Every minute |
| Neighborhood data refresh (inline closure) | Daily |

All portal-sync tasks are safe no-ops when their respective team-level API keys/settings are absent (§6.2). None of these are required for a user to browse the demo; they exist for data freshness and background notifications.

**Recommendation:** requires `php artisan schedule:run` invoked every minute somewhere — either Laravel Cloud's own scheduler feature (**TO VERIFY** exact configuration mechanism, §26) or the `supercronic`-based cron already baked into `Dockerfile` (local Docker path only, toggled via `WITH_SCHEDULER`). Not blocking for an initial "can people see the demo" deployment.

---

## 11. Broadcasting / Reverb Inventory

- **Package installed:** `laravel/reverb ^1.0` (resolved `v1.11.0`), `config/reverb.php` and `config/broadcasting.php` both exist.
- **Backend usage:** exactly **one** event class implements `ShouldBroadcast` — `app/Events/BookingCreated.php`. `routes/channels.php` defines exactly one private channel (`App.Models.User.{id}`), Laravel's own default boilerplate channel.
- **Frontend usage: none.** A repository-wide search of `resources/js/` found no `laravel-echo`, `Pusher`, or `Echo` import/usage anywhere. `resources/js/app.js` only wires up Flowbite, Leaflet (maps), and a 3D model viewer — nothing subscribes to any broadcast channel.
- **Default driver:** `BROADCAST_DRIVER=log` (`.env.example`) — broadcast events are written to the log, not actually pushed anywhere.
- **Conclusion: package exists, one backend event is wired to broadcast, but the feature is not actually consumed by any UI.** This is a clear case of "installed ≠ required" per this task's own framing.
- **Required variables if ever enabled:** `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME`, plus the matching `VITE_PUSHER_*`/frontend Echo wiring, which does not currently exist and would need to be written.
- **Recommendation: do not enable Reverb for initial staging.** It adds a whole additional always-running process (§18's Dockerfile provisions it, toggled off by default via `WITH_REVERB=false`) for a feature with no live consumer.

---

## 12. Storage / File Upload Inventory

**This section identifies what would be lost on redeploy — the most consequential finding in this document for Laravel Cloud planning.**

`config/filesystems.php` disks:

| Disk | Driver | Path | Used for |
|---|---|---|---|
| `local` (default disk name, `FILESYSTEM_DISK=local`) | `local` | `storage_path('app')` | Compliance documents, document workflow entries (private downloads — `ComplianceDocumentController`, `DocumentWorkflowController`) |
| `public` | `local` | `storage_path('app/public')`, served via `storage:link` | **Property photos** (`Property::registerMediaCollections()`, explicitly `->useDisk('public')`), Staff-uploaded documents/images (`DocumentResource`, `ImageResource`), document templates |
| `s3` | `s3` | *(configured, unused)* | Available drop-in via `AWS_*` env vars, **not currently activated anywhere** |

**Every upload path in this application currently writes to local disk** (verified via `grep` for `useDisk(`/`Storage::disk(`/`->disk(` across `app/`) — no code path currently uses the `s3` disk despite it being fully configured and ready.

**Consequence for Laravel Cloud:** if Laravel Cloud's filesystem is ephemeral per-deployment/per-container (common for PaaS platforms) rather than a persistent mounted volume, **every property photo, staff-uploaded document, and image uploaded after the first deploy will be lost on the next deploy or container restart.** This is not hypothetical — it is the direct, mechanical consequence of the `local` driver's semantics combined with an ephemeral filesystem.

**Two paths forward (decision for the next phase, not made here):**
1. Confirm Laravel Cloud provides genuinely persistent storage for the `local`/`public` disks (**TO VERIFY**, §26), or
2. Switch `FILESYSTEM_DISK`/the `public` disk to `s3` (already wired, just needs `AWS_*` credentials + `AWS_BUCKET` + updating `Property::registerMediaCollections()`'s `useDisk()` calls) before any real demo data with real uploaded photos is expected to survive.

**Livewire's own temporary-upload disk** (`config/livewire.php`, `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK`) defaults to `filesystems.default` (i.e. `local`) — same caveat, though these are genuinely temporary files by design (deleted after processing), lower risk than the permanent media above.

---

## 13. Email Inventory

- **Transport:** `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit` (`.env.example`) — **`mailpit` is a local-dev-only mail-capture container** (also defined as a service in `docker-compose.yml`), not a real mail provider. **A real SMTP/API mail provider must be chosen and configured before staging can send real email.**
- **Real mail usage exists:** 1 Mailable (`ContactMessageReceived`) + 11 Notification classes (`AppointmentCreated`, `AppointmentReminder`, `AutomationNotification`, `BookingNotification`, `LeaseAgreementReady`, `LeaseNotification`, `LeaseRenewalReminder`, `MaintenanceRequestSubmitted`, `NewPropertyMatches`, `OnTheMarketSyncStatus`, `PropertyAlert`) — covers contact form confirmation, booking/appointment confirmations, lease lifecycle, maintenance requests, and portal-sync status.
- **Is email required for the staging demo to be usable?** No — none of these are in the critical path of "browse properties, log in, see panels." They enhance the experience (confirmations, reminders) but their absence/failure doesn't block core navigation.
- **Safe temporary setup for initial staging:** either (a) point `MAIL_MAILER=log` (mail written to the log instead of sent — zero external dependency, matches the existing `BROADCAST_DRIVER=log` pattern already used elsewheré in this codebase for a similar "safe no-op" default), or (b) configure a real low-effort transactional provider (Mailgun/Postmark/SES — all three already have first-class `config/services.php` entries ready, just need credentials) if demo email flows need to actually be seen working.
- **No real credentials appear anywhere in this repository or this document.**

---

## 14. Session / Auth / Sanctum Inventory

| Setting | Local value | What changes once a real Cloud URL exists |
|---|---|---|
| `SESSION_DRIVER` | `cookie` | Fine to keep — no server-side session store needed unless switched to `database`/`redis` |
| `SESSION_DOMAIN` | `null` | Should generally stay `null` unless multiple subdomains need to share a session |
| `SESSION_SECURE_COOKIE` | `false` | **Must become `true`** once served over HTTPS (Laravel Cloud will terminate HTTPS) — an insecure-cookie-over-HTTPS mismatch breaks login |
| `APP_URL` | `http://localhost` | **Must become the real Cloud/custom domain URL, exactly** (§6.1) |
| `SANCTUM_STATEFUL_DOMAINS` | *(unset)* | **Self-adapting** — `config/sanctum.php`'s default expression appends `Sanctum::currentApplicationUrlWithPort()`, i.e. it automatically includes whatever `APP_URL` resolves to. Usually does not need manual setting once `APP_URL` is correct. |
| `FRONTEND_URL` (drives `config/cors.php`'s `allowed_origins`) | *(unset, hardcoded fallback `http://localhost`)* | **Does NOT auto-adapt** — must be explicitly set to the real Cloud/custom domain once it exists, or all cross-origin API/CSRF-cookie requests from a real frontend will be rejected. **This is a genuine `.env.example` gap** (not documented there at all) — flagged in Risk Register §24. |

**Domain progression to track across phases:** `localhost` (local dev) → Laravel Cloud's auto-assigned staging subdomain (**TO VERIFY the exact format**, §26) → eventual custom domain. Every row above that says "must match" needs updating at each transition.

---

## 15. Cache / Redis Inventory

- **Default cache driver:** `CACHE_DRIVER=file` (`.env.example`). Works with zero external dependency.
- **Redis connection details are pre-configured** (`REDIS_HOST=127.0.0.1`, `REDIS_PORT=6379`, `REDIS_PASSWORD=null`) but **Redis is not the active driver for cache, session, or queue** in the shipped defaults.
- **Installed package ≠ required service, again:** the `redis` PHP extension is installed in the Docker image (§18) and `docker-compose.yml` defines a `redis` service as a hard `depends_on` for the local Docker Compose `app` service — but the application's own PHP-level configuration does not require it at the current driver defaults. The Docker Compose file over-provisions Redis structurally (every service depends on it) even though the code doesn't need it unless drivers are switched.
- **Recommendation:** `file` cache driver is adequate for an initial single-instance staging deployment. Move to Redis if/when Laravel Cloud horizontal-scales the app across multiple instances (file cache wouldn't be shared between instances) or when Horizon is activated (§11).

---

## 16. Docker / Local Deployment Inventory

**This is explicitly the *local Docker Compose* architecture, not necessarily the Laravel Cloud target architecture** (per this phase's own instruction — Laravel Cloud has its own native build/deploy pipeline, generally independent of a repo's own `Dockerfile`).

**`Dockerfile`** (multi-stage, based on the third-party `exaco/laravel-octane-dockerfile` template — MIT-licensed, already correctly attributed via its own `LABEL org.opencontainers.image.source`):
- Stage 1 (`composer-deps`): `php:8.5-cli-alpine`, installs `intl sockets zip`, runs `composer install --no-dev` (with `--ignore-platform-req=ext-pcntl` and `ext-exif`, matching the Windows-vs-Linux extension gap noted in §3).
- Stage 2 (main): installs a full extension set — `bz2 pcntl mbstring bcmath sockets pgsql pdo_pgsql opcache exif pdo_mysql zip intl gd redis pcntl igbinary` — notably more than the app strictly uses (Postgres extensions present but unused, per §5).
- Runs under Supervisor, managing up to 5 toggleable processes: RoadRunner (Octane, always on), Horizon (`WITH_HORIZON`, off by default), Reverb (`WITH_REVERB`, off by default), Scheduler via `supercronic` (`WITH_SCHEDULER`, off by default at the `Dockerfile` `ENV` level but **on by default in `docker-compose.yml`**), and a generic queue worker.
- **Health check:** `php artisan octane:status`.
- Exposes ports `8000` (HTTP/Octane) and `8080` (Reverb's default WebSocket port).
- Copies `.env.example` → `.env` at build time (a placeholder; real deployments are expected to inject real env vars at runtime, not bake them into the image).

**`docker-compose.yml`** (local multi-container orchestration): `app` (main web, scheduler on/Horizon+Reverb off by default), `reverb` (separate Compose profile, off unless explicitly enabled), `worker` (separate Compose profile, off unless explicitly enabled, runs `queue:work`), `mysql:8.0`, `redis:7-alpine`, `mailpit` (dev-only SMTP capture). Named volumes (`storage`, `logs`, `mysql`, `redis`) provide persistence **only within this local Docker setup** — this mechanism does not exist on Laravel Cloud and must not be assumed to carry over.

**Do not reintroduce Kubernetes** — none exists in this repository (`k8s/` was already removed in a prior cleanup pass; `docs/PRODUCTION-DEPLOYMENT.md` documents Docker Compose as the sole supported local/self-hosted deployment path).

---

## 17. GitHub Actions / CI Inventory

Four workflows in `.github/workflows/`, all triggered on push/PR to `main`:

| Workflow | Purpose | Secrets required |
|---|---|---|
| `install.yml` | Full install smoke test (fresh MySQL service container, migrate, seed, build) | None |
| `tests.yml` | `php artisan test --coverage-clover=coverage.xml`, uploads to Codecov | `CODECOV_TOKEN` (upload step has `fail_ci_if_error: false`, so its absence does **not** fail the workflow) |
| `main.yml` | Runs the test suite, then (on push only) builds and pushes a Docker image to Docker Hub as `novixa/aqar` | `DOCKERHUB_USERNAME`, `DOCKERHUB_TOKEN` (the Docker-push job *will* fail without these — the preceding test job is unaffected) |
| `security.yml` | PHP security-advisory checker, PHP Copy/Paste Detector, PHP Insights (scheduled weekly + on push/PR) | None |

**All four already run automatically on every push to `main`** since `main` is already published — this has been true since the repository was first pushed, not something this phase changes.

**Are `DOCKERHUB_USERNAME`/`DOCKERHUB_TOKEN`/`CODECOV_TOKEN` needed immediately for Laravel Cloud staging?** **No.** Laravel Cloud deployment (the actual goal) does not depend on this repository's own GitHub Actions workflows succeeding — Cloud's own deployment mechanism is independent of whether the `main.yml` Docker-Hub-push job is red or green. These three secrets only matter if the Docker Hub image publishing / Codecov coverage-tracking side-features are wanted; they can be added later, or that job/step can be disabled, without blocking Cloud deployment.

**No secrets were created or invented in this phase.**

---

## 18. Build / Deploy Command Inventory

Laravel-native commands this application actually needs (not Laravel-Cloud-specific syntax, which is verified separately in the next phase):

| Purpose | Command |
|---|---|
| Install PHP dependencies | `composer install --no-dev --optimize-autoloader` |
| Install + build frontend | `npm ci && npm run build` |
| Generate app key (once per environment) | `php artisan key:generate` |
| Run migrations (safe, additive) | `php artisan migrate --force` |
| Run migrations + seed demo data (first deploy only — see §19) | `php artisan migrate --seed --force` |
| Cache config/routes/views for production | `php artisan config:cache && php artisan route:cache && php artisan view:cache` |
| Link public storage | `php artisan storage:link` |
| Queue worker (if async queue is enabled) | `php artisan queue:work --sleep=3 --tries=3 --max-time=3600` |
| Horizon (if enabled) | `php artisan horizon` |
| Scheduler tick (needs to run every minute) | `php artisan schedule:run` |
| Reverb server (if enabled) | `php artisan reverb:start` |
| Health check | `php artisan octane:status` (what `Dockerfile` uses) — **TO VERIFY** if Cloud expects a different endpoint/mechanism |
| Application server | `php artisan octane:start --server=roadrunner` |

**Laravel Cloud configuration itself** (how these commands map to Cloud's deployment hooks, build packs, process types, etc.) is explicitly **out of scope for this phase** — see §26.

---

## 19. Staging Database Safety

This project has an established, documented distinction (`docs/PRODUCTION-DEPLOYMENT.md`, `docs/RELEASE-CHECKLIST.md`) between destructive and non-destructive database operations:

| Command | Safe for... | Not safe for... |
|---|---|---|
| `php artisan migrate` | Any environment, any time (additive-only, assuming no destructive migration is ever written) | N/A |
| `php artisan migrate --seed` | **First deploy to a genuinely empty staging database only** | Re-running against a staging database that already has real/demo data people have interacted with — most seeders (§5, §22) are not idempotent and would create duplicates |
| `php artisan migrate:fresh --seed` | **Local development and demo-reset only** | **Never against staging or production once real data exists — this drops every table first.** `docs/PRODUCTION-DEPLOYMENT.md` already explicitly warns against this exact command in a production context. |

**Recommendation for the first Laravel Cloud staging deployment:** `php artisan migrate --seed` exactly once, against the freshly-provisioned empty Cloud database. Every subsequent deploy should run only `php artisan migrate` (no `--seed`, no `--fresh`) unless a deliberate, explicit demo-data reset is intended and understood to discard anything since the last seed.

---

## 20. Demo Data Inventory

`DatabaseSeeder`'s chain (`database/seeders/DatabaseSeeder.php`), in order: `SiteSettingsSeeder`, `PermissionsSeeder`, `RolesSeeder`, `DefaultTeamSeeder`, `UserSeeder`, `PropertyCategorySeeder`, `NeighborhoodSeeder`, `PropertySeeder`, `LeadAndBuyerSeeder`, `AppointmentTypeSeeder`, `VirtualTourAppointmentTypeSeeder`, `MenuSeeder`, `DocumentTemplateSeeder`, `ComponentSettingsSeeder`, `NewsSeeder`, `DemoWorkflowSeeder`.

| Demo data | Seeder | Notes |
|---|---|---|
| Demo tenant/agency | `DefaultTeamSeeder` | "Dar Hadhramaut Real Estate \| دار حضرموت العقارية" — the project's deliberate, consistent Yemen-appropriate demo brand (not a stray leftover — see `docs/REPOSITORY_RELEASE_AUDIT.md` §3.4) |
| Demo user accounts, one per role | `UserSeeder` | 8 roles (super_admin/staff/agent/buyer/seller/landlord/tenant/contractor), all `@darhadhramaut.ye` addresses. **Full list and login credentials: `docs/TEST_USERS.md`.** Stored in that local/demo-only documentation — do not publish real production credentials anywhere. |
| Properties | `PropertySeeder` | Includes bundled demo gallery photos (written to the `public` disk — see §12's persistence caveat) |
| Leads/buyers, leases, bookings, payments, maintenance requests | `LeadAndBuyerSeeder`, `DemoWorkflowSeeder` | Realistic-volume demo workflow data |
| News articles | `NewsSeeder` | |
| Site settings, menu, categories, neighborhoods, appointment types, document templates | Various | Supporting reference data |

**Production safety — only partial:** of these 16 seeders, only **`UserSeeder`** and **`DemoWorkflowSeeder`** contain an explicit `if (app()->environment('production')) { ... }` guard (verified via `grep`). The other 14 have no such check. **Running the full `php artisan db:seed` against a real production database would still seed demo properties/leads/news/etc.** — flagged in the Risk Register (§21).

**Appropriateness for public staging:** the seeded content (Yemen-flavored demo agency, realistic-but-fictional properties/leads/leases) is appropriate for a public-facing staging/demo environment as-is — this was already the subject of a prior audit pass confirming no leftover placeholder/lorem-ipsum content (`docs/AQAR_PROGRESS.md`).

---

## 21. Deployment Risk Register

| Risk | Severity | Evidence | Impact | Recommendation | Status |
|---|---|---|---|---|---|
| Uploaded media/documents on ephemeral local disk | **HIGH** | §12 — every upload path uses the `local` driver; `s3` disk configured but unused | Property photos, staff documents lost on redeploy/restart if Cloud filesystem isn't genuinely persistent | Confirm Cloud storage persistence (§26) or switch to the already-configured `s3` disk before real content accumulates | Open |
| `FRONTEND_URL` / CORS hardcoded fallback | **MEDIUM** | §6.1, §14 — `config/cors.php` defaults `allowed_origins` to `http://localhost`; `FRONTEND_URL` absent from `.env.example` | Cross-origin API/CSRF requests from the real deployed frontend rejected until manually set | Set `FRONTEND_URL` explicitly once the Cloud URL exists; add to `.env.example` | Open |
| `MAIL_HOST=mailpit` is a dev-only placeholder | **MEDIUM** | §13 | Any real email send fails until a real provider is configured | Configure a real provider or set `MAIL_MAILER=log` as a safe interim default | Open |
| Only 2/16 seeders self-check `environment('production')` | **MEDIUM** | §20 | Accidental `db:seed` against a real production DB would seed demo content | Add production guards to the remaining seeders before any production database exists, or rely on process discipline (never seed production) | Open |
| Horizon dashboard deny-all by default | **LOW** | §11 | Not a security bug (fails closed) but blocks intentional Horizon dashboard use until the gate's allow-list is edited | Add authorized emails to `HorizonServiceProvider::gate()` when Horizon dashboard access is actually wanted | Open |
| `ext-pcntl`/`ext-posix` missing locally (Windows) | **LOW** | §3 | Cannot fully exercise Horizon locally; should not affect Linux-based Cloud runtime | Confirm Cloud's PHP runtime includes both (§26) | Open — needs Cloud-side verification |
| `public/build/` is tracked in git | **LOW** | §4 | Risk of stale committed assets if Cloud doesn't rebuild, or redundant/conflicting rebuild if it does | Confirm Cloud's build behavior (§26); align on one source of truth | Open — needs Cloud-side verification |
| No `engines` field pins a Node version | **LOW** | §4 | Future contributors/CI could build with a different Node version than what's been tested (v24.15.0 locally) | Consider adding `"engines": {"node": ">=20"}` (informational, not enforced by npm by default) | Open, minor |
| `docker-compose.yml` hard-depends on Redis for every service | **LOW / informational** | §17 | Local Docker Compose only — does not reflect actual code-level requirements | No action needed; noted so it isn't mistaken for a Cloud requirement | Informational |
| `MYSQL_ATTR_SSL_CA` / TLS-to-DB unclear | **LOW — TO VERIFY** | §6.3 | If Laravel Cloud's managed MySQL enforces TLS, an unconfigured connection could fail | Confirm Cloud's MySQL connection requirements (§26) | Open — needs Cloud-side verification |

No hardcoded IP addresses were found outside of standard local-dev defaults (`127.0.0.1`, `0.0.0.0`) already covered above; no hardcoded production URLs, no development-only file paths outside of the expected `storage_path()`/`public_path()` helpers.

---

## 22. Staging Environment Variable Matrix

Practical, condensed matrix for the actual first Laravel Cloud staging environment. Full detail and rationale for every row is in §6.

| Variable | Local | Laravel Cloud Staging | Required Action | Secret? |
|---|---|---|---|---|
| `APP_NAME` | `Novixa Aqar` | `Novixa Aqar` | None | No |
| `APP_ENV` | `local` | `staging` | Set manually | No |
| `APP_DEBUG` | `true` | `false` | Set manually — **do not deploy with this `true`** | No |
| `APP_URL` | `http://localhost` | `<GENERATED BY LARAVEL CLOUD>` (or custom domain) | Set after app creation | No |
| `APP_KEY` | *(local key)* | *(new key)* | Generate via `php artisan key:generate` | **Yes** |
| `DB_CONNECTION` | `mysql` | `mysql` | None | No |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` | local values | `<GENERATED BY LARAVEL CLOUD>` | Cloud-provided | `DB_USERNAME` sensitive |
| `DB_PASSWORD` | *(local)* | `<GENERATED BY LARAVEL CLOUD>` | Cloud-provided | **Yes** |
| `SESSION_SECURE_COOKIE` | `false` | `true` | Set manually (HTTPS) | No |
| `FRONTEND_URL` | *(unset)* | Same as `APP_URL` | **Must set manually — not auto-derived** | No |
| `LOG_LEVEL` | `debug` | `warning` or `error` | Set manually (recommended) | No |
| `FILESYSTEM_DISK` | `local` | `local` (pending §12 decision) or `s3` | Decide + configure before real uploads accumulate | If `s3`: AWS keys **yes** |
| `QUEUE_CONNECTION` | `sync` | `sync` (initial) → `database`/`redis` later | Decide based on whether async jobs are wanted | No |
| `CACHE_DRIVER` | `file` | `file` (initial) | None initially | No |
| `MAIL_MAILER` / `MAIL_HOST` | `smtp` / `mailpit` | `log` (interim) or a real provider | Decide + configure | If real provider: **yes** |
| `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` | *(blank)* | Stripe test-mode keys | Provide manually from Stripe Dashboard | **Yes** |
| All §6.2 integration keys | *(blank)* | *(blank, unless that integration is wanted)* | None required | **Yes**, if ever set |

No future Cloud URL, database credential, or any other not-yet-existing value has been fabricated anywhere in this document.

---

## 23. Currently Verified Facts

**Verified previously (prior sessions, cited from existing project documentation — not re-run in this phase):**
- `php artisan migrate:fresh --seed --force` — completed successfully (exit 0) against the local MySQL dev database, all migrations and seeders `DONE` (`docs/REPOSITORY_RELEASE_AUDIT.md` §12).
- GitHub repository `Novixa-dev/novixa-aqar` exists and `main` is published (`docs/REPOSITORY_RELEASE_AUDIT.md` §14.5).
- Repository cleanup (git history, branding, dev-workspace artifacts) completed; no `archive/*` branches published (`docs/REPOSITORY_RELEASE_AUDIT.md`).

**Verified during this phase (commands actually run, this session):**
- `git status` → clean; `git fetch` + `git rev-parse` → local `main` and `origin/main` are identical (`9c702f2`).
- `php -v` → PHP 8.4.16; `composer --version` → 2.9.3; `composer check-platform-reqs` → all pass except `ext-pcntl`/`ext-posix` (expected on Windows, §3).
- `node -v` → v24.15.0; `npm -v` → 11.12.1.
- `npm run build` → succeeded, 22.82s, no errors.
- `php artisan db:show` → MySQL 8.4.3 reachable, database present, 361 tables (across all local schemas on this shared dev MySQL instance).
- `php artisan test --no-ansi` → **553 passed / 0 failed / 32 skipped** (2130 assertions, 1306.72s). Exact match to the established baseline (`docs/AQAR_TASKS.md`, `docs/REPOSITORY_RELEASE_AUDIT.md`) — confirmed fresh, not merely cited.

---

## 24. Laravel Cloud Facts To Verify Before Deployment

Genuinely Cloud-specific unknowns — **not answered from memory in this document**, to be verified against current official Laravel Cloud documentation/dashboard in the next phase:

- Exact current PHP runtime options/images available, and whether they include `ext-pcntl`/`ext-posix` (needed for Horizon) by default.
- Exact current Node/build configuration: does Cloud run `npm run build` itself during deployment, or does it expect `public/build/` pre-committed? (§4, §21 risk)
- Exact current managed-MySQL provisioning workflow: version offered, whether TLS/SSL is enforced (relevant to `MYSQL_ATTR_SSL_CA`), and whether credentials are supplied as discrete `DB_*` vars or a single `DATABASE_URL`.
- Current Redis availability/pricing/provisioning workflow, if/when needed.
- **Current storage mechanism and its persistence guarantees** — the single most consequential unknown in this document (§12/§21).
- Current scheduler configuration mechanism (native Cloud feature vs. requiring a cron-like setup).
- Current worker/Horizon configuration mechanism (dedicated Cloud process type vs. manual setup).
- Current Reverb/WebSocket support and configuration, if it's ever activated later.
- Current deployment-hook behavior (pre/post-deploy command execution — where would `php artisan migrate --force` actually run?).
- Current environment-variable-management UI/workflow.
- Current domain/SSL configuration workflow (for both the auto-assigned staging subdomain and an eventual custom domain).
- Current GitHub integration workflow (auto-deploy on push to `main`? a specific deploy branch convention?).
- Current sleep/scaling behavior for a low-traffic staging/demo app (cold-start behavior matters for a demo someone might click into after it's been idle).
- Current health-check convention (does Cloud expect a specific endpoint, or does it respect `Dockerfile`-style health checks at all, given Cloud likely does not use this repo's `Dockerfile` directly)?

---

## 25. Files Changed In This Phase

- **Created:** `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` (this file).
- **No other files were created, modified, or deleted.**
- **No commands with side effects were run** beyond `npm run build` (regenerates `public/build/`, already a tracked/expected artifact of running that command — see §4's note on this being flagged for Cloud-behavior verification rather than treated as a problem) and `php artisan test` (fully isolated, in-memory SQLite, zero side effects on any real data).
- **Nothing was pushed to GitHub in this phase** (explicitly out of scope, per the task).
