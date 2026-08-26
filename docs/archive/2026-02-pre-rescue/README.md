# Archive: pre-rescue documentation (superseded 2026-08-20)

Everything under this folder predates the `feature/aqar-productization` rescue
effort and is kept for historical reference only. **None of it should be
trusted as a description of current system state.**

## Why this exists

The project accumulated three generations of status/audit documentation
(`docs/audit/01`–`39`, `docs/project/*`, `docs/tasks/TASK-*-ANALYSIS.md`) plus
a dozen root-level "Implementation Summary" / "Final Status" reports for
individual features. Cross-checking them against the live application during
the 2026-08 audit (`AQAR_PRE_RELEASE_AUDIT_REPORT.md`) found they frequently
claimed "COMPLETED & VERIFIED" status backed by nothing more than a passing
`php artisan test` run — which does not verify UI correctness, translation
completeness, or RTL rendering. Two concrete examples that motivated this
archive:

- `tasks/TASK-004-ARABIC-LOCALIZATION-ANALYSIS.md` and
  `tasks/TASK-005-RTL-POLISH-ANALYSIS.md` both claim Arabic localization and
  RTL support are "COMPLETED & VERIFIED," citing only a green test suite.
  Live browser verification during the 2026-08 rescue found extensive
  untranslated strings and RTL layout bugs across the site — the opposite of
  what these documents claim.
- `project/RELEASE_CHECKLIST.md` asserted release-readiness items as done
  with no supporting evidence, at a time the app had fatal 500 errors on
  6 of 9 Filament panels (see `AQAR_PRE_RELEASE_AUDIT_REPORT.md` P0-3).

Reading any of these documents at face value will produce wrong conclusions
about what is actually implemented, tested, or working.

## What's here

- `audit/` — 39 numbered audit-iteration snapshots (`docs/audit/01`–`39`)
- `project/` — 8 files from `docs/project/` (context, workflow, handover
  notes, release checklist, feature matrix, architecture audit)
- `tasks/` — 17 `TASK-NNN-*-ANALYSIS.md` files + template, an earlier
  task-numbering scheme superseded by `docs/AQAR_TASKS.md`
- `root-reports/` — root-level status reports superseded by
  `AQAR_PRE_RELEASE_AUDIT_REPORT.md`, including an earlier full audit pass
  (`NOVIXA_REAL_ESTATE_MASTER_AUDIT_AND_EXECUTION_PLAN.md`, 2026-08-17) that
  the current audit report supersedes
- `feature-summaries/` — per-feature "Implementation Summary" documents
  where a genuine reference doc for the same feature already exists
  elsewhere in `docs/` (e.g. `WISHLIST_SUMMARY.md` archived because
  `docs/WISHLIST_API.md` is the real reference)

## Current source of truth

- Live defect/status tracking: `docs/AQAR_PROGRESS.md` (the original baseline audit that superseded this archive's documents has itself since moved to `docs/archive/2026-08-rescue-qa-history/AQAR_PRE_RELEASE_AUDIT_REPORT.md`, once it too became a historical record)
- Active task list: `docs/AQAR_TASKS.md`
- Progress log: `docs/AQAR_PROGRESS.md`
- Decision log: `docs/AQAR_DECISIONS.md`
- Operating rules: `docs/AQAR_RULES.md`, `CLAUDE.md` (root)
- Feature reference docs now live under `docs/` top level (e.g.
  `docs/WISHLIST_API.md`, `docs/CHATBOT_ARCHITECTURE.md`) — see
  `docs/README.md` for the full index. They are believed accurate as
  *feature descriptions* but were not re-verified line-by-line during this
  archival pass — treat implementation claims in them with the same
  skepticism as any other pre-rescue document until spot-checked.
