# Archive: 2026-08 rescue QA history (superseded 2026-08-25)

Everything under this folder is a dated, point-in-time audit or QA report produced during the `feature/aqar-productization` rescue effort (2026-08-20 through 2026-08-25). Each one was accurate **at the time it was written**, and each was superseded by a later pass in the same series — read them as a historical record of how the project got to its current state, not as a description of current state.

**For current state, see `docs/AQAR_PROGRESS.md` (the live, continuously-updated log) and `docs/REPOSITORY_RELEASE_AUDIT.md` (the current repository/release readiness record).**

## What's here, in chronological order

1. `AQAR_PRE_RELEASE_AUDIT_REPORT.md` (2026-08-20) — the original full-codebase audit that kicked off the rescue effort. 8 P0s, 21 P1s. Cited as `docs/AQAR_TASKS.md`'s root-cause source of truth for the tasks it originated.
2. `AQAR_EXECUTION_PLAN.md` (2026-08-20) — the original 10-phase plan. Superseded in practice: the project progressed well past the "Phase 1, in progress" state this document was frozen at — see `docs/AQAR_TASKS.md`/`docs/AQAR_PROGRESS.md` for what actually happened in each later phase.
3. `REAL_ESTATE_SCOPE_AUDIT.md` (2026-07-28) — a scope-completeness audit against an external upstream feature-spec document.
4. `AQAR-DEMO-READINESS-AUDIT.md` (2026-08-25) — a 41-section independent re-audit; found several defects the original baseline had missed (seed-memory crash, a credible IDOR, an image-loading CSP bug). Verdict at the time: READY WITH FIXES.
5. `AQAR-FINAL-RELEASE-QA.md` (2026-08-25) — the verification record for fixing every item #4 found. Verdict: READY.
6. `AQAR-FINAL-END-TO-END-ACCEPTANCE-REPORT.md` (2026-08-25) — a second, independent, deeper end-to-end acceptance pass (persona × authorization × CRUD × forms × API). Verdict: READY FOR DEMO, NOT READY FOR PRODUCTION (pending the README/`k8s`/`install.php` branding issue).
7. `AQAR-BROWSER-TEST-MATRIX.md` (2026-08-25) — the raw live-browser test matrix backing #6.
8. `AQAR-REMAINING-WORK.md` (2026-08-25) — the open-items list as of #6. **Its "Production Blockers" §1 (README/`k8s`/`install.php` still being 100% upstream Liberu content) is now resolved** — see `docs/REPOSITORY_RELEASE_AUDIT.md` for what replaced it. Do not treat this document's open items as still-open without checking `docs/AQAR_TASKS.md`/`docs/AQAR_PROGRESS.md` first.

## Current source of truth

- Live task list: `docs/AQAR_TASKS.md`
- Live progress log: `docs/AQAR_PROGRESS.md`
- Decision log: `docs/AQAR_DECISIONS.md`
- Operating rules: `docs/AQAR_RULES.md`, `CLAUDE.md` (root)
- Repository/release readiness: `docs/REPOSITORY_RELEASE_AUDIT.md`
- Deployment & release process: `docs/PRODUCTION-DEPLOYMENT.md`, `docs/RELEASE-CHECKLIST.md`
