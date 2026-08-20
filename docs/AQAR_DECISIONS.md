# Aqar Rescue — Decision Log

Meaningful architectural/product decisions made during the repair effort, with rationale. Not a changelog — see `docs/AQAR_PROGRESS.md` for that.

---

### 2026-08-20 — Discard the worktree-isolated browser-QA agent's findings

**Decision:** an earlier verification pass delegated browser QA to a subagent with `isolation: "worktree"`. The resulting worktree's merge-base with `feature/aqar-productization` predated this branch's entire Arabic-localization/branding work (it had silently checked out `origin/main`, the upstream template repo, instead of the working branch). Its findings on RTL, localization, and 3 of the 9 panel dashboards were discarded entirely rather than merged into the audit; each was independently re-verified against the real branch instead.
**Why:** trusting findings from the wrong codebase would have poisoned the audit with false claims (e.g., it claimed `dir="rtl"` was never set, which is false on the real branch) and false all-clears (e.g., it claimed Buyer/Seller dashboards were fully populated, which is false — they crash).
**How to apply going forward:** any subagent given `isolation: "worktree"` must have its branch/commit explicitly verified against `feature/aqar-productization` before its findings are trusted. See `CLAUDE.md` Git rules. Prefer working directly in the real working tree for anything needing the actual current code, per the master execution prompt's Section 3.

### 2026-08-20 — New clean task-tracking system instead of extending `docs/tasks/TASK-*`

**Decision:** created `docs/AQAR_TASKS.md` as the current, live task tracker rather than continuing the old `docs/tasks/TASK-001` through `TASK-018` numbering.
**Why:** the audit report found `TASK-018` (Final Release Audit) falsely claimed all 18 roadmap tasks complete, directly contradicted by evidence in the same report (P0-4: two required MVP features have zero routes). That numbering series is flagged as historically unreliable and slated for archival per the audit's Section 17 documentation findings.
**How to apply:** `docs/tasks/TASK-*` files are historical record only, same status as `docs/audit/*` and `docs/project/*` — do not add new entries there or treat their COMPLETE/VERIFIED claims as current without independent re-verification.

### 2026-08-20 — Did not backfill Tenant/MaintenanceRequest seed data while fixing P0-2/P0-3

**Decision:** while verifying the P0-2 IDOR fix, discovered zero `Tenant` model records and zero `MaintenanceRequest` records exist anywhere in the dev database — the seeded `tenant@darhadhramaut.ye` persona has no linked `Tenant` row. Fixed the code (P0-2, P0-3B) to behave safely in this state (empty results, not an error), verified via temporary tinker-created test data (deleted after), but did not add permanent seed data for this.
**Why:** seeding realistic demo data is a distinct Phase 4 concern (`docs/AQAR_EXECUTION_PLAN.md`) with its own realism/consistency bar — bolting on a couple of tinker-created rows during a security-fix pass risks leaving low-quality placeholder data in place instead of a proper `TenantSeeder` update.
**How to apply:** tracked as P2-NEW-1 in `docs/AQAR_TASKS.md`. Do this properly in Phase 4, not as a side effect of a different fix.

### 2026-08-20 — `tenants` table is not a simple alias for tenant-role `users`

**Observation, no action taken:** the `tenants` table has its own `name`/`email`/`password` columns independent of `users` — a `Tenant` record links to a `User` via `user_id` but is not automatically created when a user is assigned the `tenant` role, and could in principle represent a different identity than the logged-in user. This is the same class of "two parallel domain concepts" issue as the Lease/LeaseAgreement split (P1-10 in the audit report).
**Why noted:** anyone touching tenant-scoped queries in the future should resolve the `Tenant` record explicitly (`Tenant::where('user_id', auth()->id())`) rather than assuming `auth()->id()` is directly usable — this is exactly the mistake that caused P0-2 and part of P0-3.
