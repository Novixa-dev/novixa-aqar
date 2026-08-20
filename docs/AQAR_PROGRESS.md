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
