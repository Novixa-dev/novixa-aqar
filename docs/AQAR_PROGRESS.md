# Aqar Rescue — Progress Log

Chronological. Anyone should be able to read this and know the actual current state without re-deriving it.

---

## 2026-08-20 — Phase 0: Baseline & Safety

**State confirmed:** branch `feature/aqar-productization` @ `f58eac8`, working tree has 48 pre-existing uncommitted changes (unrelated in-progress localization/currency work + 3 file deletions from an earlier live-corruption-bug fix — see `AQAR_PRE_RELEASE_AUDIT_REPORT.md` Section 2). PHP 8.4.16 via `herd/bin/php84/php.exe` (default `php` on PATH is 8.4.0 and fatally errors). MySQL up, dev DB migrated + seeded (`docs/TEST_USERS.md` accounts exist). Node v24.15.0, npm 11.12.1.

**Tests:** `php artisan test` → 558 passed / 0 failed / 32 skipped / 2094 assertions, exit 0. `php artisan route:list` → 669 routes, exit 0.

**Docs created:** `CLAUDE.md`, `docs/AQAR_RULES.md`, `docs/AQAR_EXECUTION_PLAN.md`, `docs/AQAR_TASKS.md`, `docs/AQAR_DECISIONS.md`, this file.

**Discovered blockers:** see `AQAR_PRE_RELEASE_AUDIT_REPORT.md` — 8 P0s, 21 P1s. Most severe: P0-3, six of nine Filament panels return HTTP 500 on first authenticated page load (root-caused to the exact line/import in each case).

**Next:** Phase 1 — fix P0-3A/B/C and P0-2.

---

## 2026-08-20 — Phase 1: P0-3 (panel crashes) and P0-2 (IDOR) fixed and verified live

**P0-3 (6 of 9 panels crashing on login) — FIXED**, commit `2159108`. Root-caused to 3 independent bugs (Bug A: wrong `EditProfile` import + empty `->pages([])` in 5 PanelProviders; Bug B: `properties.tenant_id`/`tenants.id` confusion in Tenant Dashboard + 2 widgets; Bug C, found mid-fix: `transaction_amount`/`transaction_date` vs. real columns `amount`/`date`, present in 6 files across App/Staff panels, far more widespread than the audit report's original single-file citation). Also found and fixed a 4th crash (P0-3D) that only became reachable once P0-3B was fixed: Tenant dashboard blade manually echoed a raw widgets array instead of letting Filament auto-render it.

**P0-2 (cross-tenant IDOR in Tenant Maintenance Requests) — FIXED**, commit `50e1d3f`. Added `getEloquentQuery()` scoped through the `Tenant` record (not `auth()->id()` directly — same tenant_id/user_id confusion as P0-3B). Verified with temporary test data (2 tenants, 1 request each) — tenant 1's scoped query returns only their own record. Test data removed after verification.

**Verification performed:**
- Restarted `php artisan serve` fresh after each fix batch.
- Logged into all 9 seeded personas (`docs/TEST_USERS.md`) plus explicit checks on `/staff/dashboard` and `/staff/transactions` (11 checks total) via Playwright — **0 HTTP errors, 0 JS console errors**, down from 6 of 9 panels returning HTTP 500 at the start of this session.
- Full test suite re-run 3 times across this session (after Phase 5 commit, after P0-3 fix, after P0-2 fix): **558 passed / 0 failed / 32 skipped** every time — no regressions.

**New findings logged** (not fixes, tracked for later): P2-NEW-1 (no seeded `Tenant`/`MaintenanceRequest` records — demo-realism gap, Phase 4), P2-NEW-2 (`tenants` table identity-model observation, see `docs/AQAR_DECISIONS.md`).

**Next:** continue Phase 1 — P0-1 (authorization fails open), P0-7 (brand collision), P0-4 (unrouted MVP forms), then the environment-provisioning items (P0-5, P0-6, P0-8, P1-21).

---

## 2026-08-20 — Phase 1 continued: P0-7, P0-4, P0-1, and P0-5 fixed and verified live

**P0-7 (brand collision) — FIXED**, commit `c0fae89`. Found the site-wide title/footer/about/contact collision the original report described was already resolved by prior work on this branch; the surviving live inconsistency was `terms-and-conditions.blade.php`/`privacy-policy.blade.php` referring to "Novixa Aqar platform" — the one name a visitor would never see anywhere else on the public site. Fixed those two pages' "which service are you using" framing to Dar Hadhramaut, left the legitimate "Powered by Novixa Aqar" attribution and IP-ownership clause alone.

**P0-4 (unrouted MVP forms) — FIXED**, commit `aefbe26`. Routed `/submit-property` and `/property-request`, added discoverable nav entries (`MenuSeeder.php`). End-to-end Playwright verification (fill + submit, check DB for a real created record) caught a second bug beyond routing: `PropertySubmissionForm`'s blade view had no input at all for `year_built`, which validation marked required — every real submission would have failed forever. Fixed by adding the field and relaxing the rule to `nullable` (matching the fallback default already coded in `submit()`); updated the one test that encoded the old broken requirement.

**P0-1 (authorization) — FIXED**, commit `bb0b39a`, the largest fix of the session. Generated real Policies for all 32 unique models via `shield:generate` across all 9 panels. While doing this, found something significantly worse than the original "5 of 45 policied" framing: **every role except super_admin had zero permissions granted, full stop, predating this session** — `config/filament-shield.php`'s permission naming (`View:Property`) never matched `RolesSeeder.php`'s case-sensitive snake_case checks (`view_property`). Fixed via a custom `FilamentShield::buildPermissionKeyUsing()` closure. Also completed the 5 pre-existing hand-written policies (all missing `viewAny`/`create`/`delete` — `PropertyPolicy` had none of the three, which alone would have blocked property browsing for every non-admin role even after the naming fix), and fixed 2 more `RolesSeeder.php` naming mismatches found by auditing every permission pattern against real DB names (`communication_hub`→`message`, `contractor`→`user` — both resources use a different underlying model than their name implies).

**P0-5 (multi-tenant portal-sync leak) — FIXED**, pending commit. `RightMoveService`/`OnTheMarketService`/`ZooplaApiService` all read a single global API key and queried `Property::all()`/`needsSyncing()` with no team filter. Added a `forSettings()` configuration method to each (per-team API key + `team_id` scope), changed the 3 jobs to take a settings row, changed the 3 commands to dispatch one job per active team per frequency tier, and removed the `routes/console.php` `->when()` gates that — like an identical bug in P0-1 — only ever checked the *first* active settings row. `ZooplaSettings` had no `team_id` column at all (its two siblings did) — added via migration. Also fixed the related P1-9 bug (`needsSyncing()` comparing a column to a string literal). **Correction to the original audit's suggested fix**: the existing `PortalIntegration`/`PortalSyncService` system is not a working replacement, just a status-tracking stub with no real external API calls — fixing the legacy services directly was the right call, not routing through it.

**Verification across all four fixes:**
- Full test suite re-run after each fix (5 more runs this session): consistently 559 passed / 0 failed / 32 skipped, one test (`AgentPerformanceReportTest`) updated to seed real permissions instead of a bare role record, now that authorization is actually enforced.
- P0-5 specifically verified with `Http::fake()` + 2 real teams/settings/properties: exactly 2 HTTP calls recorded, each hitting the correct team's own `base_uri` with that team's own `api_key` — zero cross-tenant leakage. Test data cleaned up after (had to `forceDelete()` — `Property` uses soft deletes).
- Direct `Gate::allows()` spot checks confirm P0-1 authorization is correctly restrictive (staff: view/create yes, delete no; buyer: view property yes, create/delete/view-users no) and live browser walkthroughs of all 9 panels + Staff dashboard/transactions show 0 errors, with staff correctly getting 403 on `/admin/users`.

**Remaining P0s: P0-6 (`APP_URL`) and P0-8 (PHP version) are environment-provisioning tasks** — they depend on whatever host actually serves the public demo and can't be meaningfully "fixed" in this repo; they're a pre-launch checklist item, not a code change. All P0s that are genuine code fixes are now done.

**Next:** P1 items (Section 5 of the audit report) — 21 items, none release-blocking but materially affecting reliability/UX/professionalism. Highest-value next: P1-21 (`APP_DEBUG`), P1-1 (rate limiting), P1-15/16 (Payments/auth test coverage).

---

## 2026-08-20 — P1 batch: rate limiting, e-signature import, hardcoded currency

**P1-1 (no rate limiting) — FIXED.** Applied `throttle:login`/`throttle:register` to the manually-declared POST routes in `routes/web.php` — the limiters themselves (5/min login, 3/min register) already existed in `FortifyServiceProvider.php`, just orphaned. Verified live with a real Playwright browser session: attempts 1-5 return 302 (normal failed-login), attempt 6 returns 429.

**P1-2 (e-signature `is_signed`) — PARTIALLY FIXED, honestly.** Added the missing `LeaseAgreement` import, but investigated further and found the check is structurally dead regardless of the import: `Document` and `LeaseAgreement` are sibling models with no shared hierarchy or polymorphic link, and `signDocument()` is only ever called with a `Document` instance (confirmed by checking both call sites). Fixing this for real needs a product decision about the actual Document↔LeaseAgreement signing relationship — documented inline in the service and in `docs/AQAR_DECISIONS.md` rather than claiming a fix that doesn't actually change behavior.

**P1-11/P1-12 (hardcoded `$`/USD) — FIXED.** Notifications now use the existing `$property->formatted_price` accessor (per-property currency); payment tables now use `strtolower(config('currencies.default'))`, matching the pattern already established in `RecentTransactions.php` (Payment has no per-record currency, unlike Property).

**Verification:** full test suite re-run 3x across this batch, consistently 559/0/32. Live: `/tenant/payments` still loads with 0 errors after the currency-column change.

**Next:** continuing through P1 — P1-3/P1-4 (e-signature CRUD exposure + no Filament trigger action), P1-13 (in-panel language switcher), P1-19 (sitemap), P1-21 (`APP_DEBUG` — environment, checklist-only), then P1-15/16 (Payments/auth test coverage, larger effort).

---

## 2026-08-20 — P1-3 fixed; found and fixed a second seeded-data gap (`current_team_id`)

**P1-3 (e-signature flags directly PATCH-able via API) — FIXED.** Removed `is_signed`/`landlord_signed`/`tenant_signed` from `TenancyAgreementController::rules()` — `Request::validate()` only ever returns keys present in its rules array, so these 3 fields are now unconditionally stripped from any request body regardless of what a client sends. All 3 default `false` at the DB level, so `store()` behavior is unchanged. `renew()`'s own direct reset of these flags (legitimate server-side logic, not user input) is untouched.

**Found while verifying it, fixed the same session: `current_team_id` was `NULL` for all 8 seeded demo users.** `UserSeeder.php` attached each user to a team but never called Jetstream's `switchTeam()` to set the "active" team column — any code path reading `current_team_id` directly (confirmed for the V1 API's `teamId()` check) threw "Select an organisation you belong to" for every single seeded persona, including admin. Added `switchTeam()` calls to the seeder; re-ran it against the dev DB; confirmed all 8 users now resolve `current_team_id=1`. Not exhaustively audited for every other call site that might read this column directly — flagged in `docs/AQAR_DECISIONS.md` for a future session.

**Verification:** `rules()` checked via reflection with a real authenticated, team-attached user — all 3 flags confirmed absent, `status` confirmed still present. Full test suite: 559 passed / 0 failed / 32 skipped, unchanged.
