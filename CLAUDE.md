# CLAUDE.md — Novixa Aqar Operating Manual

## Project identity

- **Product:** Novixa Aqar — a bilingual (Arabic-first) real-estate platform, owned by Novixa (novixa.dev).
- **Purpose:** a reusable, productized real-estate platform for offices, brokers, landlords, buyers, sellers, tenants, and contractors — not a one-off prototype.
- **Primary market:** Yemen (cities/governorates, YER-primary pricing, Arabic RTL as the default experience). Architecture should not preclude broader GCC expansion later, but the Yemeni demo experience is not to be diluted for that.
- **Current objective:** take the app from its audited-but-broken state to a stable, complete, professional, deployable demo. Live status: `docs/AQAR_PROGRESS.md`. Live task list: `docs/AQAR_TASKS.md`. Baseline defect list: `AQAR_PRE_RELEASE_AUDIT_REPORT.md`.

## Architecture

- **Stack:** Laravel 13.24 (PHP 8.4), Filament 5.7.6 (9 panels: Admin, Staff, App, Tenant, Landlord, Buyer, Seller, Contractor, Agent), Livewire 4.3.5, Spatie Permission 6.25, Jetstream + Socialstream (OAuth), Sanctum, Octane (RoadRunner — not Swoole, despite `.env.example`), Horizon, Reverb, Stripe, DocuSign, Tailwind 4, MySQL.
- **Scale:** ~103 Eloquent models, ~669 routes, 9 Filament panels, 103 test files.
- **PanelProviders** live in `app/Providers/Filament/*PanelProvider.php` — one per role. Each panel has its own `Pages/`, `Resources/`, `Widgets/` under `app/Filament/{Role}/`.
- **Multi-tenancy:** teams via `team_id` on most tables (`Property`, etc.). **Do not confuse this with the `tenant` role** (a renter) — `tenant_id` columns mean different things on different tables (`Lease.tenant_id`/`Payment.tenant_id` → `users.id`; `MaintenanceRequest.tenant_id` → `tenants.id`). Verify the actual column/relationship before writing any query that filters by "tenant" — this exact confusion caused a live P0 (see audit report P0-3 Bug B).
- **Localization:** `lang/{ar,en}.json` (flat) + `lang/{ar,en}/*.php` (nested, used by Filament) + inline `app()->getLocale() === 'ar' ? … : …` ternaries in Blade (the dominant pattern on public pages). `SetLocaleMiddleware` sets the locale from session; `<html dir="{{ ... }}">` in `resources/views/layouts/app.blade.php` is correctly locale-aware — do not touch this pattern without checking it renders both directions.

## Development rules

- Match existing conventions in the file you're editing before introducing a new pattern.
- Database: never assume a column exists because the name sounds right — check `Schema::getColumnListing()` or the migration. This has already caused a live P0.
- Filament: when adding a page to a panel, register it in that panel's `PanelProvider.php` `->pages([...])` array — a page class existing on disk does not make it reachable.
- Localization: Arabic is the primary/default locale. New user-facing strings need both `lang/ar.json` and `lang/en.json` entries (or the equivalent nested file), not just a `__()` call with no matching key — an existing key-less `__()` call silently renders the raw key text in production.
- RTL/LTR: prefer logical Tailwind utilities (`ms-`/`me-`, `gap-*`) over physical ones (`ml-`/`mr-`, `space-x-*`) in any file you touch that renders in both directions.

## Testing rules

- Use the PHP 8.4.1+ binary, not the default `php` on PATH (which resolves to 8.4.0 and fatally errors before Laravel boots): `/c/Users/ak01redwan/.config/herd/bin/php84/php.exe` (Git Bash) or `C:\Users\ak01redwan\.config\herd\bin\php84\php.exe` (PowerShell).
- Run the full suite before and after any non-trivial change: `php artisan test --no-ansi`. Baseline: 558 passed / 0 failed / 32 skipped (see `AQAR_PRE_RELEASE_AUDIT_REPORT.md` Section 20). Investigate every regression; never delete or weaken a test to make it pass.
- Static/unit/feature tests are necessary but not sufficient — Filament's runtime route registration and DB-column bugs do not show up in a green test suite (this is exactly how the 6-panel P0-3 crash went undetected). Verify any panel/route change by actually loading it in a browser and logging in as the affected role (`docs/TEST_USERS.md`, all passwords `password`).
- Browser QA tooling: Playwright is available (was installed into a scratch/temp location previously, not a project dependency) — reuse rather than reinvent. Always confirm you are testing the real working tree/branch, not an isolated worktree that may have branched from the wrong ref (see Git rules).

## Git rules

- Current branch: `feature/aqar-productization`. Confirm branch + commit SHA before trusting any finding or making any change — do not assume.
- **If you use `isolation: "worktree"` for any subagent, you MUST verify the worktree's branch/merge-base against `feature/aqar-productization` before trusting anything it finds.** A prior session lost ~75 minutes of agent time to a worktree that silently checked out `origin/main` (a different, older lineage) instead of this branch — see `docs/AQAR_DECISIONS.md`. Prefer working directly in the real working tree for anything that needs the actual current code.
- Coherent, scoped commits only — no giant mixed-purpose commits, no vague messages (`fix`, `updates`, `final`). Use `type(scope): description` (e.g. `fix(filament): register EditProfile page in Landlord panel`).
- Never force-push, never rewrite published history, never commit secrets, never use `--no-verify` unless explicitly instructed.

## AI execution rules

- Inspect before modifying; verify before assuming. Reproduce a bug before fixing it.
- Fix root causes, not symptoms — do not patch a downstream symptom of a wrong DB query or a missing route registration.
- Do not hide failures, do not weaken tests or validation to manufacture a green result, do not silently change product requirements.
- Update `docs/AQAR_TASKS.md` / `docs/AQAR_PROGRESS.md` when a task's status changes — no task is `FIXED` without an actual verification step recorded.

## Release rules

No demo release until: zero unresolved P0 blockers, no fatal errors reachable from any seeded persona's first click, no broken primary workflow, no critical/live authorization vulnerability, no obviously fake/placeholder content on a page meant to look real. See `docs/AQAR_RULES.md` for the full gate.
