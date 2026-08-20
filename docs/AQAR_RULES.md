# Aqar Rescue — Project Rules

Binding rules for the current repair/hardening/demo-readiness effort. See `CLAUDE.md` for general project conventions; this file is specific to the rescue project.

1. **Never fix a symptom when the root cause can be identified.** Trace a bug to the actual wrong line/column/config before patching around it.
2. **Never declare a task `FIXED` without verification** — re-run the relevant test, reload the relevant page/panel, or reproduce-then-confirm-gone. Record the verification method in `docs/AQAR_TASKS.md`.
3. **Never weaken security or validation to make a test pass.** If a test fails because it encodes correct-but-inconvenient behavior, fix the code, not the test's expectations of security.
4. **Never delete a failing test because it exposes a real bug.** Fix the bug; the test stays.
5. **Never modify production behavior solely because the audit report said so, without reproducing the underlying issue first.** The audit is the starting point, not an unquestionable source of truth (see `AQAR_PRE_RELEASE_AUDIT_REPORT.md`'s own methodology note about a prior finding it had to revise after live verification).
6. **Arabic RTL and English LTR are both first-class.** Any UI change must be checked in both directions before being considered done.
7. **No page is complete until its loading, empty, success, validation, unauthorized, not-found, and error states have been considered** where applicable to that page.
8. **No seeded demo account may land on a fatal error after authentication.** This is the current #1 defect class (P0-3) — treat any new occurrence of this as a P0.
9. **All demo data is fictional.** No real people's private information, ever.
10. **Never commit secrets.** Check diffs before staging, especially `.env`-adjacent files.
11. **Every coherent change gets a meaningful commit.** No giant mixed-purpose commits, no vague messages.
12. (merged into 11)
13. **Never hide warnings, exceptions, or failed tests.** Surface them in `docs/AQAR_PROGRESS.md` even if not fixed yet.
14. **Prefer official documentation** (Laravel, Filament, PHP, the relevant package) over third-party snippets when researching a fix; verify version compatibility before applying anything found externally.
15. **When unsure, inspect the actual running system instead of guessing.** `Schema::getColumnListing()`, `route:list`, `storage/logs/laravel.log`, and an actual browser session are all cheaper than a wrong guess.
