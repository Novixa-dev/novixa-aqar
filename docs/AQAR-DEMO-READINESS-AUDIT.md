# Aqar Demo Readiness Audit

**Audit date:** 2026-08-24
**Branch audited:** `feature/aqar-productization` @ `41a8787db65c2be8168a4911d5685096543c22a6` (confirmed via `git rev-parse`, clean working tree except pre-existing build-asset churn in `public/build/`)
**Auditor method:** direct verification (test suite, `migrate:fresh --seed`, `npm run build`, live Playwright browser QA against a real seeded dev server, CI/Docker file review, `.env`/`.env.example` review) plus four parallel deep-dive static/live audit passes (database & seeders, translation/RTL/theme, Filament panels/routes/security, public frontend/SEO/media), each independently re-verifying prior claims in `AQAR_PRE_RELEASE_AUDIT_REPORT.md` and `docs/AQAR_TASKS.md` against the current code rather than trusting them at face value.
**Relationship to prior audit work:** this repo already carries an extensive prior audit (`AQAR_PRE_RELEASE_AUDIT_REPORT.md`) and a "Phase 0–12" fix campaign logged in `docs/AQAR_PROGRESS.md`, culminating in a same-day "release gate PASS" entry. Nearly all of that prior work holds up under independent re-verification — this audit confirms the large majority of it. But re-verification also surfaced **new defects the prior passes missed**, including one release-blocking seeding failure, one credible new cross-tenant IDOR, and a severe live-reproduced image-loading failure. Do not treat the prior "PASS" as current without reading this report's Critical Issues section.

---

## Final Verdict

# READY WITH FIXES

The core platform is real, substantially complete, and — for the specific surface directly re-verified live in this pass (all 9 Filament panel logins, 11 public pages × AR/EN) — genuinely working with zero console/page errors on that surface. This is not a hollow shell; it is not "NOT READY" in the way the original 2026-08-20 baseline audit found (six panels hard-crashing, zero permissions enforced, a live IDOR). That baseline state is gone.

However, this pass found **one release-blocking defect** (the documented `migrate:fresh --seed` procedure fatally crashes on a stock local PHP install) and **several high-severity, demo-visible defects** that were not caught by the prior QA passes (a credible new API-level IDOR, English-locale pages rendering Arabic UI chrome, the three highest-traffic auth pages never receiving the dark-mode fix applied to their siblings, no discoverable language switcher anywhere in the app despite being marked fixed, and a live-reproduced CSP failure that hides every property photo sitewide unless `APP_URL` exactly matches the serving origin). None of these are individually as severe as the original baseline's panel crashes, but several are exactly the kind of "first five minutes of a demo" defect the release rules exist to catch. Fix the items in the Critical Issues section below before any public demo; everything else can ship as-is or be scheduled post-demo.

---

## Project Snapshot

| | |
|---|---|
| Stack | Laravel 13.24, PHP 8.4 (Filament 5.7.6, Filament Shield ~4.0, Livewire 4.3.5, Spatie Permission 6.25), Jetstream + Socialstream, Sanctum, Octane (RoadRunner), Horizon, Reverb, Stripe, DocuSign, Tailwind 4, MySQL |
| Scale | 148 migration files, 100 Eloquent models, 22 seeder files, 36 factory files, 35 Policy files, 9 Filament panels, **712 registered routes** (up from the 669 baseline — grown during the fix campaign) |
| Demo tenant identity | "Dar Hadhramaut Real Estate / دار حضرموت العقارية", platform developed by Novixa Aqar — confirmed consistent site-wide in this pass (see Branding Consistency) |
| Test baseline | Prior sessions: 558–559 passed / 0 failed / 32 skipped. This session's re-run: see Automated Test Results below |
| Seeded personas | 8 roles + super_admin, all `@darhadhramaut.ye`, password `password` (`docs/TEST_USERS.md`) |

---

## Audit Scope

Full-repository static analysis (migrations, models, seeders, factories, Policies, PanelProviders, routes, Blade/Livewire views, lang files, CI/Docker config) plus live dynamic verification: a real `migrate:fresh --seed` run against MySQL, the full automated test suite, a production `npm run build`, and a real Chromium (Playwright) session logging into all 8 seeded personas + super_admin and loading 11 public routes in both locales, capturing console errors, page errors, and failed network requests. Four independent audit passes were run in parallel, each explicitly instructed to re-verify prior claims rather than assume them, and to flag findings as VERIFIED (code read / command run / live-reproduced) vs INFERRED. This report only asserts VERIFIED findings as fact; INFERRED/credible-but-unconfirmed findings are labeled as such.

**Not covered in this pass:** exhaustive manual click-through of every one of the 712 routes' CRUD forms; a full WCAG contrast/keyboard audit; load/performance testing beyond N+1/seed-time observations; penetration testing beyond static/code-level security review.

---

## Architecture

Confirmed live: Laravel 13 on PHP 8.4.16 (via `herd/bin/php84`, not the PATH-default 8.4.0 — matches documented environment quirk), Filament 5 across 9 panels (Admin, Staff, App, Tenant, Landlord, Buyer, Seller, Contractor, Agent), Livewire 4, MySQL 8, Vite 8 + Tailwind 4 for the frontend build. Octane (RoadRunner, not Swoole — confirmed `.env.example` correctly reflects this), Horizon, Reverb, and Stripe are installed and configured but **not exercised by this audit** (queue is `sync`, cache is `file`, broadcast is `log` in the default `.env.example` — deliberate zero-dependency defaults, documented in-file). DocuSign is present as an optional integration, not required for the core signing flow (which was previously rewritten to internal signing per `AQAR_TASKS.md`).

CI (`.github/workflows/`): `tests.yml` (PHP 8.5, `php artisan test --coverage-clover`), `security.yml` (`local-php-security-checker`, `phpcpd`, `php-insights` — the latter two run with `|| true`, i.e. informational-only, never fail the build), `install.yml`, `main.yml`. Docker: a production-oriented multi-stage `Dockerfile` (PHP 8.5-alpine, RoadRunner via Octane, Supervisor-managed processes for HTTP/Horizon/Reverb/scheduler/worker modes) and a `docker-compose.yml` wiring MySQL 8/Redis 7/Mailpit with healthchecks.

---

## Demo Scope

Inferred from routes, navigation, and seeded data: **public property browsing and lead-generation site** (home, search/filter, property detail with 3D models/virtual tours/investment analytics/walkability/floor plans, news, calculators, contact, owner property submission, property request) backed by **9 role-specific self-service Filament panels** (Admin/Staff full CRUD; Agent/Buyer/Seller/Landlord/Tenant/Contractor role-scoped self-service views). This is a broad, multi-sided marketplace demo, not a narrow single-flow demo — the feature matrix below reflects that breadth.

---

## Feature Matrix

Status legend: READY / READY WITH MINOR ISSUES / PARTIALLY WORKING / BROKEN / PLACEHOLDER / UNWIRED / REQUIRES MANUAL CONFIG / POST-DEMO.

| Feature | Status | Evidence |
|---|---|---|
| Public property search/browse/detail | READY WITH MINOR ISSUES | Live-verified 200s, zero JS errors in isolation; property-detail gallery only shows photo 1 of N (see High Priority Issues) |
| Owner property submission / property request forms | READY | Routed, live-verified 200 both locales; prior session end-to-end-verified real DB record creation |
| 9 Filament panel logins + dashboards | READY | Live-verified this session: all 9 personas 200, zero console/page/failed-request errors |
| Filament authorization (Policies/permissions) | READY WITH MINOR ISSUES | 34/35 Policies complete; `ImagePolicy::view()` missing (fails closed, breaks one page for non-super-admin); see Security Audit |
| `migrate:fresh --seed` demo-data bootstrap | **BROKEN (as documented)** | Fatal PHP memory exhaustion on stock 128M CLI `memory_limit`; succeeds with an explicit override — see Database & Seeders Audit |
| Bilingual AR/EN UI | READY WITH MINOR ISSUES | 342/356 keys, 0 unresolved `__()` calls; but 118 `en.json` values are Arabic text (see Translation Audit) — a real, visible defect |
| Language switching | **UNWIRED** | No discoverable AR/EN toggle anywhere in the app; only URL query param, undocumented to end users — see Translation Audit |
| Dark/light theme | READY WITH MINOR ISSUES | Filament panels consistent; public site has 14 real un-fixed `bg-white`-without-`dark:` gaps, including login/register/forgot-password (highest-traffic pages) |
| RTL/LTR | READY | Near-clean; 5 flagged instances are false-positive-adjacent (symmetric full-bleed positioning) |
| Chatbot | READY | Fully wired, routed, throttled, DB-backed |
| AI Investment Analysis | READY | Fully wired, embedded in property-detail |
| 3D property models (Three.js) | READY | Fully wired and live |
| 3D property models (media-upload path) | UNWIRED | Confirmed orphaned stub, not consumed by any UI |
| Document Generator | **PLACEHOLDER/BROKEN** | Confirmed known-broken stub, also confirmed unreachable from any UI |
| Insurance | UNWIRED | Backend model/service exist; zero route, zero Filament resource, zero view references it |
| Auction/Bidding, Blockchain, Holographic, Neural valuation | **NOT FOUND** | Zero occurrences in application code (buzzwords exist only in old docs) |
| Portal sync (RightMove/Zoopla/OnTheMarket) | READY (per-team scoped) | Prior P0-5 fix confirmed still correct in structure; not re-exercised live this session (no active settings rows in seed data) |
| SEO (sitemap/robots/canonical) | READY WITH MINOR ISSUES | Sitemap/robots correct; only 1 of 15+ public pages has a real meta description; JSON-LD only on property-detail |
| Reports/exports/PDF | NOT INDEPENDENTLY RE-VERIFIED THIS PASS | No new findings surfaced; treat prior audit's coverage gaps as still open |
| Deployment (Docker/CI) | READY WITH MINOR ISSUES | Builds correctly; `docker-compose.yml` still has a leftover `liberu` default DB name (see Build & Deployment Readiness) |

---

## Live Verification: All 9 Personas + Public Pages (this session)

Performed with a real Chromium session (Playwright) against a freshly `migrate:fresh --seed`'d dev database, driven by this audit directly (not the pre-existing progress log's claims).

**Persona login → dashboard walkthrough — 9/9 clean:**

| Persona | Login → | Dashboard status | Console errors | Page errors | Failed requests |
|---|---|---|---|---|---|
| super_admin | `/admin` | 200 | 0 | 0 | 0 |
| staff | `/staff/dashboard` | 200 | 0 | 0 | 0 |
| agent | `/agent/dashboard` | 200 | 0 | 0 | 0 |
| buyer | `/buyer/dashboard` | 200 | 0 | 0 | 0 |
| seller | `/seller/dashboard` | 200 | 0 | 0 | 0 |
| landlord | `/landlord/dashboard` | 200 | 0 | 0 | 0 |
| tenant | `/tenant` | 200 | 0 | 0 | 0 |
| contractor | `/contractor/dashboard` | 200 | 0 | 0 | 0 |

**Result: confirms the prior "release gate PASS" claim for this specific surface.** Every seeded persona reaches a working, error-free dashboard on first login. This is a genuine, re-verified strength of the current codebase.

**Public pages — 11 routes × 2 locales (22 loads) — all HTTP 200, correct `dir`/`lang` attributes and correct localized `<title>` per locale, zero *page* errors, zero *failed requests* — but a systematic console-level defect on every page that renders a property-photo card:**

Every one of the 9 pages that render property thumbnails (`/`, `/properties`, `/about`, `/contact`, `/services`, `/calculators`, `/news`, `/terms-and-conditions`, `/privacy-policy`, `/submit-property`, `/property-request` — i.e. effectively the whole public site, since property cards appear in shared layout/footer sections) logged repeated browser **CSP violations**:

```
Loading the image 'http://127.0.0.1:8000/storage/1/property-1-1.jpg' violates the following
Content Security Policy directive: "img-src 'self' data: https:". The action has been blocked.
```

This was reproduced live, not inferred. Root cause, confirmed by cross-reading `app/Http/Middleware/SecurityHeaders.php` and the `.env.example` comment on `APP_URL` (*"Must match the public host exactly: Spatie medialibrary generates absolute /storage URLs from it, and a mismatch breaks every property photo"*): Spatie Media Library bakes `config('app.url')` into every generated media URL as an **absolute** URL. This audit's test server ran on a different port (8123) than the configured `APP_URL` (port 8000) — a same-class mismatch to what any real deployment risks if `APP_URL` isn't set to *exactly* the serving origin (scheme + host + port). When the image's origin doesn't match the page's origin, the CSP's `img-src 'self' data: https:` directive has no bare `http:` fallback to allow it, and the browser silently drops the image.

**This elevates the existing P0-6 finding from "cosmetic SEO/canonical-tag leak" to "every property photo on every public page fails to load if the deploy environment's `APP_URL` doesn't exactly match the serving origin."** The `.env.example` comment already warned about this in the abstract; this audit is the first to actually reproduce the failure live and confirm its true blast radius (site-wide broken images, not just metadata). See Critical Issues below.

---

## Database Audit

Raw counts (verified by direct file read, not inference): **148 migrations, 100 Eloquent models, 22 seeders, 36 factories.**

**[BLOCKER] `PropertyModerationResource` binds form fields that don't exist on the `properties` table, and overloads `properties.status` with a third, incompatible meaning.** `app/Filament/Staff/Resources/PropertyModerations/PropertyModerationResource.php` binds `Select::make('status')` (options `pending/approved/rejected`) plus `DateTimePicker::make('moderated_at')` and `Textarea::make('moderation_notes')` directly onto the `Property` model. Neither `moderated_at` nor `moderation_notes` exist in any `properties` migration, and neither is in `Property::$fillable`. Any staff user who actually uses this resource will have their save either silently drop data or throw, **and** will overwrite the same `status` column that encodes for-sale/for-rent/sold/rented, corrupting real listing state. This resource should be considered unsafe to use in a demo until fixed.

**[HIGH] `properties.status` has 5 distinct, mutually incompatible naming conventions in live use** (worse than the 3-way split documented in the prior audit): Title Case (`'For Sale'`/`'For Rent'`, written by `PropertySeeder`/`DemoWorkflowSeeder`/`PropertyFactory`), snake_case (`for_sale`/`for_rent`/`sold`/`rented`, written by the live Staff `PropertyResource` form every staff user actually edits properties through), lowercase single-word (`sold`/`let`/`to_let`, read by 5+ agent-performance files), UK-portal terminology (14 more values accepted by the V1 API's validation), and two more ad-hoc `whereIn` lists in `PublicWebsiteController`/`ReportController`. **Concrete, demo-visible consequence, verified:** every "Properties Sold" statistic (Agent Performance dashboard and its 3 supporting files) will show **0** against the real seeded data, because the seeder never writes the literal string `'sold'` that those files filter on. This is not a theoretical inconsistency — it is a guaranteed-zero stat on a real, reachable dashboard.

**[HIGH] `RentalCharge` model + controller exist with zero backing database table.** No `rental_charges` migration exists anywhere. Currently unreachable (not registered in any route file), so this is latent dead code rather than a live crash risk — but any future work that routes `RentalChargeController` will fail immediately.

**[MEDIUM] `lease_agreements` is fully wired into the Staff panel UI but never seeded — it will render empty in the demo — while the separate, overlapping `leases` table (which is seeded, 2 rows) backs a different resource used by Staff/Landlord/Tenant.** A demo viewer who happens to click "Lease Agreements" instead of "Leases" sees an empty list next to a populated sibling feature, which reads as broken even though the code itself is not.

**[MEDIUM] `properties.country` still DB-defaults to `'UK'`.** Fully masked today because `PropertySeeder` explicitly overrides every seeded row to `'Yemen'`, but any other insertion path (a future "quick add," a factory, a direct API create) that omits `country` silently reverts to `UK` on a Yemen-first platform.

**[LOW] 5 seeder files exist on disk but are never called from `DatabaseSeeder::run()`:** `TeamSeeder` (harmless — superseded by the correctly-branded `DefaultTeamSeeder`, which *is* called), `BranchSeeder`, `CommunityEventSeeder`, `PropertyHistorySeeder`, `UKASTTemplateSeeder`. The latter three mean the Community Events feature and the property price-history timeline will be empty on a fresh demo seed even though the seeding code to populate them already exists.

**Demo data realism and cross-table consistency — clean, no defects found.** `PropertySeeder` uses `ar_SA` Faker locale, hardcoded Arabic titles/descriptions, real Al-Mukalla coordinates, `country => 'Yemen'` explicit override, and currency-aware YER/SAR/USD pricing. `DemoWorkflowSeeder` resolves all cross-entity links by email/status lookup, not hardcoded IDs — verified no fragile ordering dependencies. Zero `liberu`/`GBP`/`£`/UK-city artifacts found anywhere in `database/seeders`. `tenants` table's independent-identity-from-`users` design (the documented historical root cause of the original P0-2 IDOR) is confirmed unchanged at the schema level, but the query-scoping fix around it is confirmed still correctly applied (see Security Audit).

**On `migrate:fresh --seed` reproducibility (Section 8 of the audit brief) — this is the audit's most important finding, see Critical Issues.**

---

## Translation Audit (i18n)

**Raw counts:** `lang/ar.json` 342 keys, `lang/en.json` 356 keys. 0 keys missing from `en.json` that exist in `ar.json`. 14 keys present only in `en.json` (by design — they're pre-translated Arabic strings used as dictionary keys, and Laravel's fallback already returns the key text correctly when no `ar.json` entry exists — not a bug). 11 nested PHP lang files per locale, key-parity-matched in 9 of 11 (`validation.php` missing 14 low-impact `attributes.*` entries in `en`, falls back gracefully; `agency.php` holds two entirely disjoint, dead vocabularies per locale — config drift, not a live bug, since nothing in the app reads it). **395 unique `__()`/`trans()` call-site keys extracted and cross-referenced: 0 fail to resolve in either locale** — no raw-translation-key-displayed-to-user risk exists anywhere in the app.

**[HIGH, NEW] 118 keys in `lang/en.json` map to Arabic-language values, not English.** Confirmed by direct read of the file (roughly lines 226–355): `"Cancel": "إلغاء"`, `"Save": "حفظ"`, `"Delete": "حذف"`, `"Close": "إغلاق"`, `"Reset Password": "إعادة تعيين كلمة المرور"`, and the entire Team-management / Two-Factor-Auth / API-Token / Browser-Sessions vocabulary that Jetstream's stock views wrap in `__()`. **Live consequence:** any user on the English locale who visits Profile, Team Settings, API Tokens, 2FA setup, or Browser Sessions sees Arabic text embedded in an otherwise-English page. This was not caught by any prior QA pass (a green test suite and even a browser-console check wouldn't catch it — the page renders correctly, just with wrong-language content) and is exactly the kind of defect a bilingual demo audience notices immediately.

**[HIGH, NEW] No discoverable language switcher exists anywhere in the application.** `docs/AQAR_TASKS.md` credits `App\Support\PanelSwitcher` with fixing P1-13 ("no in-panel language switcher"). Independent re-verification of that class's actual code shows it is a **cross-*panel* navigator** ("Switch to: Admin" / "الانتقال إلى: الموظفون" links between the 9 role panels) — it uses the current locale only to pick which language to render its own labels in; it contains no mechanism to change the locale itself. A sitewide grep for any `?lang=`/`?locale=` link or language-toggle UI element, across the public site and all 9 panels, found zero matches. The only way to change locale is manually editing the URL query string — which the backend middleware does correctly honor and persist, but which no real user would discover unaided. **P1-13 should be reopened as still broken**, not closed.

**Live untranslated text confirmed on reachable pages:** `livewire/property-tax-estimator.blade.php` (embedded in the live `/calculators` page and in property-detail) renders `"Total Tax"` and `"Effective Tax Rate"` in English unconditionally, regardless of locale. `livewire/property-detail.blade.php` has 7 more hardcoded English strings (review prompts, "Preferred Date"/"Preferred Time"/"Additional Notes (Optional)" on the live booking form).

**Confirmed clean / matches prior claims:** Filament `->label()`/`->placeholder()`/`->helperText()` sweep — exactly 1 raw string remains sitewide, and it's the deliberately-excluded `user@example.com` format example. RTL utility sweep — near-perfectly clean (5 flagged instances are all direction-symmetric full-bleed overlays, not real bugs). Filament's own built-in dark-mode/theme-switcher system — confirmed consistent across all 9 panels (framework default is `true`, `AdminPanelProvider` sets it explicitly redundant with the default). Several files the progress log listed as "not yet touched" (`custom-report-form`, `custom-reports/index`, `investment-analysis`) were independently re-checked and found **already fully bilingual and dark-mode-covered** — the progress log was stale on this point, in the "better than claimed" direction.

**Dark-mode gaps, systematic sweep, 30 `bg-white`-without-`dark:` instances found, 14 confirmed live/reachable across 7 files (16 confirmed dead/orphaned code or justified exceptions):**

**[HIGH, NEW] `auth/login.blade.php`, `auth/register.blade.php`, `auth/forgot-password.blade.php` — the three highest-traffic pages in the entire application, every persona's literal first click — never received the dark-mode fix applied to their sibling auth pages** (confirm-password, reset-password, two-factor-challenge, verify-email all use the shared `<x-authentication-card>` component that was fixed; login/register/forgot-password use their own raw, un-fixed `bg-white` card markup). Switching to dark mode on the login screen — the very first thing any demo viewer who prefers dark mode will do — shows a broken light-only card.

**[MEDIUM, NEW]** Also missing dark-mode coverage, none previously flagged: the shared Jetstream modal primitive (`components/modal.blade.php`, used by live team-management dialogs), `livewire/advanced-property-search.blade.php` (embedded in a real public search page), `livewire/property-booking.blade.php` (routed, 4 instances), `livewire/property-comparison.blade.php` (routed, 2 instances), `livewire/wishlist-manager.blade.php` (routed).

---

## Filament Panels, Routes & Security Audit

**712 routes confirmed live via `route:list`** (up from the 669 baseline). **Panel registration (P0-3A): confirmed still correctly fixed** — all 4 previously-broken PanelProviders now import and register their own panel's `EditProfile` class, verified against live route output, not just the import statement (Filament resolves by `{panel}.{slug}`, so a shared class reused *inside* a panel's own `pages()` array — which Admin/Agent/Staff deliberately do — is not itself a bug).

**[MEDIUM, NEW, cosmetic]** `Tenant/Pages/EditProfile.php` has a copy-paste-damaged route override (a dead 4-argument call, extra args silently discarded by PHP) that produces a visibly-odd doubled URL segment: `/tenant/tenant/profile` instead of the clean `/tenant/edit-profile` every other panel uses. Functional, not a crash, but looks broken in a URL bar.

**Policy coverage: 35 policy files exist, 34/35 (97%) implement all 5 of viewAny/view/create/update/delete.** `ImagePolicy::view()` is the one exception — genuinely missing, not just untested. Because Filament fails closed by default and `super_admin` gets an explicit `Gate::before` bypass, this doesn't manifest as an open security hole; it manifests as **a real page (`ViewImage`) being denied to any non-super-admin role that should otherwise have access** — the prior claim of "completed the Image policy" is only partially true.

**[HIGH, NEW, credible but not fully proven exploitable] A cross-tenant IDOR of the same bug class as the original P0-2, in a code path P0-2's fix never touched.** `app/Http/Controllers/API/V1/MaintenanceController.php` extends `TenantCrudController`, whose query scoping is by `team_id` only — no per-tenant/per-user restriction. `MaintenanceRequestPolicy::view()`/`update()`/`delete()` are pure ability checks with no record-level ownership check (this exact gap is already documented as an accepted risk elsewhere in the codebase, for a different resource). Combined, this means: any authenticated API caller holding a `maintenance`-related agency-permission on a team (a separate authorization system, `AgencyRole`, from the Spatie roles governing the Filament panels) can potentially read/update/delete **any other team member's** maintenance record via `GET/PATCH/DELETE /api/v1/maintenance/{id}`. This was not confirmed via a live exploit attempt in this pass (would require confirming a seeded persona actually holds the relevant `AgencyRole` grant, which needs a targeted follow-up), but the code-level gap is real and directly analogous to the original P0-2. **Recommend a scoped follow-up check before demo**, and treat as HIGH not BLOCKER given the unconfirmed exploitability.

**[LOW, NEW] Dead code, not missing features:** 3 orphaned legacy Filament resources (`Alert`/`Booking`/`DocumentTemplate`) sit in an un-namespaced `app/Filament/Resources/` directory that no PanelProvider discovers. 8 orphaned Livewire components (`MaintenanceRequestForm`, `ManageBooking`, `SchedulingSystem`, `DocumentGenerator`, `AgentPerformanceDashboard`, `CommunicationHub`, `AIAssistant`, `MarketAnalysisChart`, plus 6 more found by the translation-audit pass: `PropertyRecommendations`, `LatestNews`, etc.) each have a working, already-wired counterpart elsewhere — these are superseded duplicates, not gaps.

**Security posture — confirms all prior claims, no new issues found:** CSP's documented `unsafe-inline`/`unsafe-eval` trade-off unchanged; CSRF exemption list empty; no `$guarded = []` on any model; no hardcoded secrets/API-key patterns in `config/`; `/api/metrics` payload-reduction + rate-limiting (P2-9) confirmed still in place; `.env.example`'s `APP_DEBUG=true`/`APP_ENV=local` confirmed still present and still correctly classified as an environment-provisioning task, not a code bug.

**Authorization spot-checks — P0-2 and P1-7 fixes both confirmed still correctly applied** (Tenant `MaintenanceRequestResource` scopes through the `Tenant` record; Landlord `RentalApplicationResource` scopes by property ownership *and* has a real Policy-level second check). The previously-documented "accepted risk" (Contractor role's global `view_any_user`/`update_user` grant, safe today only because of the Resource's own query scope, not the permission model) is confirmed unchanged and still accurately documented.

**Buzzword feature wiring, verified:**

| Feature | Verdict |
|---|---|
| Chatbot | FULLY WIRED |
| AI Investment Analysis | FULLY WIRED |
| 3D Models (Three.js) | FULLY WIRED |
| 3D Models (media-upload path) | ORPHANED STUB |
| Document Generator | FULLY ORPHANED / KNOWN-BROKEN |
| Insurance | BACKEND-ONLY, NO UI ANYWHERE |
| Auction/Bidding | NOT FOUND IN CODE |
| Blockchain / Holographic / Neural valuation | NOT FOUND IN CODE |
| VR/AR (as a distinct feature) | NOT FOUND — closest real analog is the 3rd-party virtual-tour iframe allowlist (Matterport/Kuula/etc.), which is genuinely wired |

---

## Frontend / SEO / Accessibility / Media Audit

**18 public routes/views audited.** The prior claim that all full-page Livewire components were "repointed to `layouts.app`" (fixing an earlier bug where they rendered on a bare, meta-less shell) is **confirmed correct** — traced through Livewire's own default-layout resolution, not just assumed.

**[MEDIUM, NEW] The actual per-page SEO gap is broader than documented.** `/submit-property`, `/property-request`, `/services`, `/terms-and-conditions`, `/privacy-policy` render with only the generic sitewide `<title>`/no description (confirmed live in this session's QA — several of these show the bare site name as their title, e.g. "دار حضرموت العقارية" with no page-specific suffix). Only `property-detail` has a real per-page meta description; every other page site-wide has none. Structured data (JSON-LD) exists **only** on property-detail — confirmed completely absent everywhere else, a broader gap than the prior "lacks some fields" framing.

**[MEDIUM, NEW] Property-detail page shows only 1 of N gallery photos — no gallery/thumbnail/lightbox UI exists**, despite a prior session specifically bundling and attaching 16 real photos (2–3 per property, confirmed 70 media rows in the live DB this session). This is real, spent effort that is currently invisible to a demo viewer — clicking into any property shows exactly one hero image with no way to see the rest.

**Images:** 0 of 12 `<img>` tags site-wide are missing meaningful `alt` text — clean. Media mime/size validation confirmed present and broader than assumed (`images`, `videos`, `3d_models`, and document collections all validated, not just `images`).

**Zero lorem-ipsum/placeholder-text hits anywhere in views or seeders** — confirms the demo has no visible filler content.

**Accessibility, spot-checked on home/properties-list/property-detail:** property-detail's heading hierarchy is non-logical in one section (a conditional `<h3>` renders before the page's own `<h1>` in DOM order depending on 3D-model presence). The property-list filter form's 7 inputs (search, purpose, type, bedrooms, bathrooms, min/max price) use visually-adjacent `<label>` text with no `for=`/`id=` programmatic association — a real screen-reader gap on a primary workflow page.

**Confirmed clean / matches prior claims:** storage symlink present and correct; sitemap (44 URLs, all real property-status conventions matched defensively) and `robots.txt` both serve correctly; no `noindex` anywhere; no hreflang (confirms the already-documented, architecturally-larger P1-20 gap); dead files (`bookings/*.blade.php`, `manage_section`/`products_section` components) confirmed still present but genuinely unreachable, `home-header` confirmed actually deleted.

---

## Automated Test Results

Full suite run to completion this session (a first attempt was interrupted by an environment restart mid-run; re-run cleanly to completion afterward, using the required PHP 8.4.16 binary, no test weakened or deleted per project rules):

**553 passed / 0 failed / 32 skipped (2130 assertions), duration 1362.2s (~22.7 minutes), exit code 0.**

This is consistent with every prior session's baseline (558–559 passed / 0 failed / 32 skipped — the small count drift is normal variance from tests added/removed across sessions, not a regression). The 32 skips are self-documented in the suite's own output as a Livewire test-harness limitation (`Filament form/table tests require Livewire test setup`), not flakiness. **Zero failures — the automated suite is clean and imposes no blocker.**

One operational note for future runs: this session's suite took ~22.7 minutes end-to-end, noticeably longer than earlier same-day runs, most likely due to CPU contention with a concurrently-running `migrate:fresh --seed` (see Build & Deployment Readiness) rather than a suite regression — the same suite ran faster once seeding had finished.

---

## Build & Deployment Readiness

**`npm run build` (production Vite build): succeeds.** One warning worth tracking: the main JS bundle (`app-*.js`) is 1.31 MB uncompressed / 364 KB gzipped, over Vite's 500 KB chunk-size-warning threshold — not a blocker, but a real page-weight concern for a public-facing demo on a slow connection. No code-splitting is currently applied to the large Three.js/model-viewer/chatbot bundle.

**[BLOCKER — see Critical Issues] `php artisan migrate:fresh --seed` — the exact command this repo's own documentation directs a developer to run — fatally crashes on a stock local PHP install** (`memory_limit=128M`, PHP 8.4.16's default). Confirmed via direct reproduction:

```
PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 8192 bytes)
in vendor\spatie\image\src\Drivers\Gd\GdDriver.php on line 290
```

during `PropertySeeder`'s media-conversion generation for the bundled real photos. Confirmed the seed **succeeds completely** when re-run with an explicit memory override (`php -d memory_limit=1024M artisan migrate:fresh --seed`) — verified via full post-seed DB counts matching every expected total (28 properties, 8 users, 1 team, 2 leases, 8 payments, 4 maintenance requests, 3 rental applications, 5 news articles, 9 roles, 393 permissions, staff correctly holding 124 granted permissions, 70 media rows). **The Docker image's shipped `.docker/php.ini` sets `memory_limit=256M`**, double the failure threshold — likely sufficient, but not independently verified in this pass (the container's seed-on-first-boot path was not exercised live).

**[MEDIUM, NEW] `PermissionsSeeder` takes ~408 seconds (6.8 minutes) to run**, confirmed reproducibly across two full seed runs this session. This is a real performance red flag for any CI pipeline or fresh-deploy script with a timeout — `.github/workflows/tests.yml`'s job has a 15-minute cap, which the test run itself (not this seeder) consumes; a deploy script that also seeds could be at meaningfully higher risk of timing out.

**[LOW, NEW] `docker-compose.yml` still carries the leftover "liberu" template default** for the MySQL service's fallback database/user names (`MYSQL_DATABASE: '${DB_DATABASE:-liberu}'`, `MYSQL_USER: '${DB_USERNAME:-liberu}'`) — `.env.example` itself was already correctly fixed to `DB_DATABASE=aqar` in a prior session, but this second, independent leftover in `docker-compose.yml` was missed. Low impact (only surfaces if `DB_DATABASE`/`DB_USERNAME` env vars are left completely unset when running `docker compose up`), but the same class of branding-leftover issue as the already-tracked P2-22.

CI/Docker structure itself is otherwise sound: multi-stage Docker build, non-root container user, health-checked services, Supervisor-managed multi-process container modes (http/horizon/reverb/scheduler/worker), and a CI test job pinned to PHP 8.5 (within the `composer.json` `^8.4|^8.5` range).

---

## Environment Variables & Manual Configuration Required

Extracted from actual `.env.example` and code usage — nothing speculative added.

| Manual Action | Required? | Why | Variable(s) | Can demo launch without it? |
|---|---|---|---|---|
| Set `APP_URL` to the **exact** public demo origin (scheme+host+port) | **Yes, critical** | Confirmed live this session: a mismatch doesn't just leak `localhost` into canonical/OG/sitemap tags (the originally-documented risk) — it makes the browser's CSP block **every property photo sitewide** | `APP_URL` | No — public site will look broken (missing images everywhere) |
| Set `APP_DEBUG=false` | Yes, critical | Stack traces/error internals must not be exposed publicly | `APP_DEBUG` | No |
| Increase PHP `memory_limit` for the seed step, or verify the deploy path's 256M is sufficient | Yes, critical | `migrate:fresh --seed` fatally crashes at 128M (default) | PHP `memory_limit` (php.ini or CLI `-d`) | No, for a from-scratch deploy |
| Configure a real mail provider (SMTP/Resend/SendGrid, etc.) | Yes, for contact form / notifications to work | `.env.example` ships Mailpit/`smtp` placeholder host | `MAIL_MAILER`, `MAIL_HOST`, etc. | Demo runs, but contact-form/notification emails silently fail |
| Configure `CONTACT_INBOX_EMAIL` | Recommended | Falls back to `MAIL_FROM_ADDRESS` if unset — confirm intended recipient | `CONTACT_INBOX_EMAIL` | Yes, has a safe fallback |
| Configure Stripe keys | Only if a payment flow is in the demo script | `STRIPE_KEY`/`STRIPE_SECRET`/`STRIPE_WEBHOOK_SECRET` all blank in `.env.example` | `STRIPE_*` | Yes, if no payment demo is planned |
| Switch `QUEUE_CONNECTION`/`CACHE_DRIVER` to `redis`, run Horizon | Only if background jobs (portal sync, notifications) need to actually run during the demo | Currently `sync`/`file` — zero-dependency, jobs run inline instead of queued | `QUEUE_CONNECTION`, `CACHE_DRIVER` | Yes for a click-through demo; No if a live sync/notification needs to be shown async |
| Configure Reverb + `BROADCAST_DRIVER=reverb` | Only if any real-time feature is in the demo script | Currently `log` (no-op) | `REVERB_*`, `BROADCAST_DRIVER` | Yes, if no real-time feature is shown |
| WalkScore / Neighborhood Data API keys | No | Code has a documented, tested mock-data fallback when unset (`WalkScoreServiceTest`: "returns mock data when api key not configured") | `WALKSCORE_API_KEY`, `NEIGHBORHOOD_DATA_API_KEY` | Yes — mock data is realistic and already demo-appropriate |
| DocuSign credentials | No | Signing flow was rewritten to internal signing per prior session; DocuSign is optional | `DOCUSIGN_*` (not in `.env.example`, confirmed genuinely optional) | Yes |
| S3/cloud storage | No | `FILESYSTEM_DISK=local` with a working `public/storage` symlink is sufficient for a demo; no code path requires S3 | `AWS_*` (present, all blank, unused unless explicitly switched) | Yes |
| OAuth provider credentials (Google/GitHub/etc.) | No | Confirmed already gated: only providers with real credentials render a login button (prior fix, re-confirmed structurally sound) | `SOCIALSTREAM`-related | Yes — buttons simply don't appear if unconfigured |
| DNS/SSL for the real demo domain | Yes, standard | Standard pre-launch step | — | No, if going live on a real domain |

---

## Disconnected / Placeholder Features

See Feature Matrix and the "Buzzword feature wiring" table above for the full verdict list. Summary of genuinely dead/placeholder code found or re-confirmed this pass: Document Generator (known-broken stub, confirmed unreachable), Insurance (backend-only, zero UI), the media-upload 3D-model path (orphaned stub alongside the working Three.js path), `RentalCharge` (model+controller with no backing table, unrouted), `lease_agreements` (fully wired UI, zero seed data), and roughly 14 orphaned Livewire components across two audit passes — all confirmed to have working counterparts elsewhere, i.e. leftover duplicates rather than missing functionality.

---

## Branding Consistency

**Clean, single-sourced, re-confirmed this pass.** Live browser QA this session shows a consistent "دار حضرموت العقارية" / "Dar Hadhramaut Real Estate" identity across the homepage `<title>` in both locales, with "Novixa Aqar" appearing only in its correct, singular role as the platform developer attribution. Grep of all seeders for `liberu`/`GBP`/`£`/stray brand names: zero hits. The one leftover `liberu` artifact found this pass is in `docker-compose.yml`'s fallback env-var defaults (see Build & Deployment Readiness) — invisible to any demo viewer, relevant only to a deploy operator who skips setting `DB_DATABASE`.

---

## Demo Credibility Review

Viewed as a first-time visitor: the public site is professional-looking, has no lorem ipsum, no broken alt text, no fake placeholder buttons, and a coherent Yemen/Hadhramaut identity throughout. The specific things that would visibly undercut that impression today, in rough order a visitor would hit them: **(1)** every property photo failing to load if `APP_URL` isn't set exactly right (site-breaking, see Critical Issues); **(2)** the login/register screen looking broken in dark mode, the very first thing many users try; **(3)** clicking into any property and finding only one photo despite the listing implying a gallery; **(4)** an English-locale visitor hitting Arabic text on the Team/Profile/2FA pages; **(5)** no way to actually switch language despite a language mechanism existing.

---

## Critical Issues (fix before demo)

1. **[BLOCKER] `migrate:fresh --seed` fatally crashes on a stock local PHP install (128M default `memory_limit`).** Fix: either raise `memory_limit` in the documented setup instructions (`CLAUDE.md`'s testing section and any onboarding doc) to at least 512M for the seed step, or make `PropertySeeder`'s media-conversion step less memory-hungry (process one image at a time / lower conversion quality / defer conversions to a queued job). Verify the Docker image's 256M is actually sufficient by exercising `RUNNING_MIGRATIONS_AND_SEEDERS=true` for real before relying on it.
2. **[BLOCKER-ADJACENT / CRITICAL DEPLOY STEP] `APP_URL` must be set to the exact serving origin before demo, or every property photo sitewide silently fails to load** (live-reproduced CSP violation, this session). This was previously only understood as an SEO/canonical-tag issue; it is now confirmed to be an all-images-broken issue. Add this as an explicit, tested pre-launch checklist step — don't just set the variable, load the homepage after deploying and confirm images render.
3. **[HIGH] `PropertyModerationResource` writes to non-existent columns and corrupts `properties.status`.** Either fix the underlying schema (add `moderated_at`/`moderation_notes` columns and stop overloading `status`) or remove/hide this resource from the demo build until fixed — do not let a demo presenter click into it.
4. **[HIGH] 118 `en.json` translation values are Arabic text**, breaking the English-locale experience on Profile/Team/2FA/API-Token/Browser-Session pages. Fix: retranslate these 118 entries to real English.
5. **[HIGH] No discoverable language switcher exists anywhere.** Either add a real, visible AR/EN toggle (public site + all 9 panels), or explicitly script the demo to only demonstrate one locale at a time via a pre-set URL.
6. **[HIGH] Credible cross-tenant IDOR in the V1 API's maintenance-request endpoints** (same bug class as the already-fixed P0-2, different code path). Recommend a scoped verification (confirm whether any seeded persona holds the relevant `AgencyRole` grant) and a fix (add ownership scoping to `TenantCrudController`/`MaintenanceRequestPolicy`) before exposing this API publicly.
7. **[HIGH] Login/Register/Forgot-Password pages break visually in dark mode** — the three highest-traffic pages in the app. Apply the same `<x-authentication-card>` fix already proven on the 4 sibling auth pages.
8. **[MEDIUM] Property-detail page shows only 1 of the seeded 2–3 photos per property**, with no gallery UI — wastes real, already-completed seeding work and looks unfinished. Add a simple thumbnail strip or lightbox over `$property->getMedia('images')`.

---

## High Priority Issues

- `ImagePolicy::view()` missing — breaks the `ViewImage` page for non-super-admin roles.
- `properties.status` 5-way naming inconsistency causes "Properties Sold" stats to always read 0 against seed data.
- `RentalCharge` model/controller with no backing table (currently dead, not user-visible, but a landmine for future work).
- `lease_agreements` fully wired in Staff panel UI but zero seed data — reads as broken next to the populated `leases` sibling feature.
- Public SEO gap broader than previously documented: only 1 of 15+ public pages has a real meta description; structured data exists only on property-detail.
- 6 additional dark-mode gaps beyond login/register/forgot-password: shared Jetstream modal, advanced property search, property booking, property comparison, wishlist manager.
- Property-list filter form's 7 inputs lack programmatic `<label for=>` association — real accessibility gap on a primary workflow page.
- `PermissionsSeeder` takes ~7 minutes — a real CI/deploy-pipeline timing risk.

## Medium Priority Issues

- `properties.country` DB-level default still `UK` (masked by explicit seeder override today).
- 5 seeder files never wired into `DatabaseSeeder` (Community Events and Property History features will look empty).
- `docker-compose.yml` leftover `liberu` fallback default DB/user name.
- `Tenant/Pages/EditProfile` produces a cosmetically-doubled `/tenant/tenant/profile` URL.
- Property-detail page's heading hierarchy is non-logical in one conditional section.
- Live untranslated strings on `/calculators` ("Total Tax", "Effective Tax Rate") and property-detail (7 strings on the booking/review UI).
- Main JS bundle exceeds Vite's 500KB chunk-size warning threshold (1.31MB / 364KB gzip) — real page-weight concern.

## Low Priority Issues

- 3 orphaned legacy Filament resources under an undiscovered `app/Filament/Resources/` directory.
- ~14 orphaned Livewire components duplicating already-working features (safe to delete, not urgent).
- `agency.php` lang file pair holds two disjoint, dead vocabularies per locale.
- `validation.php` missing 14 low-impact `attributes.*` keys in `en` (has a graceful fallback).
- `PropertyRecommendations` orphan component references a route name that doesn't exist (harmless — unreachable).

## Post-Demo Improvements

- Delete confirmed-dead code (orphaned Livewire components, `bookings/*.blade.php`, `manage_section`/`products_section` components, 3 orphaned Filament resources) once a maintainer has had a chance to double-check nothing depends on them.
- Consolidate `properties.status` to one canonical set of values with a real PHP enum + data migration (flagged repeatedly across multiple audit passes now — this is the single most-recurring root cause in the codebase).
- Resolve the `leases`/`lease_agreements` duplication (same root-cause class as `status`: two competing models for one concept).
- Code-split the main JS bundle (Three.js/model-viewer/chatbot) to address the build-size warning.
- Add real hreflang/locale-prefixed URL routing (large, architectural — already tracked as P1-20).

---

## Final Pre-Launch Checklist

- [ ] Increase `memory_limit` for the seed step (or fix `PropertySeeder`'s memory usage) and confirm `migrate:fresh --seed` completes cleanly on the actual deploy target
- [ ] Set `APP_URL` to the exact public demo origin, then **load the homepage after deploying and visually confirm property photos render** (not just that the page returns 200)
- [ ] Set `APP_DEBUG=false` on the public demo environment
- [ ] Fix or hide `PropertyModerationResource` before any staff-persona demo walkthrough
- [ ] Fix the 118 mistranslated `en.json` entries
- [ ] Decide: add a real language switcher, or script the demo around a fixed locale
- [ ] Apply the dark-mode fix to login/register/forgot-password
- [ ] Configure a real mail provider if the contact form or email notifications are part of the demo script
- [ ] Configure Stripe keys only if a payment flow is scripted into the demo
- [ ] Run the full test suite one final time on the actual deploy target and confirm 0 failures
- [ ] Final smoke test: log in as all 9 personas and click through their primary dashboard on the actual deployed environment (not just locally)

---

## Final Score

| Dimension | Score | Why |
|---|---|---|
| Technical Readiness | 72/100 | Solid architecture, real 9-panel authorization now working; docked for the seed-crash blocker and the API-level IDOR gap |
| Functional Readiness | 78/100 | All 9 personas + 22 public page/locale combinations verified live with zero errors; docked for the gallery/status/lease_agreements gaps |
| UI/UX | 74/100 | Consistent design, zero placeholder content; docked for the login/register dark-mode break and the single-photo gallery |
| Localization | 68/100 | Structurally sound (0 raw-key risk, near-perfect RTL) but the 118 mistranslated en.json values and missing language switcher are real, visible defects |
| Responsive | 82/100 | Confirmed clean at 375px in prior verified sessions; not independently re-tested this pass beyond desktop |
| Security | 76/100 | Authorization model is real and enforced; docked for the missing ImagePolicy method and the credible new API IDOR |
| SEO | 60/100 | Sitemap/robots correct, but only 1 page site-wide has a real meta description and structured data is single-page |
| Demo Data | 80/100 | Realistic, Yemen-appropriate, internally consistent seeded data; docked for the status-naming chaos and two empty-but-wired features |
| Deployment Readiness | 62/100 | Docker/CI structure is sound, but the seed-crash blocker and the unverified 256M-sufficiency mean "will it actually stand up cleanly" is not yet proven |
| **Overall Demo Readiness** | **72/100** | READY WITH FIXES — fix the 2 blockers and the 6 high-priority items above, then this is a genuinely presentable demo |
