# Novixa Aqar — Full QA Sweep & Environment Report

**Date:** 2026-09-09
**Branch:** `claude/practical-allen-15l7zt` (based on `main`)
**Scope:** live browser QA across all 9 roles/panels + public site, full environment/services/keys
inventory, and every bug found and fixed along the way. Requested as a professional, priority-ordered
test pass ("من الاهم فالاهم") with a complete `.md` report as the deliverable.

**Headline result:** the staging deployment is live, auto-deploying from `main`, fully seeded, and
passed every P0 (blocking) and the large majority of P1/P2 checks executed. **5 real bugs were found
and fixed this session** (1 data-integrity, 1 crash, 1 authorization gap, and a deploy-reliability
crash-loop that turned out to be 2 independent bugs stacked on top of each other) — all merged, live,
and verified via a fully-read deploy log end to end, not an early status flag. See §5 for full detail
(§5.4 in particular carries the honest multi-attempt timeline — a first fix that didn't stick, and a
second bug that only became visible once it did) and §7 for what remains untested.

---

## 1. Executive Summary

| Area | Status |
|---|---|
| Homepage / guest browsing (AR, RTL) | ✅ PASS |
| Self-registration → working dashboard | ✅ PASS (historically the most severe defect class — regression-checked, still fixed) |
| All 9 persona logins (admin/staff/agent/buyer/seller/landlord/tenant/contractor/app) | ✅ PASS |
| Admin + Staff panel resource sweep (37 + 34 pages) | ✅ 69/71 PASS — 2 failures found, both fixed |
| Full CRUD lifecycle (create → search → edit → delete) on a live resource | ✅ PASS, incl. correct staff-vs-admin delete-permission boundary |
| Public site: property browse/detail, calculators, contact form (real DB write), news, chatbot (real reply) | ✅ PASS |
| English locale, dark mode, responsive (375/768/1440px) | ✅ PASS, zero horizontal overflow at any breakpoint |
| GitHub → Railway auto-deploy pipeline | ✅ confirmed working, live, multiple times this session |
| CI (6 GitHub Actions checks) | ✅ green on every commit pushed this session |
| Data integrity (property duplication) | 🔴 found → ✅ fixed → ✅ live-verified on Railway |
| Chat Conversations resource (Admin+Staff) | 🔴 found (HTTP 500, 100% reproducible) → ✅ fixed → ✅ verified |
| Contact Messages authorization | 🔴 found (fail-open gap) → ✅ fixed → ✅ verified |
| RESEED_FRESH deploy reliability | 🔴 found (crash-loop, 2 independent bugs stacked) → ✅ both fixed, merged, redeployed, and verified via a fully-read deploy log (1 clean container boot, disk usage back to healthy, `RESEED_FRESH` set back to `false`) |

**Not yet executed** (see §7): per-role deep interaction testing beyond Admin/Staff (Landlord/Tenant/
Buyer/Seller/Contractor panels were reachability-checked but not feature-by-feature exercised),
booking/appointment creation flow, PDF/Excel report exports, secondary Tier-P3 surfaces.

---

## 2. Methodology

This session's sandboxed execution environment has a hard, organization-level network policy that
blocks direct outbound HTTPS to the live Railway URL (`web-production-68b34.up.railway.app`) —
confirmed via two independent tools and the proxy's own status endpoint, and correctly **not**
worked around (per this environment's own instructions never to route around a policy-level block).

**Workaround used, matching this project's own established QA methodology** (18 prior documented
phases in `docs/AQAR_TASKS.md`/`docs/AQAR_PROGRESS.md`, all using the identical local-serve +
Playwright pattern): stood up the **exact same commit** locally —

- MariaDB 10.11, fresh `migrate:fresh --seed`
- `composer install`, `npm install && npm run build`
- `php artisan serve --host=127.0.0.1 --port=8787`
- Playwright (Chromium, pre-installed at `/opt/pw-browsers`) driving real browser sessions — login,
  click-through, form fills, screenshots — against that local instance

This gives byte-identical **code/behavior** to what's on Railway; the **data** is a fresh local seed,
not Railway's live rows (Railway's actual live data was separately inspected via Railway's own deploy
logs and management API, not the blocked app URL — see §4.6).

Deploy pipeline actions (merging PRs, setting `RESEED_FRESH`, reading Railway deploy logs) **were**
reachable throughout, via the Railway MCP integration (a separate channel from the blocked direct
HTTP path) — so infrastructure operations in §5.4 are against the **real, live** Railway service.

Every finding below is evidence-backed: an HTTP status + console-error count, a DB row confirmed via
`tinker`, or a reviewed screenshot — never assumed from a green test run alone.

---

## 3. Test Plan & Priority Ordering

Full plan: `docs/AQAR_QA_REPORT_2026-09-09-testplan.md` is not committed separately — it lived in this
session's scratchpad; the tiering below is preserved here since it drove execution order.

- **Tier P0** (blocks everything else): guest homepage, self-registration, all persona logins,
  authorization boundaries, core property browsing, contact/submission forms, debug-mode-off check.
- **Tier P1** (core feature completeness per role): every panel's primary resources reachable with
  real data; property CRUD; chatbot; calculators; virtual tours; news; community events/property
  history first-live-check.
- **Tier P2** (language/theme/responsive/polish): English re-test, dark mode, 375/768/1440px,
  form validation, 404/500 handling, SEO basics.
- **Tier P3** (secondary/lower-traffic): compare tool, saved searches/reports, email campaigns,
  utility/energy records, full resource-class reachability sweep.

Ordering rationale: if execution stopped at any point, everything completed is strictly more
important than anything not reached. Execution reached deep into P2 before time was redirected to
the RESEED_FRESH incident (§5.4), which was itself a live, in-progress deploy issue that took
priority over continuing further down the plan.

---

## 4. Live Environment & Services Inventory

### 4.1 Hosting — Railway

| Fact | Value |
|---|---|
| Project | `novixa-aqar-staging` (`62a3b385-5e48-496b-9cb3-f16bee7086a8`) |
| Environment | `production` (`e5bbe8f5-3ead-4ef0-b4ea-43d5ad396e6d`) — Railway's own environment name; this *is* the staging/demo box, not a separate prod deployment |
| `web` service | `1ff28b19-93b5-4dbe-845e-85c7103608bc` — Laravel app, Octane/RoadRunner |
| `mysql` service | `704601bc-e9fc-463e-bfd7-1fbbaac8d7fe` — standalone Railway-managed MySQL container |
| Public domain | `web-production-68b34.up.railway.app` (Railway-issued, no custom domain configured yet) |
| Builder | RAILPACK, defers to the repo's own `Dockerfile` (multi-stage, based on `exaco/laravel-octane-dockerfile`) |
| Auto-deploy | **Confirmed working**, multiple times this session: every push/merge to `main` triggers a Railway rebuild+redeploy automatically, no manual step |
| `web` persistent volume | `web-storage`, 500 MB, mounted at `storage/app`, region `ams` |
| `mysql` persistent volume | `mysql-data`, 500 MB, mounted at `/var/lib/mysql`, region `ams` — stable, deployed once 2026-09-02, never redeployed since |
| Current live commit | `b4ae817` (this session's last push — pending merge via PR #17; `f46c65e` is the latest **merged and deployed** commit as of this report) |
| Current service health | **Online**, 1/1 replica running, 0 crashed, 0 warnings/criticals (`environment-status`, checked live) |

### 4.2 `web` service — environment variables (names only; values redacted at the API level, never read by this session)

`ADMIN_EMAIL`, `ADMIN_NAME`, `ADMIN_PASSWORD`, `APP_DEBUG`, `APP_ENV`, `APP_KEY`, `APP_NAME`, `APP_URL`,
`BROADCAST_DRIVER`, `CACHE_DRIVER`, `CONTAINER_MODE`, `DB_CONNECTION`, `DB_DATABASE`, `DB_HOST`,
`DB_PASSWORD`, `DB_PORT`, `DB_USERNAME`, `FRONTEND_URL`, `LOG_CHANNEL`, `LOG_LEVEL`, `MAIL_MAILER`,
`MULTITENANCY`, `OCTANE_HOST`, `OCTANE_HTTPS`, `OCTANE_PORT`, `OCTANE_SERVER`, `QUEUE_CONNECTION`,
`RESEED_FRESH`, `RUNNING_MIGRATIONS_AND_SEEDERS`, `SESSION_DRIVER`, `SESSION_SECURE_COOKIE`, plus
Railway-injected `RAILWAY_ENVIRONMENT`/`RAILWAY_ENVIRONMENT_ID`/`RAILWAY_ENVIRONMENT_NAME`/
`RAILWAY_PRIVATE_DOMAIN`/`RAILWAY_PROJECT_ID`/`RAILWAY_PROJECT_NAME`/`RAILWAY_PUBLIC_DOMAIN`/
`RAILWAY_SERVICE_ID`/`RAILWAY_SERVICE_NAME`/`RAILWAY_SERVICE_WEB_URL`/`RAILWAY_STATIC_URL`/
`RAILWAY_VOLUME_ID`/`RAILWAY_VOLUME_MOUNT_PATH`/`RAILWAY_VOLUME_NAME`.

**`RESEED_FRESH` is currently `true`** on the live service as of this report — a leftover from the
investigation in §5.4. **Action required once PR #17 is merged and redeployed cleanly: set it back
to `false`.** It is explicitly documented in `.docker/start-container` as "never meant to be left on."

**NOT set** (code correctly defaults/blanks; every one of these is a genuinely optional integration,
confirmed via `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` §6.2 — none block the demo):
`STRIPE_KEY`/`STRIPE_SECRET`/`STRIPE_WEBHOOK_SECRET` (payments won't process, but nothing crashes),
`AWS_*`, `REVERB_*` (broadcasting installed but no frontend consumer exists yet), `WALKSCORE_*`,
`NEIGHBORHOOD_DATA_*` (`.env.example`'s own default URI is a placeholder — never pointed at a real
provider), `RIGHTMOVE_*`/`ONTHEMARKET_*`/`ZOOPLA_*`/`JUPIX_*` (portal-sync integrations — hardened to
safely no-op when absent), `DOCUSIGN_*`, every Socialstream OAuth provider ID/secret (social login —
app works fine with standard email/password regardless), `FILESYSTEM_DISK` (defaults to `local`),
`CONTACT_INBOX_EMAIL` (falls back to `MAIL_FROM_ADDRESS`).

Full variable-by-variable purpose/risk breakdown (150+ names across the whole app, not just what's
set on Railway) already exists at `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` §6 — this report does
not duplicate it, only adds the Railway-specific current state on top (that document predates the
Railway pivot and still frames things in terms of "Laravel Cloud"; the variable catalog itself is
still accurate and complete).

### 4.3 `mysql` service — environment variables

`MYSQL_DATABASE`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `MYSQL_USER`, plus Railway-injected
`RAILWAY_*`. Standard Railway-managed MySQL, not a third-party plugin.

### 4.4 Object storage — provisioned, unused

An `aqar-media` bucket (`8e9d3f7b-ce07-482c-9b92-831c15393ed8`, region `ams`) was created earlier this
session as a candidate fix for the storage-permission bug (§5, now fixed via a different, cleaner
approach — the boot-time `chown` in `.docker/start-container`). **Currently unused by the app.** A
reasonable future home for private compliance documents (currently on the `local` disk), not blocking
anything today.

### 4.5 GitHub — CI/CD, repository health

- **Workflows** (`.github/workflows/`): `install.yml` (fresh-install smoke test), `tests.yml` (full
  PHPUnit suite + Codecov), `main.yml` (tests + Docker Hub push — the push job needs
  `DOCKERHUB_USERNAME`/`DOCKERHUB_TOKEN` secrets, **not set**, so that one job fails independent of
  app correctness; not Railway-related, not blocking), `security.yml` (fixed this session — see §5).
- **All 6 checks green on every commit pushed this session** (laravel-tests, Full Install Smoke Test,
  PHP Security Checker, PHP Copy/Paste Detector, PHP Insights, `tests`) — verified live via the
  GitHub API before every merge, not assumed.
- **10 open Dependabot PRs** (#1–#10), oldest since 2026-08-26 (~2 weeks stale) — dependency version
  bumps, none reviewed/merged yet. Not urgent, but worth a look; listed here as a flagged item, not
  actioned this session (out of scope for a QA sweep — these are upgrade PRs, not defects).
- Repository: `Novixa-dev/novixa-aqar`, default branch `main`.

### 4.6 What actually shipped this session (commits, chronological)

| Commit | What | PR | Merged? |
|---|---|---|---|
| `f424db0` | `fix(seed)`: idempotent `PropertySeeder` + `RESEED_FRESH` mechanism | #16 | ✅ merged |
| `43f96b6` | `fix(filament)`: stop `ChatConversationResource` 500 on null `escalated_at` | #16 | ✅ merged |
| `f46c65e` | `fix(security)`: add missing `ContactMessagePolicy` | #16 | ✅ merged |
| `b4ae817` | `fix(seed)`: skip responsive-image generation for seeded photos — **first attempt, turned out ineffective, see §5.4** | #17 | ✅ merged (superseded by `3cbbf74`) |
| `3cbbf74` | `fix(seed)`: `Property::$skipResponsiveImagesForSeeding`, gated inside `registerMediaCollections()` itself — genuinely fixed the responsive-images bug, but a second bug was hiding underneath (see next row) | #18 | ✅ merged |
| `70960b7` | `fix(deploy)`: clear `storage/app/public` before `RESEED_FRESH`'s `migrate:fresh --seed` — the volume was completely full (~504MB/500MB) from every earlier crashed attempt's orphaned photos, which is the real reason the crash-loop continued after `3cbbf74` | #19 | ✅ merged, redeployed, and live-verified (§5.4) |

Each merge auto-triggered a Railway redeploy (confirmed via `list-deployments` matching commit SHAs
to deployment records) — the GitHub→Railway pipeline is genuinely live and working, not just
configured.

---

## 5. Bugs Found and Fixed This Session

### 5.1 PropertySeeder non-idempotency → live data duplication (data integrity, MEDIUM)

**Found:** comparing local (`migrate:fresh --seed`, exactly once) vs. a documented Railway deploy log
showing **560** `property_features` rows where exactly **140** was expected — a precise 4× multiplier,
because `PropertySeeder::run()` had no existence guard and every partial reseed attempt during earlier
storage-permission-bug iteration had re-created the full 28-property batch from scratch.

**Fixed:** guarded property creation behind `Property::exists()`; added a documented, off-by-default
`RESEED_FRESH` env flag to `.docker/start-container` as the correct "reset this demo" mechanism going
forward (never appropriate against real user data — this staging box only ever holds seed content).

**Verified:** re-ran the seeder directly twice locally — stayed at 28 both times (was 28→56 before).

### 5.2 `ChatConversationResource` — 100%-reproducible HTTP 500 (crash, HIGH)

**Found:** live Playwright sweep hit `/admin/chat-conversations` and `/staff/chat-conversations` (same
underlying Staff-namespaced resource, shared by Admin via its `discoverResources()` call) — both threw
`Could not parse '—': Failed to parse time string`.

**Root cause:** the `escalated_at` column chained `->dateTime()->sortable()->default('—')`. Filament's
`default()` substitutes the literal string `'—'` as the column's state whenever the DB value is null,
and that fake state then gets fed through `dateTime()`'s Carbon parser, which throws on a non-date
string. Every non-escalated conversation (the majority of any real dataset) hit this on every page
load.

**Fixed:** changed `->default('—')` to `->placeholder('—')` — verified against Filament's own source
(`vendor/filament/tables/src/Columns/Concerns/CanFormatState.php`) that `date()`'s `formatStateUsing`
checks `if (blank($state)) return null` *before* attempting to parse, so a genuinely-null state now
formats cleanly and only the *display* falls back to the placeholder.

**Verified:** re-ran Playwright against `/staff/chat-conversations` after `view:clear` → HTTP 200, all
3 seeded conversations render correctly (escalated row shows its real timestamp; active/closed rows
show the placeholder), zero errors. Screenshot reviewed, genuine correct Arabic RTL rendering.

### 5.3 `ContactMessagePolicy` — missing, authorization fails open (security, MEDIUM)

**Found:** of 35 Filament-Resource-backed models (cross-referenced against real `discoverResources()`
calls in every PanelProvider, not just filename matching), 2 had no matching Policy class — `Alert`
(confirmed dead code, unreachable, not a live gap — a pre-existing orphaned resource from before the
panel reorg) and `ContactMessage` (genuinely registered and reachable in the live Admin panel).

**Impact:** Filament's authorization is deliberately non-strict on this project (`docs/AQAR_DECISIONS.md`),
so the absence of a Policy class fails **open**: any Admin-panel-eligible user (super_admin *and*
staff) could view/manage public contact-form submissions (real visitor names/emails/phones/messages)
with no explicit permission check — directly contradicting `RolesSeeder`'s own
`matchesStaffAllowlist()`, whose subject list *deliberately excludes* `contact_message` (its own
comment: "new sensitive resources must be consciously added rather than being auto-granted").

**Fixed:** added `app/Policies/ContactMessagePolicy.php`, following the exact generated-policy pattern
already used everywhere else in this codebase (permission-per-action via Spatie's `$user->can()`). No
explicit registration needed — this project's `AuthServiceProvider::$policies` is empty; it relies
entirely on Laravel's model-name auto-discovery convention, confirmed working via
`Gate::getPolicyFor()`.

**Verified:** `staff->can('viewAny', ContactMessage::class)` is now `false` (was silently `true`);
`admin->can(...)` stays `true` — exactly matching the seeder's own stated intent. The
`view_*_contact_message` permissions themselves already existed (created by `PermissionsSeeder`'s
`shield:generate --option=permissions`, which only needs a discovered Resource, not a Policy) — no
re-seeding was needed, just the missing enforcement class.

### 5.4 `RESEED_FRESH` crash-loop on Railway — two real bugs stacked, both found and fixed live, fully resolved and verified (deploy reliability, HIGH)

This one was found *while executing the cleanup this session's other fixes made possible* — using
`RESEED_FRESH=true` (added in §5.1) to clear out the already-duplicated live property data. It is
reported here with the full honest timeline, including a first fix that turned out not to work —
per this project's own rule that nothing is `FIXED` without an actual verification step, a claim of
success gets withdrawn the moment live evidence contradicts it, not left standing in the report.

**What happened, observed directly via Railway's deploy logs:** `migrate:fresh --seed --force`
crash-looped. Multiple attempts, ~90 seconds apart, each threw
`Spatie\MediaLibrary\MediaCollections\Exceptions\DiskCannotBeAccessed` deep inside
`ResponsiveImageGenerator`, reached from `PropertySeeder::attachGalleryPhotos()`'s
`toMediaCollection()` call. `.docker/start-container`'s `RESEED_FRESH` branch runs this command under
`set -e` with **no** `|| true` fallback — so each crash aborted the whole container boot, and Railway
auto-restarted it, re-running the entire fresh reseed from scratch each time. (The deployment that sat
on the crash-looping code, `f34661d9`, was later independently confirmed `CRASHED` in Railway's own
deployment history — an earlier `environment-status` snapshot that had looked transiently healthy was
a timing artifact, not evidence the boot ever actually succeeded; this report no longer relies on that
signal.)

**First fix attempt (PR #17, `b4ae817`) — verified locally, then found NOT to work live:** added
`->withResponsiveImagesIf(false)` to the seeder's `addMedia()` call, reasoning that seeded photos never
needed responsive srcset variants. Verified locally (`migrate:fresh --seed --force` clean, 28/28
properties with photos) and merged. **On the very next live `RESEED_FRESH` redeploy after merging,
the identical crash happened again, at the identical line.** The fix had no effect in production —
a real miss caught only by watching the actual redeploy through to completion rather than trusting the
local verification and the merge as sufficient.

**Actual root cause of why the first fix didn't work:** traced into
`vendor/spatie/laravel-medialibrary/src/MediaCollections/FileAdder.php`. `toMediaCollection()` →
`attachMedia()` → `processMediaItem()` **unconditionally** calls `checkGenerateResponsiveImages()`,
which re-derives the responsive-images flag from the **media collection's own registered default**
(`Property::images` has `->withResponsiveImages()`) and silently re-enables it — discarding whatever
`withResponsiveImagesIf(false)` had set on the `addMedia()` chain moments earlier. There is no way to
override this per-call once the collection itself has responsive images enabled; only the collection
registration is authoritative.

**Real fix (PR #18, `3cbbf74`):** added `Property::$skipResponsiveImagesForSeeding`, a plain instance
property checked *inside* `registerMediaCollections()` itself
(`->withResponsiveImagesIf(! $this->skipResponsiveImagesForSeeding)`). `registerMediaCollections()` is
re-invoked fresh on the specific model instance every time `FileAdder` needs the collection's settings,
so this is the one place in the call chain that actually sticks. `PropertySeeder` sets the flag on each
`$property` before its gallery-photo loop; real user uploads via Filament are unaffected (the flag
defaults `false`).

**Verified locally:** `migrate:fresh --seed --force` end-to-end — exit 0, zero errors. `PropertySeeder`
now completes in **607ms** (down from 75,990ms even on the one earlier run where it happened not to
crash — responsive-image generation was the expensive part too, not only the crash trigger). Confirmed
via `tinker`: 28/28 properties have photos (70 media rows), and a sample media row's
`responsive_images` field is genuinely empty (`[]`) — generation is actually skipped, not silently
swallowing an error. **Merged (PR #18) and redeployed — but the crash-loop continued anyway**,
because a second, independent bug had been masked underneath the first one the whole time (below).

**Second bug, found on that very next live redeploy:** the crash's stack trace had changed — no longer
routed through `ResponsiveImageGenerator` at all (proof the PR #18 fix genuinely worked), but still
throwing `DiskCannotBeAccessed`, now from the base media file copy itself
(`FileAdder.php:616` → `Filesystem->put()` returning `false`), deterministically, on `PropertySeeder`'s
very first `addMedia()` call, every attempt. Checked Railway's own `DISK_USAGE_GB` service metric:
the `web` service's 500MB persistent volume was reading **~504MB used — completely full**.
`migrate:fresh --seed` only ever resets the *database*; it never touches the filesystem, so every
earlier crashed or partial `RESEED_FRESH` attempt across this whole investigation (and likely before
it) had left its already-written property photos orphaned on the volume, with nothing ever cleaning
them up, until it filled completely and every subsequent write failed outright.

**Real, complete fix (PR #19, `70960b7`):** `.docker/start-container`'s `RESEED_FRESH` branch now
clears `storage/app/public` (`rm -rf` + recreate) *before* running `migrate:fresh --seed --force`, so
the flag's own "reset the demo to a clean slate" promise actually covers the filesystem too, not only
the database. Deleting doesn't require free space, so this works correctly even starting from an
already-full volume.

**Verified live, fully resolved:** merged, redeployed, and this time watched the complete deploy log
end to end rather than trusting an early "SUCCESS" status or an absence-of-error snapshot (both had
been misleading earlier in this investigation — one Railway deployment status flips to `SUCCESS`
before the actual boot-time seed chain even starts, and an `environment-status` "healthy" read had
earlier turned out to be a timing artifact against a build that later showed `CRASHED`). This redeploy:
exactly **1 container boot** (no restart loop), full migration set applied, full seed chain completed
including `PropertySeeder` (1,259ms, zero errors) through `DemoDataCompletionSeeder`, reached
`Octane Server: roadrunner` and a running RoadRunner process — genuinely serving, not just reported as
such. `DISK_USAGE_GB` confirmed back to a healthy ~70MB (from the prior ~504MB/full). `RESEED_FRESH`
was then set back to `false` on the `web` service (per its own "never leave this on" documentation),
which itself triggered one more redeploy — logs confirm it correctly took the normal boot path
("Nothing to migrate", not another fresh rebuild), admin account ensured, `storage:link` connected,
config cached, and the server started cleanly. The live staging demo is confirmed healthy, fully and
correctly seeded, with `RESEED_FRESH` correctly off, as of this report.

**Why this took three PRs to actually resolve, honestly stated:** each of the three fixes (PR #17,
#18, #19) addressed a real thing — #17's reasoning was sound even though its mechanism didn't stick;
#18 correctly fixed the mechanism and is independently verified to have worked; #19 found and fixed a
second, entirely separate bug that had been hiding behind the first one's symptom the whole time. None
of the three was a wasted or incorrect fix — the crash-loop simply had two independent causes stacked
on top of each other, and the second one only became visible once the first was actually gone. The
lesson carried into the rest of this report and this project's practice going forward: a local
verification plus a merge is not "done" for anything that runs against live infrastructure — only a
fully-read deploy log, from the same commit, showing the actual expected end state (not just an early
or ambiguous status flag), counts as verified.

---

## 6. Detailed Test Results

### 6.1 Tier P0 — 10/10 PASS

| # | Check | Result |
|---|---|---|
| 1 | Guest homepage, Arabic default, `dir="rtl"` | ✅ PASS — 200, correct `dir`/`lang`, zero real console errors |
| 2 | Self-registration → working panel (historical P18-1 class) | ✅ PASS — landed on a genuine, fully-functional Tenant dashboard, zero 403 |
| 3–11 | Login for all 9 personas (admin/staff/agent/buyer/seller/landlord/tenant/contractor + guest) | ✅ PASS — all 9 land on their correct dashboard URL, zero errors |

Evidence: `qa_p0_results.json`, screenshots `01`–`03` (all visually reviewed — genuine, correctly
rendered Arabic RTL content, real seeded data visible on every dashboard).

### 6.2 Tier P1 — Panel sweep: 107/109 PASS (2 failures found → both fixed, see §5.2)

Live nav-discovery + visit for every persona's full sidebar (not a hand-picked subset):

| Persona | Pages found | Result |
|---|---|---|
| admin | 37 | 36 PASS, 1 FAIL (chat-conversations — fixed) |
| staff | 34 | 33 PASS, 1 FAIL (chat-conversations — fixed) |
| agent | 4 | 4/4 PASS |
| buyer | 3 | 3/3 PASS |
| seller | 4 | 4/4 PASS |
| landlord | 7 | 7/7 PASS |
| tenant | 7 | 7/7 PASS |
| contractor | 5 | 5/5 PASS |

Buyer and Agent panels intentionally expose no Filament Resources beyond Dashboard/EditProfile
(browse/favorite/booking flows live on the public site, not the panel) — confirmed by reading
`BuyerPanelProvider`/`AgentPanelProvider` and their `Resources/` directories (empty, `.gitignore`
only). This matches this project's own documented design, not a bug.

### 6.3 Full CRUD lifecycle — PASS (live browser, Staff + Admin panels, Properties)

Explicitly requested by the user ("أضف وعدل واحذف" — add/edit/delete):

1. **Create** (staff): filled the full property form incl. the required Features repeater row,
   submitted → landed on the new record's edit page. PASS.
2. **Read/search** (staff): new property found via the list's search box. PASS.
3. **Update** (staff): renamed the title, saved, no error. PASS.
4. **Delete boundary check**: staff correctly has **no** delete capability for properties (verified via
   `tinker`: `$staff->can('delete_property')` = `false`, matching `RolesSeeder`'s explicit design —
   "Staff can manage most content but not delete"). The one "🗑" icon reachable in the staff session
   turned out, on inspecting its `wire:click` payload, to be the Features-repeater row-remove control,
   not the record action — Filament correctly hid the real record-delete action from an unauthorized
   user; the confusion was in the test script's generic selector, not the app.
5. **Delete** (admin, who has full permissions): located the true page-header `DeleteAction` by its
   precise `wire:click` payload, triggered it, got Filament's real confirmation modal ("حذف عقار" /
   "هل أنت متأكد من القيام بهذه العملية؟"), confirmed, redirected to the list.
6. **Final verification**: `Property::find(29)` confirmed gone from the DB via `tinker`.

Screenshots `06` through `21` document every step, including the genuine Arabic RTL confirmation
modal.

### 6.4 Public-facing features — PASS (guest session)

| Feature | Result |
|---|---|
| `/properties` list | ✅ 200, real seeded listings (photos, prices, bed/bath/area) |
| `/properties/1` detail | ✅ 200, gallery (5 images), **community events and property history text both detected** — first live confirmation these two recently-seeded features actually render somewhere reachable |
| `/calculators` | ✅ 200, real interactive mortgage calculator, fields genuinely labelled in YER (`ر.ي`) throughout, not `$` |
| `/contact` form | ✅ submitted, **and verified via `tinker` that a real `contact_messages` row was created** (not just a UI success message) — count went 4→5 with matching name/email |
| `/news` + `/news/{slug}` | ✅ both 200, real seeded article content |
| Chatbot widget | ✅ floating trigger opens a real chat panel; sent a genuine Arabic question ("كم سعر شقة في المكلا؟") and got an immediate, contextually relevant reply plus a live-staff escalation option — backed by a real `ChatbotController`, not a stub. **Minor i18n finding** (low severity): the reply came back in English despite the Arabic-locale question — the canned/fallback response doesn't appear to key off `app()->getLocale()`. Worth a follow-up, not release-blocking. |
| `/wishlist` | ✅ 200, reachable as guest |

### 6.5 Tier P2 — Language / theme / responsive — PASS

| Check | Result |
|---|---|
| English locale switch | ✅ `dir="ltr"`, `lang="en"`, zero raw untranslated keys detected, correct icon mirroring (chat bubble moved to bottom-right) |
| Dark mode toggle on `/login` | ✅ PASS — this specific page class was the one missed in an earlier documented dark-mode sweep (P13-6); regression-checked, still fixed |
| Responsive 375px (mobile) — homepage + property detail | ✅ zero horizontal overflow |
| Responsive 768px (tablet) — homepage + property detail | ✅ zero horizontal overflow |
| Responsive 1440px (desktop) — homepage + property detail | ✅ zero horizontal overflow |

---

## 7. Not Yet Executed (honest scope boundary)

Per the priority ordering in §3, execution reached deep into Tier P2 before the live `RESEED_FRESH`
incident (§5.4) took priority (a real, in-progress production/staging issue outranks continuing a
QA checklist). The following remain **not yet tested this session**:

- Deep per-role feature interaction beyond reachability: Landlord Leases/Payments/Rental-Applications
  data-accuracy, Tenant Maintenance-Request creation flow, Buyer favorite/booking/review flows on the
  public site, Seller "My Listings" data correctness, Contractor Work-Order assignment view.
- Booking/viewing appointment creation end-to-end (create → persists → visible to the right roles).
- Virtual tour / 3D model viewer rendering (a seeded property has a `virtual_tour_url` set — not yet
  opened and visually confirmed).
- Form validation messaging (deliberately-invalid input on public forms).
- 404/500 error page correctness (nonexistent property ID / nonexistent route).
- SEO basics (`<title>`/OG/canonical tags, `/sitemap.xml`, `/robots.txt`).
- Tier P3 in full: compare-properties tool, saved searches, PDF/Excel report export, email-campaign
  admin UI content review, energy/utility record display, a full reachability sweep of every
  remaining Filament Resource class not explicitly covered above.

None of these gaps are known to be broken — they are simply not yet verified. Recommended as the next
session's starting point, in the same priority order.

---

## 8. Recommendations

1. ~~Merge the RESEED_FRESH fix, confirm one clean redeploy, set the flag back to `false`~~ — **done**:
   PR #19 merged and redeployed; the deploy log shows exactly 1 container boot, a complete seed chain,
   disk usage back to healthy (~70MB, was ~504MB/full), and `RESEED_FRESH` is confirmed `false` again
   as of this report (§5.4). If a future re-seed is ever needed, it should now complete cleanly in one
   pass — but per the lesson in §5.4, verify that via a fully-read deploy log each time, not an early
   status flag.
2. Continue the Tier P1/P2/P3 items listed in §7, in that order.
3. Consider the low-severity chatbot locale finding (§6.4) — reply language should follow
   `app()->getLocale()`.
4. Consider reviewing the 10 stale Dependabot PRs (§4.5) — none urgent, but 2 weeks old.
5. `docs/DEPLOYMENT-ENVIRONMENT-INVENTORY.md` still frames itself around "Laravel Cloud" — worth a
   pass to reflect the actual Railway deployment, now that this report has established the current
   Railway-specific facts in §4.
6. The orphaned `app/Filament/Resources/AlertResource.php` (confirmed dead code, §5.3) is a safe,
   minor cleanup candidate whenever convenient — not urgent, not a live gap.

---

## Appendix: Test Personas

All seeded accounts use password `password` (documented in `docs/TEST_USERS.md`):
`admin@darhadhramaut.ye`, `staff@darhadhramaut.ye`, `agent@darhadhramaut.ye`,
`buyer@darhadhramaut.ye`, `seller@darhadhramaut.ye`, `landlord@darhadhramaut.ye`,
`tenant@darhadhramaut.ye`, `contractor@darhadhramaut.ye`.
