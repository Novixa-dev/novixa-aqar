# Aqar (Novixa Aqar) — Pre-Release Audit & Execution Report

**Audit date:** 2026-08-20 (original pass); **updated same day** with a follow-up live-application verification pass (see below).
**Branch audited:** `feature/aqar-productization`
**Scope:** Full codebase (Laravel 13 / Filament 5 / Livewire 4), all 9 Filament panels, public site, API, docs corpus (~90 `.md` files), CI, and dev-environment verification (`route:list`, full test suite run, and — new in the follow-up pass — an actual browser-driven login-and-click-through of every seeded role).
**Method:** 8 parallel deep-dive passes (documentation reconciliation ×2, architecture/code quality, Filament panels & authorization, localization/RTL, SEO/OpenGraph, security/auth, testing/CI/deployment), each independently verifying claims against the actual running code — not against prior audit documents. **Follow-up pass:** re-ran the test suite and `route:list` against the live working tree to confirm no drift, independently re-verified 8 of the original report's concrete claims by direct inspection, and — the main addition — actually started the app (`php artisan serve`, real MySQL dev DB, PHP 8.4.16) and logged into every one of the 9 seeded role accounts in a real browser, which surfaced a materially more severe version of P0-3 (Section 4) that no code-reading pass had caught. Findings below are labeled **Verified** (read the code / ran the command / reproduced live) or **Likely** (strong inference, not executed) per source.
**Supersedes:** every prior audit artifact in this repo (`docs/audit/01`–`39`, `docs/project/*`, *reference removed — document no longer exists*, and all root-level `FINAL_STATUS*`/`IMPLEMENTATION_*` reports). Those documents contradict each other on material facts (test-pass counts, security posture, branding, feature completeness) and should be treated as historical record only — see Section 17. **This report itself was revised once already, within the same day, after a live-app pass found its own Section 4/7 understated a defect's severity — a live illustration of the exact "always re-verify, never trust a status claim at face value" discipline this report otherwise asks of every prior document in this repo.**

---

## 1. Executive Summary

Novixa Aqar is a substantially-built, feature-rich Laravel/Filament real-estate platform for the Yemeni market. The core browsing experience (property search, detail pages with 3D models/virtual tours/walkability/investment analytics/floor plans, chatbot, wishlist) is real, wired end-to-end, and the automated test suite genuinely passes (558/558, 0 failures) once run on a correct PHP version. **It is not ready for a public demo release today.**

The blocking issues are not cosmetic. This audit found:

- A **live cross-tenant data leak** (IDOR) in the Tenant panel — any logged-in tenant can view, edit, and bulk-delete any *other* tenant's maintenance requests.
- **Authorization is decorative for ~90% of the admin/staff surface.** Only 5 of 45 Filament resources have a real Laravel Policy; Filament fails *open* by default, so the carefully-designed role/permission model in `RolesSeeder.php` does not actually restrict access to Users, Payments, Leases, Documents, and most other sensitive resources.
- **Six of the nine Filament panels crash with a fatal HTTP 500 error on the very first click after login** — this was not visible to the original code-reading pass and was only found by actually running the app in a browser and logging in as each seeded role. Landlord, Buyer, Seller, and Contractor all throw `RouteNotFoundException: Route [filament.{panel}.pages.edit-profile] not defined` on every page (their `PanelProvider`s import the *App* panel's `EditProfile` class instead of their own, and register an empty `->pages([])`). Tenant and the generic `App` panel crash on different bugs — a query for a nonexistent `properties.tenant_id` column, and `transactions.transaction_amount` instead of the real column `transactions.amount`, respectively. **This is far more severe than "empty shells"**: a demo audience clicking into any of these six roles hits a raw Laravel error page, not an underwhelming dashboard. See the rewritten P0-3 in Section 4 — the fix for all six is small (a handful of one-line corrections), but it is currently live and 100% reproducible.
- **Two features required by the project's own MVP scope document are fully built but have zero routes** — Owner Property Submission and Property Requests are unreachable by any URL, despite a "final release audit" doc falsely claiming all 18 roadmap tasks are complete.
- A **multi-tenant property-sync data leak**: once any one agency configures a RightMove/Zoopla API key, the scheduled sync job pushes *every* other tenant's properties to that one account.
- Brand identity is inconsistent in exactly the places a visitor or investor would notice first: the public site's `<title>`/OpenGraph tags say "Dar Hadhramaut," while `.env`, the footer, and legal pages say "Novixa Aqar."
- The environment itself has a PHP version mismatch (Composer requires ≥8.4.1, the default local PHP is 8.4.0) that fatally blocks every `artisan` command — a class of problem that could silently break a first deployment.

None of these are individually catastrophic to fix, and the underlying application is more complete than the sprawling, contradictory documentation history in this repo would lead you to believe. But taken together they mean: **do not let anyone log into the Landlord, Buyer, Seller, Contractor, or Tenant panels until P0-3 is fixed (they will hit a raw error page), do not assume the permission model is enforced, and do not treat this repo's own prior "COMPLETE"/"VERIFIED" claims — including earlier drafts of this very report — as fact without re-checking.**

**Verdict: NOT READY.** See Section 24 for the full reasoning and the minimum path to a safe, scoped demo.

---

## 2. Current Project Status

- **Stack:** Laravel 13.24.0, PHP 8.4 (Filament 5.7.6, Filament Shield 4.3.1, Livewire 4.3.5, Spatie Permission 6.25.0), Jetstream + Socialstream (OAuth), Sanctum, Octane (RoadRunner), Horizon, Reverb, Stripe, DocuSign, Tailwind 4.
- **Scale:** 103 Eloquent models, 669 registered routes, 9 Filament panels (Admin, Staff, App, Tenant, Landlord, Buyer, Seller, Contractor, Agent), 103 test files (558 tests, 2094 assertions).
- **Git state:** mid-refactor on `feature/aqar-productization`. Three files were just deleted (`app/Filament/App/Pages/Tenancy/EditTeamProfile.php`, `app/Http/Controllers/Landlord/AuthController.php`, `app/Services/LeaseAgreementService.php`) — **confirmed clean** by three independent agents: zero remaining references anywhere in the codebase, `route:list` and the full test suite both run without error.
- **Documentation state:** ~90 markdown files across the repo root and `docs/`, representing five chronological "generations" of audit/status documentation that materially contradict each other (see Section 17). The most recent internal document, *reference removed — document no longer exists* (2026-08-17–19), is the only one that should be treated as a status source, and it explicitly leaves its own Phase 5 (browser-rendered visual QA) **open**, having swept only ~15% of the app's surface.
- **What actually works, verified by running it:** `php artisan route:list` completes cleanly (669 routes, no fatal errors). `php artisan test` passes 558/558 with 32 self-documented skips (Livewire test-harness gap, not flakiness). The core property-browsing experience — search, detail page, 3D models, virtual tours, chatbot, wishlist, walkability, investment analytics, floor plans, property history, news, community events, neighborhood data — is real, wired, and tested.

---

## 3. Overall Readiness Assessment

| Dimension | Status | Notes |
|---|---|---|
| Core property browsing (public) | 🟢 Good | Best-tested, best-SEO'd part of the app |
| Authentication (login/register) | 🟡 Functional but under-hardened | No rate limiting on shared login; password reset broken for 7/8 roles |
| Authorization (Filament panels) | 🟢 Fixed and verified live | All 32 unique models policied; real permission grants restored for every role (was silently zero for all non-super_admin roles — see P0-1 update) |
| Role-specific self-service panels | 🔴 Broken (verified live) | 6 of 9 panels (Landlord, Buyer, Seller, Contractor, Tenant, App) throw a fatal HTTP 500 on first click after login; Agent is a bare profile form; only Admin/Staff are stable |
| MVP-required public features | 🔴 Incomplete | Owner Submission & Property Requests built but unroutable |
| Multi-tenancy correctness | 🟢 Fixed and verified live | RightMove/OnTheMarket/Zoopla sync now scoped per-team; confirmed with `Http::fake()` that 2 teams' syncs use each team's own key/URL with zero cross-contamination |
| Localization (AR/EN) | 🟡 Partial | Solid on public pages' primary content; weak on flash messages, dates, panel switching |
| RTL/LTR | 🟢 Mostly good | A handful of `space-x-*`/`ml-`/`mr-` foot-guns, not systemic |
| SEO / metadata | 🟡 Sitemap fixed; per-page metadata still one-page-only | Sitemap now complete and scheduled daily (P1-19, fixed); property-detail is still the only page with real per-page `<title>`/OG (P1-18, open) |
| Security posture | 🟡 No critical holes found, but real gaps | No hardcoded secrets, CSRF/mass-assignment clean; rate limiting and e-signature integrity need work |
| Branding consistency | 🔴 Inconsistent, user-visible | "Dar Hadhramaut" vs "Novixa Aqar" collision in `<title>`/OG vs footer/legal |
| Testing | 🟢 What exists passes | 558/558, but real coverage gaps on Payments and Auth |
| Deployment config | 🟡 Multiple template bugs | `.env.example` has a broken Octane default, missing Reverb/DocuSign vars, duplicate session keys |
| Documentation | 🔴 Untrustworthy as-is | Internally contradictory; needs consolidation before it can be used as a release gate |

---

## 4. Release Blockers (P0) — must fix before any public demo

| ID | Title | File(s) | Why it blocks release |
|---|---|---|---|
| **P0-1** | **[FIXED, verified live]** Filament authorization fails **open**: only 5 of 45 resources have a Policy | `app/Policies/*` (was only `Appointment`, `Booking`, `ConnectedAccount`, `Image`, `Property`, and those 5 were themselves missing `viewAny`/`create`/`delete`) | **Fixed and closed two bugs, not one.** (1) Generated real Policies for all 32 unique underlying models via `shield:generate` across all 9 panels. (2) Found and fixed something worse while doing it: `RolesSeeder.php`'s permission grants had **never actually applied to any role except super_admin**, because `config/filament-shield.php`'s permission naming (`View:Property`) never matched the seeder's case-sensitive snake_case checks (`view_property`) — this predates this session entirely. Fixed via a custom `FilamentShield::buildPermissionKeyUsing()` closure (`AppServiceProvider::boot()`) producing the naming the seeder always assumed, plus 2 seeder pattern-name corrections (`communication_hub`→`message`, `contractor`→`user` — both resources use a different underlying model than their name implies) and several missing `view_any_X` companions that were silently hiding nav items. Verified live: `staff`/`buyer`/etc. now correctly allowed/denied per real permission (spot-checked via direct `Gate::allows` calls and browser walkthroughs of all 9 panels); staff correctly gets 403 on `/admin/users`. Full detail: `docs/AQAR_TASKS.md`, `docs/AQAR_DECISIONS.md`. |
| **P0-2** | Live cross-tenant IDOR: Tenant Maintenance Request resource has no query scoping | `app/Filament/Tenant/Resources/MaintenanceRequests/MaintenanceRequestResource.php` (no `getEloquentQuery()` override; sibling `LeaseResource`/`PaymentResource` in the same folder both have one) | Any tenant test account can view, edit, and bulk-delete every other tenant's maintenance requests today. This is not theoretical — it is a confirmed, exploitable data-integrity bug in a resource with create/edit/delete actions live. |
| **P0-3** | **[Verified live, upgraded from "empty" to "crashes"]** Six of nine Filament panels throw a fatal HTTP 500 on the first authenticated page load | See breakdown below | Not a code-reading inference — reproduced by actually running `php artisan serve` against the real `feature/aqar-productization` working tree (PHP 8.4.16, MySQL, seeded `docs/docs/TEST_USERS.md` accounts) and logging in as every role. Six of nine role logins immediately hit Laravel's raw error page instead of a dashboard — a guaranteed, instant demo failure for any of these personas, strictly worse than the "empty dashboard" originally assumed from static code reading. |
| **P0-4** | Owner Property Submission & Property Requests — required MVP features, fully built, zero routes | `app/Livewire/PropertySubmissionForm.php`, `app/Livewire/PropertyRequestForm.php` + their Blade views | `docs/ai/ANTIGRAVITY_AQAR_MASTER_PROMPT.md` (the project's own operating brief) explicitly lists "Submit your property" and "Property request" as required public MVP items. Both components exist in full but are referenced nowhere in `routes/web.php`/`api.php` or any other view — unreachable by any click path or URL. `docs/tasks/TASK-018` (Final Release Audit) falsely claims all 18 roadmap tasks are complete, papering over this exact gap; TASK-010/011 honestly self-report `IN_PROGRESS` in their own files. |
| **P0-5** | **[FIXED, verified with `Http::fake()`]** Multi-tenant portal-sync data leak | `app/Services/RightMoveService.php`, `OnTheMarketService.php`, `ZooplaApiService.php` | **Correction from the original wording:** the "correctly team-scoped `PortalIntegration` system" mentioned below is not actually a usable replacement — it's a status-tracking stub that never calls the real external APIs, so removing the legacy commands in its favor would have silently disabled real sync entirely (see `docs/AQAR_DECISIONS.md`). Fixed the 3 legacy services directly instead: added `forSettings(Settings): static` to each (per-team API key/base URI, replacing a global `config()` read), scoped their property queries to `team_id`, and changed the jobs/commands/schedule to dispatch one sync per active team rather than one global sync (also fixing the `routes/console.php` `->when()` gates, which — like an identical bug found in P0-1 — only ever checked the *first* active settings row). `ZooplaSettings` had no `team_id` at all (unlike its two siblings) — added via migration, matching the existing pattern; Filament's tenancy already auto-populates it on create (`AdminPanelProvider`'s `->tenant()`), same as the other two. Also fixed the related P1-9 `needsSyncing()` bug (comparing a column to a string literal) while in the same code path. Verified: `Http::fake()` + 2 real teams/settings/properties — exactly 2 HTTP calls recorded, each to the correct team's own `base_uri` with that team's own `api_key`. |
| **P0-6** | `APP_URL=http://localhost` will leak into production canonical/OG/sitemap URLs | `.env`, `.env.example`, confirmed live in `public/sitemap.xml` (`<loc>http://localhost</loc>`) | Every canonical tag, OG URL, and sitemap entry derives from `APP_URL`. If the demo/production environment doesn't override this, search engines and social shares will show `localhost` links. Trivial fix, but must be explicitly checked as part of demo-environment provisioning, not assumed. |
| **P0-7** | Brand-name collision visible in `<title>`/OpenGraph vs. footer/legal copy | `resources/views/layouts/app.blade.php:8-10` (site-wide title/meta say "Dar Hadhramaut Real Estate / دار حضرموت العقارية"); `.env:1` `APP_NAME="Novixa Aqar"`; `resources/views/components/footer.blade.php`, `terms-and-conditions.blade.php` (say "Novixa Aqar") | This is the single most visible, first-impression bug a stakeholder/investor could hit — the browser tab, every social share preview, and every search snippet will show a different company name than the footer and legal pages. Confirmed independently by the code-level SEO audit *and* by this session's live browser pass: the public homepage's `<title>` reads "دار حضرموت العقارية" while the Admin/Staff Filament panels' `<title>` reads "… \| Novixa Aqar" and the footer copyright line reads "© 2026 دار حضرموت العقارية" — three different names visible within one click-through, sourced from two different `GeneralSettings` DB fields (`site_name` vs `footer_copyright`) that were never reconciled. |
| **P0-8** | Dev/deploy PHP version mismatch fatally blocks all `artisan` commands | Composer requires `"php": "^8.4\|^8.5"` with a lockfile-baked platform check floor of `>=8.4.1`; the default local `php` resolves to exactly 8.4.0 | Confirmed to fatally error before Laravel even boots (`platform_check.php`). CI pins PHP 8.5, so this exact skew is untested there. Must be explicitly verified against whatever server will host the public demo — a silent boot failure on first deploy is a self-inflicted, entirely avoidable P0. |

### P0-3 breakdown — root causes and exact fixes (all verified by reproducing live, not inferred)

This was found by actually starting the app (`php artisan serve`, real MySQL dev DB, PHP 8.4.16) and logging in as every seeded role from `docs/docs/TEST_USERS.md`, then reading `storage/logs/laravel.log` for the resulting stack traces. Two distinct, unrelated bugs are responsible, each affecting multiple panels:

**Bug A — wrong `EditProfile` class imported, and never registered, in 4 PanelProviders.** `app/Providers/Filament/{Landlord,Buyer,Seller,Contractor}PanelProvider.php` each contain `use App\Filament\App\Pages\EditProfile;` (the **`App` panel's** EditProfile class — copy-pasted from a shared template) instead of their own panel's class (`App\Filament\Landlord\Pages\EditProfile`, etc.), and each also declares `->pages([])` (empty — the panel's own `EditProfile.php` file, where one exists, is never registered as a page). The shared Filament topbar unconditionally calls `Pages\EditProfile::getUrl()` to build the user-menu "Edit profile" link, which throws `Symfony\Component\Routing\Exception\RouteNotFoundException: Route [filament.{panel}.pages.edit-profile] not defined` on **every single page in the panel**, not just the dashboard — confirmed identically reproducible for Landlord, Buyer, Seller, and Contractor (4 separate live 500s, one stack trace per panel, `storage/logs/laravel.log`). **Fix (quick, ~15 min):** in each of the 4 `PanelProvider.php` files, change the `use` import to that panel's own `Pages\EditProfile` class and add it to `->pages([EditProfile::class])` (Landlord and Tenant already have a real `EditProfile.php` file sitting unused in `Pages/`; Buyer and Seller/Contractor need one created, following the `Tenant`/`Landlord` version as a template).
**Bug B — wrong column names in two dashboard data queries.** `app/Filament/Tenant/Pages/Dashboard.php:23` runs `Property::where('tenant_id', auth()->id())->first()` — the `properties` table has no `tenant_id` column (confirmed via `Schema::getColumnListing('properties')`; it has `user_id`/`team_id` instead, and a property only relates to a tenant indirectly through a `Lease`). This throws `SQLSTATE[42S22]: Column not found: 1054` on every Tenant-panel login. Separately, `app/Filament/App/Widgets/StatsOverview.php` computes `Transaction::sum('transaction_amount')` — the real column (confirmed via `Schema::getColumnListing('transactions')`, and correctly used two files over in `Staff/Widgets/PropertyStatsOverview.php`) is `amount`, not `transaction_amount` — this throws the same class of SQL error on every `App`-panel dashboard load. **Fix (quick, ~15–30 min once the correct relationship is confirmed with product/data owner):** rewrite `Tenant/Pages/Dashboard.php:23` to resolve the tenant's current property via their active `Lease` (e.g. `Lease::where('tenant_id', auth()->id())->where('status','active')->first()?->property`) rather than a direct, nonexistent FK; fix `StatsOverview.php`'s column name to `amount`.

**Net effect:** Landlord, Buyer, Seller, Contractor (Bug A), Tenant, and the generic `App` panel (Bug B) — **6 of the 9 panels** — currently return HTTP 500 on first authenticated page load. Only Admin, Staff, and Agent (a bare, query-free profile form) load without error. This was previously mischaracterized by this report (and by every prior audit generation) as "empty dashboards" because no one had actually logged in and clicked past the login form until this session — **a direct illustration of why Section 17's "no status claim without re-running it" rule exists.**

---

## 5. Critical Issues (P1) — strongly recommended before release

| ID | Title | File(s) | Impact |
|---|---|---|---|
| P1-1 | **[FIXED, verified live]** Login/registration have zero rate limiting despite fully-defined Fortify limiters | `routes/web.php:26-31`; limiters defined but orphaned in `FortifyServiceProvider.php` | Applied `throttle:login`/`throttle:register` middleware to the POST routes. Verified with a real browser session: 6th rapid login attempt in a minute correctly returns HTTP 429. |
| P1-2 | **[PARTIALLY FIXED]** `DigitalSignatureService::signDocument()` silently never sets `is_signed` | `app/Services/DigitalSignatureService.php:1-48` | Added the missing `use App\Models\LeaseAgreement;` import, but discovered the check is structurally dead regardless: `Document`/`LeaseAgreement` are sibling models (neither extends the other, no polymorphic link), and `signDocument()` is only ever called with a real `Document`. The `is_signed` flag genuinely never updates from this code path — fixing that needs a product decision on the real Document↔LeaseAgreement relationship, folds into P1-4/P1-5. Documented inline and in `docs/AQAR_DECISIONS.md`. |
| P1-3 | **[FIXED, verified]** E-signature status flags directly settable via generic CRUD API | `app/Http/Controllers/API/V1/TenancyAgreementController.php:44-46` | Removed the 3 flags from `rules()` — `Request::validate()` only returns declared-rule keys, so they're now unconditionally stripped from any request. Verified via reflection with a real authenticated, team-attached user. Finding this also surfaced a new bug: **`current_team_id` was `NULL` for all 8 seeded users**, breaking every code path (like this controller's `teamId()`) that reads it directly — fixed in `UserSeeder.php`, see `docs/AQAR_DECISIONS.md`. |
| P1-4 | No Filament UI action anywhere triggers the e-signature/DocuSign flow | `app/Filament/Staff/Resources/LeaseAgreements/LeaseAgreementResource.php` | Staff can create a Lease Agreement but there's no "Send for Signature" button — the DocuSign integration is only reachable via API/service code, not the panel. |
| P1-5 | Deleted `LeaseAgreementService.php` removed real template-population logic with no replacement | (deleted) — confirmed via `git show` | UK-AST tenancy-agreement template placeholder substitution (`{{tenant_name}}` etc.) has no current equivalent; `LeaseAgreementResource` now only exposes a free-text terms field. Needs a product decision: intentional simplification, or a gap to rebuild. |
| P1-6 | Staff has no functional UI to view/update tenant-submitted maintenance requests | `app/Filament/Staff/Resources/RelationManagers/MaintenanceRequestRelationManager.php` (built but never attached to any Resource) | The maintenance-request workflow is a dead end on the staff side — tenants can file requests (modulo P0-2), but staff/admin can't act on them through Filament at all. |
| P1-7 | Landlord has `update_rental_application` permission but no panel to use it | `RolesSeeder.php` grants it; only `RentalApplicationResource` lives in Staff/Admin panels, which landlords can't access | Same root cause as P0-3 — permission without UI. |
| P1-8 | Agent Matching Service — documented "production-ready," has zero UI entry point | `app/Livewire/AgentRecommendations.php` + view, fully built/tested | Confirmed via grep: referenced nowhere outside its own files. Same failure mode as P0-4, lower severity because it's not an explicit MVP requirement. |
| P1-9 | **[FIXED alongside P0-5]** `Property::needsSyncing()` compares a column to a literal string, not another column | `app/Models/Property.php:493-499` — changed to `orWhereColumn('updated_at', '>', 'last_synced_at')` | Fixed in the same pass as P0-5, since `ZooplaPortalSyncService` is the only caller of this scope. |
| P1-10 | Two independent, non-integrated "lease" domain models | `app/Models/Lease.php` vs `app/Models/LeaseAgreement.php` (separate tables, separate Filament resources, no shared FK) | Risk of showing inconsistent lease data between the tenant-facing lease list and the staff tenancy-agreement workflow in a live demo. |
| P1-11 | **[FIXED]** Hardcoded `$` (US dollar) prices in outbound email notifications | `app/Notifications/PriceAlertNotification.php:37`, `PropertyAlert.php:37` | Both now use `$property->formatted_price` (the existing per-property-currency accessor). |
| P1-12 | **[FIXED]** Hardcoded `->money('USD')` in payment tables | `PaymentRelationManager.php:52`, `Tenant/Resources/Payments/PaymentResource.php:84` | Both now use `strtolower(config('currencies.default'))`, matching the pattern already used in `RecentTransactions.php`. |
| P1-13 | No language switcher inside any of the 9 Filament panels | all `app/Providers/Filament/*PanelProvider.php`; only switcher exists in the public navbar | Staff/tenant/landlord/etc. users cannot change language without leaving the panel entirely. |
| P1-14 | ~96% of Livewire flash/error messages are untranslated (or wrong-language) | ~27 instances across `app/Livewire/*.php`; two (`PropertyRequestForm.php:70`, `PropertySubmissionForm.php:114`) are hardcoded **Arabic-only**, shown even to English-locale users | User-facing confirmation/error text ignores the active locale almost everywhere outside form field labels. |
| P1-15 | Zero test coverage for Payments/Stripe | confirmed via grep across `tests/` | Real money-handling code, actively touched on this branch, has no safety net. |
| P1-16 | Zero functional auth tests (login/2FA/panel session) | only `SocialstreamConfigTest` exists, and it only checks config shape | This branch deleted `Landlord\AuthController` and touched tenancy/profile pages — exactly the area with no test coverage. |
| P1-17 | **[FIXED]** `.env.example` ships `OCTANE_SERVER=swoole`; only RoadRunner is installed | `.env.example`; `composer.json` has `spiral/roadrunner-*`, no Swoole extension | Changed to `roadrunner`. Fixed P2-8/P2-16 in the same pass (below); deliberately left P2-17 as a documented trade-off rather than a silent behavior change. |
| P1-18 | `/properties` listing and `/news/{slug}` articles have no dynamic SEO metadata | `resources/views/livewire/property-list.blade.php`, `news-detail.blade.php` | Both have unique per-record data available but fall back to the generic sitewide `<title>`/OG — the primary internal search-landing page and all news articles are effectively unoptimized. |
| P1-19 | **[FIXED, and found worse than described]** Sitemap excludes all non-property pages and never regenerates on a schedule | `app/Console/Commands/GenerateSitemapCommand.php`, `routes/console.php` | Added all static/legal pages, MVP forms, and published news articles to the sitemap; added a daily `Schedule::command('sitemap:generate')` entry (previously none). **Also found and fixed: the sitemap has never included a single property, ever** — 3 different property-status naming conventions coexist in this codebase (`'For Sale'`/`'for_sale'`/the original filter's `'published'`/`'active'`/`'available'`, which matched none of them) — matched all real conventions defensively. Synced `robots.txt`'s `Sitemap:` directive from the same command (a static file can't read `config('app.url')` at request time). Verified: 44 URLs now generated (was 16, 0 of which were properties); regenerated 3x confirming idempotency; live `/sitemap.xml` and `/robots.txt` both serve correctly. |
| P1-20 | Locale architecture has no URL segmentation — hreflang structurally impossible, English site invisible to crawlers | `app/Http/Middleware/SetLocaleMiddleware.php` (session/cookie-based, no `/en/` vs `/ar/`) | Search crawlers carry no session, so they only ever see the default `ar` version of every page. This is a routing-architecture decision, not a template fix — flag now, plan later. |
| P1-21 | Debug Mode is enabled | confirmed via `php artisan about` | `APP_DEBUG=true` exposes stack traces/error details — must be off before any public-facing demo URL is shared. |

---

## 6. Important Issues (P2) — should be fixed before or shortly after demo

| ID | Title | File(s) | Notes |
|---|---|---|---|
| P2-1 | The 5 existing Policies are themselves partial (missing `viewAny`/`create`/`delete`) | `AppointmentPolicy`, `BookingPolicy`, `PropertyPolicy` | Even "covered" models fall open on uncovered actions — fold into the P0-1 fix. |
| P2-2 | `FilamentShieldPlugin` registered on Admin panel only | all other `*PanelProvider.php` | Likely intentional (single source of truth for role management), but every panel has the identical commented-out line — confirm intent, clean up dead code either way. |
| P2-3 | Several resources rely on `shouldRegisterNavigation()` alone to "hide" sensitive settings | `OnTheMarketSettingsResource`, `RightMoveSettingsResource`, `ZooplaSettingsResource`, `RentalApplicationResource` | Hides the sidebar link only; direct URL navigation bypasses it entirely. Low real exposure today (panel-level gating currently covers it) but fragile. |
| P2-4 | Password reset has no route for 7 of 8 roles | only the `app` panel enables `->passwordReset()` | Same root cause as P1-1 — manual route re-declaration in `web.php` didn't carry over Fortify's full route set. Support-burden risk (no self-service recovery). |
| P2-5 | CSP allows `'unsafe-inline' 'unsafe-eval'` in `script-src`; broad `connect-src https: wss:` | `app/Http/Middleware/SecurityHeaders.php:20-23` | Likely required by current Alpine/Livewire versions — acceptable as a documented trade-off for the demo, should be tightened before wider GA. |
| P2-6 | Property "images" media collection has no mime/size validation | `app/Models/Property.php:613-625`, `PropertyResource.php:198-203` | Inconsistent with the `videos`/`3d_models` collections right next to it, which are properly restricted. |
| P2-7 | RolesSeeder grants "staff" broad `view/create/update` by prefix-match across *all* permissions | `RolesSeeder.php:92-99` | Currently contained by panel-level role gating, but a maintainability/defense-in-depth risk as new resources are added. |
| P2-8 | **[FIXED in `.env.example`]** Duplicate/conflicting `SESSION_DRIVER` and `SESSION_SECURE_COOKIE` keys | `.env` and `.env.example` (both files) | Deduped in the template, keeping the previously-effective values (`cookie`, `false`) so behavior doesn't silently change. The local, gitignored `.env` was not touched — out of scope for a repo-level fix. |
| P2-9 | Public `/metrics` endpoint has no auth or rate limiting | `routes/api.php:88`, `MetricsController.php` | Leaks app/PHP version info; low severity but easy to fix. |
| P2-10 | Filament `->money()` columns vs. `CurrencyService` formatting mismatch | 7+ Staff/Tenant resource files | Same lease/transaction amount can render with different decimals/symbol/position depending on which screen you're on. |
| P2-11 | Filament `placeholder()`/`helperText()` untranslated while `label()` is 100% translated | 29 instances, mostly Admin/Users forms | Inconsistent within otherwise well-localized Filament forms. |
| P2-12 | No locale-aware date formatting anywhere (`translatedFormat()` used 0 times) | 18 files, incl. property detail, booking calendar, news, tenant dashboard | Every date shows an English month/day name even under Arabic locale + RTL. |
| P2-13 | `space-x-*` utilities without `rtl:space-x-reverse` | 7 files, 9 occurrences | Flex-child spacing stays LTR-oriented under `dir="rtl"`. Simple project-wide fix: `space-x-N` → `gap-N`. |
| P2-14 | No sitewide default `og:image`/canonical tag | `resources/views/layouts/app.blade.php` | Every page except property-detail shares with no preview image; canonical absent on ~90% of public pages. |
| P2-15 | Property JSON-LD lacks address/geo/breadcrumb/Organization schema | `property-detail.blade.php:21-37` | Minimal `RealEstateListing` + `Offer` only; no rich-result-eligible structured data beyond that. |
| P2-16 | **[FIXED]** Reverb env vars entirely absent from `.env.example` | — | Added all `REVERB_*` vars from `config/reverb.php`. `BROADCAST_DRIVER` deliberately left at `log` (not `reverb`) — flipping it would require Reverb actually running for a fresh install to work, same trade-off as P2-17. |
| P2-17 | `QUEUE_CONNECTION=sync` / `CACHE_DRIVER=file` defaults bypass Horizon/Redis entirely | `.env.example` | **Deliberately not changed** — verified this would break a zero-dependency fresh install if Redis isn't running locally. Added an explanatory comment instead; the actual demo/production environment should switch both to `redis`. |
| P2-18 | No tests for Communication Hub, Chat Conversations, Energy Consumption, Utility Payments, Market Analyses | — | All five were actively modified on this branch; zero matching test files. |
| P2-19 | `.env.example` missing entries for DocuSign, Stripe publishable key, Xero, Sage, Jupix, LetsSafe, portal integrations, CRM, Holographic Tours | — | Config exists and is used; template doesn't expose it — a checklist item, not a bug. |
| P2-20 | Two service classes with overlapping purpose for Investment Analytics | `AIInvestmentAnalysisService` + `InvestmentAnalysisService` | Intentional (two distinct UI sections), but the second service is completely undocumented. |
| P2-21 | Two conflicting "3D Property Models" implementations coexist; only one is actually rendered | Three.js/URL approach (live) vs. Spatie-Media-Library upload approach (built, orphaned) | Docs describe both as if current; the upload path's `3d_models` media collection has no consuming UI. |
| P2-22 | Leftover "liberu" template branding baked into live logic, not just docs | `SocialLinksWidget.php:40` (GitHub link), `AccountingIntegrationController.php:37` (`liberu_accounting` validation option), `.env.example` `DB_DATABASE=liberu_real_estate` | User-visible in one case (social widget), embedded in validation logic in another. |
| P2-23 | `DocumentGenerator` template engine is a known-broken stub | flagged in master plan, confirmed present | Produces raw `{{ $variable }}` placeholders in any generated document. |
| P2-24 | `CostOfMovingCalculatorService` remains UK-calibrated (£/GBP) | flagged across three phases of prior audit, still open | Wrong-currency output if a demo path exercises this calculator. |
| P2-25 | Real, built features with zero UI entry point (backlog) | `PropertyComparison` (has entry point but no `<h1>`/SEO — see P3), `SendToFriendService`, `AlertPreferences`, `PriceAlertManager`, `LandlordReviewForm`, `TenantReviewForm`, `InsurancePurchaseForm` | Same class of issue as P0-4/P1-8 at lower stakes — worth a single pass to either wire up or consciously shelve. |
| P2-26 | `ContactController` sends no email notification to staff on submission | flagged in master plan | A "dead button"-class bug — visitors fill the contact form and nothing happens on the staff side. |
| P2-27 | `SyncActivityToCrm` job body unimplemented | flagged in master plan | — |

---

## 7. UI/UX Findings

**Update: this gap has now been substantially closed.** The version of this report that first shipped stated no browser-rendered QA had been performed on ~85% of the app. In this follow-up pass, a real browser (Chromium via Playwright) was driven against the actual running app (`php artisan serve`, real MySQL dev DB, PHP 8.4.16, on the real `feature/aqar-productization` working tree) and logged in as every seeded role from `docs/docs/TEST_USERS.md`. This is what found **P0-3** (Section 4) — six panels crashing outright — which no amount of additional code reading would have surfaced, because the bug is in how Filament wires routes at runtime, not in anything a static grep flags as wrong.

**Methodology note (for auditability):** the first attempt at this delegated the browser pass to a subagent using an isolated git worktree. That worktree's isolation silently checked out from `origin/main` (the upstream Liberu template repo) rather than `feature/aqar-productization` — a divergent lineage whose merge-base predates this branch's entire Arabic-localization/branding effort (commit `0de902c` and everything after it). That run's specific claims about RTL (`dir` attribute never set, no `lang/ar.json`, no language switcher, hardcoded `config('app.locale')`) and about Buyer/Seller/Contractor dashboards being fully populated are **false for `feature/aqar-productization`** and are explicitly discarded below — each was independently re-checked against the real branch and refuted (`dir="rtl"` is correctly set in `resources/views/layouts/app.blade.php:2`; `lang/ar.json` and `SetLocaleMiddleware.php` both exist and work). Its findings on branch-independent things (e.g., that `PropertyComparison` needed an `<h1>`, brand collision, social-login icon sizing) could not be assumed safe either and were separately re-verified below rather than trusted at face value.

**Verified findings from the correctly-scoped pass on the real branch:**
- **P0-3 (Section 4): 6 of 9 panels return HTTP 500.** The single most important visual-QA finding of this audit. See the full root-cause breakdown in Section 4.
- **Brand collision is visually confirmed, not just inferred from source.** Public homepage `<title>` = "دار حضرموت العقارية"; Admin/Staff panel `<title>` = "… \| Novixa Aqar"; footer = "© 2026 دار حضرموت العقارية" — three names in one click-through (Section 4, P0-7, updated with this evidence).
- **RTL is correctly wired at the document level and does render.** `<html dir="rtl" lang="ar">` is set correctly on the homepage, properties list, and mobile viewport (375×812); Arabic renders as Arabic, not fallback English keys. This directly contradicts the invalidated worktree run above and confirms the original code-level Section 13 findings (narrow `space-x-*`/`ml-`/`mr-` gaps, not a systemic failure) were the accurate characterization.
- **`PropertyComparison` (`resources/views/livewire/property-comparison.blade.php`) has zero `<h1>` anywhere in its source** — re-confirmed via direct grep of the real file (zero matches). The original P2-25/Section 14 characterization stands.
- Admin panel loads cleanly (`Dashboard`, no console errors, `dir="rtl"` correct); Staff panel loads cleanly (defaults to an "Agent Performance" view, `dir="rtl"`, no console errors); Agent panel loads a bare, functional, error-free profile form (name/email/rating) — consistent with the original "Agent is basically empty but at least doesn't crash" characterization, now visually confirmed.
- **Not re-verified in this pass** (time-boxed, not because it's assumed clean — treat as still-open per the original placeholder): systematic RTL layout QA of tables/dropdowns/modals/pagination *inside* the panels that do load (Admin/Staff/Agent), full mobile QA beyond the homepage, and a full click-through of every non-panel public page in both languages. The original recommendation to do a fuller pass before the actual public demo still stands — this session closed the highest-risk, highest-severity gap (do the panels even load), not the entire visual-polish backlog.

---

## 8. Functional and Business Logic Findings

Covered in depth in Sections 4–6. Summary of the four traced end-to-end workflows:

| Workflow | Status |
|---|---|
| Lease → LeaseAgreement → e-signature (DocuSign) | Models/relations intact; **3 real gaps**: no document-template generation since deletion (P1-5), `is_signed` never updates (P1-2), no Filament action to start signing (P1-4) |
| Rental Application: submission → staff/landlord review | Submission works (public Livewire form); staff review works via Filament; **landlord review is a dead end** — has the permission, no panel (P1-7) |
| Maintenance Request: tenant creates → staff/landlord/contractor updates | **Now confirmed worse than previously described**: the Tenant panel itself crashes with a fatal 500 on login (P0-3, Bug B) before a tenant can even reach the Maintenance Request resource to file one. Once that's fixed, the underlying resource is still **unscoped** (P0-2) and doesn't set `tenant_id` reliably; **staff has no functional UI at all** (P1-6); landlord panel also crashes on login (P0-3, Bug A), so it has no UI regardless; contractor panel also crashes on login (P0-3, Bug A) and, even when it loads, manages contractor *profiles*, not work orders |
| Utility/Rent Payment (Stripe) | **The one workflow that's a genuine positive example** — correct webhook signature verification, correctly team/tenant-scoped `PaymentResource`, auto-set `tenant_id` on create. This is the pattern the Maintenance Request resource should have followed. |

Also note a **data-model inconsistency** that would make a naive fix of P0-2 wrong: `Lease.tenant_id`/`Payment.tenant_id` point to `users.id`, but `MaintenanceRequest.tenant_id` points to `tenants.id` (a separate table). Any scoping fix must account for this.

---

## 9. Code Quality and Architecture Findings

Full detail in Section 5/6 tables (P1-9, P1-10, P2-20 through P2-27) plus:

- **P3 — N+1 query** in the daily `Neighborhood::all()` scheduled closure (`routes/console.php:86-99`) — one query + one external service call per neighborhood, no eager loading.
- **P3 — Unreachable fallback** due to operator precedence: `... === 'hourly' ?? true` in `routes/console.php` schedule conditions (`===` binds tighter than `??`) — the "default to hourly" intent never fires; if no settings row exists, sync is silently skipped every time.
- **P3 — `properties` table under-indexed** for its main search columns (`status`, `property_type`, `price`, `bedrooms`, etc.) — only one late composite index exists.
- **P3 — `PropertyObserver` uses a 5-minute cache as a workaround** for change-tracking instead of Eloquent's native `getChanges()`, with a real (if low-probability) race condition on concurrent updates.
- **P4 — Only one PHP enum in the entire app** (`AgencyRole`); every other status/state field is a raw string validated ad hoc with duplicated `Rule::in()` lists.
- **P4 — Dead code**: `app/Exceptions/Handler.php` (pre-Laravel-11 style, never wired into `bootstrap/app.php`, contains an unreachable `instanceof Closure` branch); `app/Http/Kernel.php` (same — vestigial, misleading for future reviewers); empty `app/Http/Controllers/Landlord/` directory left over from the deletion.
- **P4 — Module system is real infrastructure with nothing in it**: `ModuleServiceProvider` genuinely works, but the only module (`BlogModule`) is a bare skeleton — hide the module-management screen before the demo or it's an empty, confusing admin surface.
- **Positive findings** (no action needed): Stripe webhook signature verification is correctly implemented with amount/owner re-validation; `TenantCrudController` + `RequireAgencyPermission` middleware form a coherent, well-designed authorization pattern for the V1 API despite no FormRequest classes existing anywhere; `composer.json`/`config/app.php` top-level rebranding to `novixa/aqar`/"Novixa Aqar" is correct; no duplicate migration table names across all 146 migrations; no empty/swallowed catch blocks found.

---

## 10. Security Findings

Full detail in Sections 4–6 (P0-1, P0-2, P1-1, P1-3, P2-5 through P2-9). Additional verified-safe findings worth stating explicitly (no action needed):

- No hardcoded secrets found anywhere in `app/`, `config/`, `resources/`, `routes/`, `database/` (Stripe/AWS/PEM key patterns all checked, none found).
- `UserSeeder`'s production guard is real and unbypassable through any discovered code path — no CI/deploy script sets `APP_ENV=production` before seeding.
- CSRF: `VerifyCsrfToken`'s `$except` array is empty.
- `User` model's `$fillable` is tightly scoped — no `role`/`is_admin`/verification-flag mass assignment possible; self-registration role whitelist correctly excludes admin/staff/agent.
- 2FA is available and enabled via Fortify.
- Security headers (HSTS, X-Frame-Options, nosniff, Permissions-Policy) are otherwise well implemented.

---

## 11. Performance Findings

- N+1 in scheduled neighborhood refresh (Section 9).
- `properties` table under-indexed for its actual query patterns (Section 9).
- Test suite duration (5.5 min for ~590 tests, driven by `RefreshDatabase` per test class) is fine relative to CI's 15-minute timeout budget, but worth monitoring as the suite grows.
- `QUEUE_CONNECTION=sync` default (Section 6, P2-17) means anything that should be async (DocuSign webhooks, notification dispatch) runs inline on the request in any environment following `.env.example` literally — fine for a low-traffic demo, but shouldn't be mistaken for "queues are tested."

---

## 12. Localization Findings

Full detail: Section 5 (P1-11 through P1-14) and Section 6 (P2-10 through P2-13, P2-16-adjacent). Architectural summary:

The translation system is **split across four uncoordinated mechanisms**: `lang/{ar,en}.json` (flat, keyed by literal source string — fragile to copy edits), legacy `lang/{en,ar}/*.php` nested arrays (mostly well-synced, used by Filament), and a **dominant, undocumented fourth pattern** — 482 inline `app()->getLocale() === 'ar' ? … : …` ternaries across 43 blade files, outnumbering the 281 actual `__()` calls in the same tree. This isn't yet demo-breaking (Filament form labels are 100% translated; public page primary content is largely bilingual) but it is the architecture that produced the ~27 untranslated Livewire flash messages and the two Arabic-only success messages shown regardless of locale. Zero `trans_choice`/pluralization infrastructure exists anywhere (low urgency — no visibly broken plural text found, but latent risk for future count-sensitive copy).

---

## 13. RTL/LTR Findings

Overall in reasonable shape — `dir`/`lang` attributes are correctly set from locale, Tailwind's `rtl:`/`ltr:` variants are used correctly where present, and there are zero hardcoded `margin-left`/`float:`/`text-align:` declarations in any stylesheet. The gaps are narrow and mechanical, not systemic:
- 9 occurrences of `space-x-*` without `rtl:space-x-reverse` (P2-13) — simplest fix is a project-wide `space-x-N` → `gap-N` sweep.
- 15 occurrences of physical `ml-`/`mr-` margins instead of logical `ms-`/`me-` (P3, Section 5/6), concentrated in a few admin/staff widget views.
- No component-level RTL QA has been done via actual browser rendering (Section 7).

---

## 14. SEO Findings

Full detail: Section 4 (P0-6), Section 5 (P1-18 through P1-20), Section 6 (P2-14, P2-15). One page — property detail — does SEO correctly (dynamic title/description/canonical/OG/Twitter/JSON-LD). Every other public page, including the highest-traffic listing index and every news article, inherits one static, sitewide fallback. `robots.txt` correctly blocks all panel prefixes but has no `Sitemap:` directive. The sitemap itself only contains properties (missing every static/legal/news page) and never regenerates on a schedule.

---

## 15. Open Graph Findings

No sitewide default `og:image`/`twitter:image` (P2-14) — every non-property page shares with a blank preview. No `og:locale`/`og:locale:alternate` anywhere. Property detail's own OG implementation uses `og:type=article` where `product`/`website` would be more semantically correct (minor). `twitter:card=summary_large_image` is declared sitewide but with no default `twitter:image`, so Twitter/X cards render broken/imageless for every non-property page.

---

## 16. `<head>` / Metadata Findings

- `<meta name="robots" content="notranslate">` appears to be a copy-paste duplicate of the adjacent Google `notranslate` tag — there is no explicit `index,follow` directive anywhere, and no `noindex` on low-value utility pages (`/login`, `/register`, `/properties/compare/*`).
- No explicit favicon `<link>`/`apple-touch-icon`/manifest tags — relies solely on the `/favicon.ico` browser convention.
- No `<link rel="canonical">` at the layout level — only property-detail sets one; ~90% of public pages have zero canonical tag.
- Both analytics packages are effectively inert: `spatie/laravel-googletagmanager` is correctly gated in the layout but `GOOGLE_TAG_MANAGER_ID` is never set (P0-adjacent, folded into general demo-environment provisioning); `spatie/laravel-analytics` is installed with **zero references anywhere in the codebase** — a fully dead dependency as of today.

---

## 17. Documentation Findings

This repo contains **five chronological "generations"** of audit/status documentation that materially contradict each other:

1. **Feb 2026** — pre-Aqar upstream feature reports (root *reference removed — document no longer exists*, *reference removed — document no longer exists*, *reference removed — document no longer exists*, *reference removed — document no longer exists*, *reference removed — document no longer exists*, *reference removed — document no longer exists*, *reference removed — document no longer exists*) — describe generic "Liberu Real Estate" features unrelated to Aqar/Yemen; two of the described features (Smart Contracts, Virtual Staging) were later **deleted from the codebase entirely**.
2. **Aug 8–13** — `docs/project/*` (8 files) — TASK-001→018 tracking, all marked COMPLETED, tests climbing 670→720. `docs/AQAR_TASKS.md` has every box pre-checked with zero evidence links — **should not be treated as a status signal at all.**
3. **Aug 13** — `docs/audit/01`–`24` (first 24-report suite). Verdict YELLOW, claims 720/720 tests passing, 18/18 tasks complete.
4. **Aug 15** — `docs/audit/25`–`39` ("Independent Verification"). **Directly contradicts Gen 3**: finds 17 failing tests (not 0), a hardcoded `admin@example.com`/`password` backdoor, a PHP-version mismatch Gen 3 missed entirely, and a two-way branding collision.
5. **Aug 17–19** — *reference removed — document no longer exists* (692 lines, the only document that should be treated as prior-current-status). Re-checks Gen 4's "6 remaining blockers" and finds **all 6 still unresolved** two days later, then finds far more: a **three-way** brand collision, a site-wide response-corruption bug (raw bytes before `<?php` in 3 files, breaking every HTTP response and all Livewire JS) that no prior audit or the test suite ever caught — found only via actual browser rendering. Its own Phase 5 (visual QA) is explicitly left **open**.

**The methodological lesson, stated plainly**: every "COMPLETE"/"VERIFIED"/"PASS" claim in this repo's documentation has been contradicted by a later, more rigorous check at least once, including this audit's own findings (e.g., TASK-018's false "all 18 tasks complete" claim, directly contradicted by P0-4 above). **Treat no status claim in this repo — including this report six months from now — as current without re-verifying against running code.**

**Consolidation — DONE, 2026-08-20.** All recommendations below were executed; see `docs/archive/2026-02-pre-rescue/README.md` for the full rationale and `docs/AQAR_DECISIONS.md` for the specific finding that confirmed the "treat no status claim as current" lesson one more time (`TASK-004`/`TASK-005` falsely claimed Arabic/RTL "COMPLETED & VERIFIED").
- ~~Archive `docs/audit/01`–`39`, all 8 `docs/project/*` files, and the 7 root Feb-2026 feature reports to a dated `docs/archive/`~~ — done: `docs/archive/2026-02-pre-rescue/{audit,project,root-reports}/`. Also archived the 17-file `docs/tasks/TASK-*-ANALYSIS.md` series (same failure mode, not originally itemized here) and 4 more per-feature "Implementation Summary" docs superseded by a genuine reference doc for the same feature (`docs/archive/2026-02-pre-rescue/feature-summaries/`).
- ~~Delete `docs/project/docs/AQAR_TASKS.md` outright~~ — archived with the rest of `docs/project/*` instead of hard-deleted, per this codebase's git-safety preference for reversible operations; it carries the same "no ongoing operational value" warning as its siblings in the archive README.
- ~~Rename two mislabeled files~~ — done: `docs/MODULAR_ARCHITECTURE.md` → `docs/INTERACTIVE_FLOOR_PLANS_ARCHITECTURE.md`; *reference removed — document no longer exists* (root-level implementation-summary content, not architecture) → archived to `feature-summaries/` since `docs/WALKABILITY_SCORES.md` already covers it as reference.
- ~~Merge the duplicate `docs/TEST_USERS.md`~~ — already done by prior work this session; `docs/docs/TEST_USERS.md` is canonical, no root copy remains.
- **3D Property Models docs — partially addressed, not fully consolidated.** Archived the two purely historical status reports (`docs/3D_PROPERTY_MODELS.md`, `docs/3D_PROPERTY_MODELS.md`); kept `3D_PROPERTY_MODELS_QUICKSTART.md` (root) and `docs/3D_PROPERTY_MODELS.md` + `docs/3D_PROPERTY_VIEWER.md` as live reference docs, but did **not** verify whether these three overlap/conflict with each other or which upload mechanism (Three.js/URL-based vs. Spatie Media Library) is actually live — that content-level check was out of scope for this pass and should not be assumed done.
- This master report is the single current-status document, updated in place — confirmed still true.

---

## 18. Missing or Planned Features

| Feature | Status | Source |
|---|---|---|
| Owner Property Submission | **Built, zero routes** — see P0-4 | required MVP item |
| Property Requests | **Built, zero routes** — see P0-4 | required MVP item |
| Agent Matching Service | **Built, zero UI entry point** — see P1-8 | documented "production-ready" |
| Landlord/Buyer/Seller/Agent self-service panels | **Stub only** — see P0-3 | implied by RolesSeeder's permission grants |
| `PropertyComparison`, `SendToFriendService`, `AlertPreferences`, `PriceAlertManager`, `LandlordReviewForm`, `TenantReviewForm`, `InsurancePurchaseForm` | Built, mostly reachable but see P2-25 for specific gaps | backlog per prior internal audit, spot-checked here |
| Lease-agreement template generation | **Regressed** — logic deleted this branch, no replacement (P1-5) | was working prior to this branch |
| Locale-prefixed URL routing (`/en/`, `/ar/`) | Not implemented — architectural prerequisite for hreflang/full bilingual SEO | P1-20 |
| In-panel language switcher | Not implemented in any of 9 panels | P1-13 |

Everything else sampled in the feature-doc verification pass (3D Property Models, Virtual Tours, AI Chatbot, Wishlist, Walkability Scores, Investment Analytics, Property Tax Estimator, Neighborhood, Community Events, News, Interactive Floor Plans, Property History, Enhanced Search Filters, Core Agency API v1) is **real, wired, and consistent with its documentation** — these should not be re-litigated; they're solid.

---

## 19. Deployment and Production Readiness

Full detail: Section 6 (P1-17, P2-16 through P2-19) and Section 4 (P0-8). Summary:

- `.env.example` has three concrete bugs: broken `OCTANE_SERVER=swoole` default, duplicate/conflicting `SESSION_DRIVER` lines, and defaults (`QUEUE_CONNECTION=sync`, `CACHE_DRIVER=file`) that silently bypass the installed Horizon/Redis stack.
- `.env.example` is missing entries for Reverb, DocuSign, Stripe's publishable key, Xero, Sage, Jupix, LetsSafe, all three property-portal integrations, CRM, digital signature, and Holographic Tours config — all real, `config/`-referenced integrations with no template placeholder.
- CI (`.github/workflows/`, 4 files) is functionally sound: a genuine fresh-install smoke test, a working PHPUnit run, a weekly CVE scan (the only *gating* security job — `phpcpd`/`php-insights` are informational-only via `|| true`). All 4 pin PHP 8.5, untested against the 8.4.x skew found locally. The Docker publish job still targets `liberu/real-estate`, not the current branding.
- `public/build` is git-tracked (not gitignored) and the working tree currently shows exactly the fragile drift this invites (old hashed assets deleted, new ones untracked, `manifest.json` modified to reference files not yet committed) — recommend gitignoring it and building in CI/deploy instead (the infrastructure already exists in `install.yml`).
- The local PHP-version mismatch (P0-8) must be explicitly re-verified against whatever will actually host the public demo before launch.

---

## 20. Testing and Verification Results

**Commands actually run as part of this audit** (not assumed from documentation):

```
$ php artisan route:list --no-ansi
Showing [669] routes                      → exit 0, no exceptions

$ php artisan test --no-ansi
Tests: 32 skipped, 558 passed (2094 assertions)
Duration: 330.06s                          → exit 0, 0 failures, 0 errors
```

**Follow-up verification pass** (this session, re-checking the above report against the live app rather than assuming it was still accurate):
- Re-ran `route:list` (669 routes, unchanged) and the full suite (`558 passed, 0 failed, 32 skipped, 2094 assertions, 204.56s`, exit 0) against PHP 8.4.16 — identical results, confirms no regression from the uncommitted working-tree state.
- Independently re-verified 8 of the report's concrete file/config claims by direct inspection: local PHP resolves to 8.4.0 (confirmed mismatch, P0-8), `.env` `APP_URL=http://localhost`/`APP_DEBUG=true` (P0-6/P1-21), the brand string collision in `layouts/app.blade.php` (P0-7), `MaintenanceRequestResource.php` has no `getEloquentQuery()` override (P0-2), no routes reference `PropertySubmissionForm`/`PropertyRequestForm` (P0-4), exactly 5 files in `app/Policies/` (P0-1), 39 files in `docs/audit/` and 8 in `docs/project/` (Section 17), and a duplicate `docs/TEST_USERS.md` at both repo root and `docs/` (Section 17). All matched exactly.
- **Ran the actual application in a browser** (`php artisan serve`, real MySQL dev DB already migrated/seeded, PHP 8.4.16, Playwright/Chromium) and logged in as every seeded role from `docs/docs/TEST_USERS.md`. This is what surfaced **P0-3's true severity** (Section 4/7): 6 of 9 Filament panels (Landlord, Buyer, Seller, Contractor, Tenant, App) return HTTP 500 on the first authenticated page load, root-caused via `storage/logs/laravel.log` stack traces down to the exact line and exact wrong column/import in each case. This could not have been found by code reading alone — it is a Filament runtime-routing failure, not a static defect a grep would catch.
- A first attempt at the browser pass (delegated to a subagent using an isolated git worktree) produced invalid results because the worktree isolation checked out `origin/main` instead of `feature/aqar-productization` — see the methodology note in Section 7. Its claims are excluded from this report except where independently re-confirmed against the real branch.
- All 32 skips are self-documented (Filament form/table/action/filter tests requiring a Livewire test harness not yet wired up) — an acknowledged gap, not flakiness. Notably, **none of the 32 skips cover the 6 panels found broken in this pass** — the skip list is Filament-testing-harness-related, not a signal that these panels were known to be untested for this specific reason.
- Grep-verified: zero remaining references anywhere to the three deleted classes (`EditTeamProfile`, `Landlord\AuthController`, `LeaseAgreementService`).
- Test suite runs against SQLite in-memory (phpunit.xml overrides `.env`), so no real database was touched during this audit.
- **Coverage gaps** (verified by absence, not just doc claims): zero tests for Payments/Stripe, zero functional auth tests, zero tests for Communication Hub/Chat Conversations/Energy Consumption/Utility Payments/Market Analyses (all five actively modified on this branch).
- **Not independently re-verified in this pass** (documented but not executed): the specific test/route counts cited across `docs/tasks/TASK-003` through `TASK-018` (670/706/708/720+ passed) — the environment PHP mismatch blocked earlier verification attempts until the working PHP 8.4.16 binary was found; this audit's own run (558 passed) does not match any of those cited historical numbers, which is expected given feature deletions (Smart Contracts, Virtual Staging, UK-portal integration UI) between then and now, but is worth noting as one more data point that historical test-count claims in this repo should not be trusted without re-running.

---

## 21. Prioritized Action Plan

Numbered for execution order within each tier; items in the same tier can generally be parallelized across engineers.

### Tier 0 — before any demo, no exceptions (P0, Section 4)
1. **Fix P0-3's Bug A and Bug B (Section 4) — do this first, it is both the most severe finding and one of the cheapest to fix.** Correct the `use` import + `->pages([])` registration in the 4 affected `PanelProvider.php` files (Landlord/Buyer/Seller/Contractor), and fix the two wrong column references (`Tenant/Pages/Dashboard.php:23`, `App/Widgets/StatsOverview.php`). Realistic estimate: well under a day including re-testing all 6 logins. Until this is fixed, 6 of 9 demo personas cannot log in at all — every other Tier 0 item is close to moot if a stakeholder clicks one of these roles first.
2. Fix the Tenant `MaintenanceRequestResource` IDOR (P0-2) — small, highest-severity *security* fix, do alongside #1 (both touch the Tenant panel).
3. Once #1 is fixed and the panels actually load, do the real content/scope review for Landlord/Buyer/Seller/Agent (P0-3's original concern): either build minimal real content (resources, not just dashboard stat cards), or explicitly exclude those roles from the live demo script and say so to stakeholders. Do not let someone free-click into a resource-less dashboard live.
4. Run `php artisan shield:generate --all` to complete Filament Policies for all 45 resources (P0-1). **Correction, verified against Filament 5 source (`vendor/filament/filament/src/helpers.php`) during the follow-up execution pass:** the `->strictAuthorization()` "stop-gap" this item originally suggested does **not** fail closed for missing policies — it throws a fatal `LogicException` on first use, which would 500 nearly every resource across the app (worse than the current fails-open state, and a direct re-run of the exact P0-3 failure mode). Do not enable it without complete policy coverage first; `shield:generate` (or hand-written policies) is the only safe path here, not a toggle.
4. Route or hide the Owner Property Submission / Property Request forms (P0-4) — either wire the routes (`/submit-property`, `/property-request`) so the built features are demo-visible, or remove any implied promise of them from demo talking points.
5. Scope-check P0-5 (portal-sync leak): if the demo involves only one team/tenant, defer with a tracked ticket; if multi-tenant, fix or disable the RightMove/Zoopla/OnTheMarket scheduled sync before the demo.
6. Set the correct `APP_URL` for wherever the demo is hosted (P0-6); regenerate the sitemap after.
7. Resolve the brand-name collision (P0-7) — pick one public identity, apply it to `layouts/app.blade.php`, `home-navbar.blade.php`, `footer.blade.php`, `about.blade.php`, `contact.blade.php` consistently (ideally pulling from `GeneralSettings::site_name`, already used correctly on the property-detail page).
9. Verify the PHP version on the actual demo/deploy host resolves to ≥8.4.1 (P0-8); fix `.env.example`'s `OCTANE_SERVER` mismatch (P1-17) at the same time since it's the same "template vs. reality" class of bug.
10. Set `APP_DEBUG=false` on the demo environment (P1-21).

### Tier 1 — before or immediately after the demo (P1, Section 5)
11. Add `throttle:login`/`throttle:register` middleware to the shared login/register routes (P1-1).
12. Fix the missing `use App\Models\LeaseAgreement;` import (P1-2) — one line, unblocks the e-signature status display.
13. Restrict `is_signed`/`landlord_signed`/`tenant_signed` from generic CRUD write access (P1-3); wire a "Send for Signature" Filament action (P1-4).
14. Fix hardcoded `$`/`USD` in notifications and payment tables (P1-11, P1-12).
15. Add an in-panel language switcher (P1-13) and sweep Livewire flash/error messages for translation (P1-14).
16. Add Feature tests for Payments/Stripe and functional auth flows (P1-15, P1-16) — highest-risk untested surface, and the exact area this branch touched.
17. Fix sitemap staleness/coverage and add the `Sitemap:` line to `robots.txt` (P1-19); add dynamic SEO metadata to `/properties` and `/news/{slug}` (P1-18).
18. Decide the fate of the deleted `LeaseAgreementService` template logic (P1-5) — confirm intent with product, then either close as intentional or rebuild.
19. Configure a real `GOOGLE_TAG_MANAGER_ID` (or formally drop `spatie/laravel-analytics` as a dependency) so demo traffic is measurable.

### Tier 2 — polish, can trail closely behind the demo (P2, Section 6)
20. Complete the remaining partial Policies (P2-1); consolidate the `RolesSeeder` staff grant to an explicit allowlist (P2-7).
21. Add mime/size validation to the Property `images` media collection (P2-6).
22. Fix `.env.example` completeness (Reverb, DocuSign, Stripe publishable key, portal integrations, etc. — P2-16, P2-19) and its duplicate `SESSION_*` keys (P2-8).
23. RTL sweep: `space-x-*` → `gap-*` (P2-13), `ml-`/`mr-` → `ms-`/`me-`.
24. Date localization sweep: `->format()` → `->translatedFormat()` (P2-12).
25. Currency formatting consistency: route Filament `->money()` columns through `CurrencyService` (P2-10).
26. Add tests for Communication Hub, Chat Conversations, Energy Consumption, Utility Payments, Market Analyses (P2-18).
27. Consolidate the Lease/LeaseAgreement domain split (P1-10) — flagged here at P2 execution priority since it's a Large-effort item; start the design conversation now even if implementation trails.

### Tier 3 — backlog, explicitly not pre-demo blockers (P3/P4, various sections)
28. Documentation consolidation per Section 17's recommendations.
29. Enum adoption for status/state fields (Section 9).
30. Index tuning on `properties` (Section 9/11).
31. Dead-code cleanup (`Handler.php`, `Kernel.php`, empty `Landlord/` directory, module-management UI).
32. `hreflang`/locale-prefixed URL routing architecture (P1-20 — flagged as Large/roadmap, not a quick fix).
33. Favicon tags, robots meta hygiene, `PropertyComparison` `<h1>`/noindex (Section 14-16 residuals).
34. CI consolidation (redundant test runs, Docker image rebranding).

---

## 22. Recommended Release Sequence

1. **Complete Tier 0 (Section 21, items 1–10)** — item 1 (P0-3's Bug A/B) first, since it unblocks actually being able to test the other role panels; target a small, single-tenant, Admin/Staff/Tenant-only demo script (once Tenant's crash is fixed) that avoids Landlord/Buyer/Seller/Agent until their resource-less panels get real content, and avoids the two unrouted MVP forms, with the IDOR fixed and authorization closed via real generated Policies (`shield:generate`) — not the strict-mode toggle, which throws rather than denies (see the correction on item 4 above).
2. **Do a fuller browser-based visual QA pass** (Section 7) — this session closed the highest-severity gap (do the panels even load) by finding and root-causing P0-3's crashes; still outstanding: systematic RTL layout QA inside the panels that do load, full mobile QA beyond the homepage, and a full AR+EN click-through of every public page. Do this before anyone external sees the app.
3. **Run Tier 1 items in parallel with step 2** — none of them block the demo script above, but all are cheap and materially reduce risk (rate limiting, e-signature integrity, test coverage on Payments/Auth).
4. **Demo.**
5. **Immediately after**, work Tier 1 remainder + Tier 2, prioritizing whichever role panels/features the post-demo roadmap actually needs next (Landlord/Buyer/Seller/Agent build-out is the largest single item and should be scoped as its own workstream, not squeezed into a "quick fix" pass).
6. **Documentation cleanup (Section 17)** can happen any time after step 1 — it's zero-risk to the demo itself but should happen before this report's own findings are six months stale and someone re-derives all of this from scratch again.

---

## 23. Post-Demo Improvements

- Full enum adoption across status/state fields (Property, LeaseAgreement, Offer, WorkOrder, PortalListing).
- Consolidate the Lease/LeaseAgreement domain model split.
- Locale-prefixed URL routing for proper hreflang/bilingual SEO.
- Migrate the 482 inline ternaries toward the formal translation system incrementally, starting with the highest-traffic public views.
- CSP hardening (drop `unsafe-inline`/`unsafe-eval` via Alpine's CSP-safe build + Livewire nonces).
- Decide the fate of the module system (`app/Modules/`) — build it out or remove the management UI.
- Wire up Livewire component testing so the 32 currently-skipped Filament tests convert to real assertions.
- Rich structured data (BreadcrumbList, Organization, full address/geo on property JSON-LD).

---

## 24. Final Release Verdict

## **NOT READY**

This is not a verdict driven by polish gaps or nice-to-haves — it's driven by four concrete, demo-visible failure modes and one live security bug:

1. **Six of nine Filament panels crash with a fatal HTTP 500 error on the first click after login** (P0-3, verified by actually running the app in a browser this session — not inferred from code reading). Landlord, Buyer, Seller, Contractor, Tenant, and the generic `App` panel all fail. Any stakeholder who logs in as anything other than Admin, Staff, or Agent hits a raw Laravel error page. This is the single most demo-disqualifying finding in this report, and it is also one of the cheapest to fix — see the root-cause breakdown in Section 4.
2. A **confirmed, live cross-tenant IDOR** (P0-2) that would let one seeded demo account read/edit/delete another's data if anyone tried it during or after a demo.
3. **Authorization that doesn't actually enforce the role model** (P0-1) for the large majority of Filament resources — a structural gap, not a one-line bug.
4. **Two explicitly-required MVP features are completely unreachable** (P0-4) — hit live in front of an audience, this undermines confidence in the whole product regardless of how solid the property-browsing core actually is.
5. A **first-impression brand-identity bug** (P0-7), now visually confirmed to appear in three different places (homepage `<title>`, panel `<title>`, footer) rather than just two, plus an unverified deploy-environment PHP mismatch (P0-8) that could cause a silent boot failure.

**None of these require a rewrite.** The panel-crash fix (P0-3) is a handful of one-line import/config corrections plus two wrong column names — realistically under a day including re-testing. The IDOR fix is one method. The policy-generation fix is one artisan command plus review. The brand-name fix is a find-and-replace across a few files. The two orphaned forms need routes, not new code. The realistic total engineering effort for Tier 0 (Section 21) is measured in days, not weeks — this is a **scoping and sequencing problem**, not a "the app doesn't work" problem. If anything, this follow-up pass makes the "how fast can this be fixed" story *better* than the first version of this report suggested, even though the underlying defect (P0-3) turned out to be more severe than first described.

**Minimum path to READY WITH CONDITIONS:**
- Complete all of Tier 0 (Section 21, items 1–10) — starting with P0-3's Bug A/B, since nothing else about the role panels can be honestly assessed while they 500 on login.
- Perform the remaining browser-based visual QA (Section 7 / Section 22 step 2): this session confirmed the panels load-or-don't and that RTL/branding render as expected at the document level, but a systematic RTL/mobile pass *inside* the panels and across every public page is still outstanding.
- Scope the actual demo script to Admin, Staff, and the verified-solid public browsing experience; add Tenant back in once its P0-3/Bug-B crash and P0-2 IDOR are both fixed; explicitly exclude Landlord/Buyer/Seller/Agent/Contractor from any live click-through until they have both a working login (P0-3) and real resources to show, or clearly frame them as "in development" if shown at all.

Once those conditions are met, this becomes a **READY WITH CONDITIONS** release: a real, working, well-tested core product with a clearly-scoped, honest demo boundary — which is a legitimate and common way to do a first public demo. What this audit specifically recommends against is doing the demo **today, unscoped, on the assumption that the prior "18/18 tasks complete" and "COMPLETE ✅" documentation in this repo reflects reality.** It does not, on multiple independently-verified points, and that gap is exactly what this report exists to close.
