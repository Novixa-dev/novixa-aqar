# Aqar Rescue — Task List

Live tracker. Source of truth for priority/root-cause detail is `AQAR_PRE_RELEASE_AUDIT_REPORT.md`; this file tracks execution status. Every `FIXED` entry must name its verification method and commit. Statuses: `DISCOVERED`, `VERIFIED`, `READY`, `IN_PROGRESS`, `FIXED`, `BLOCKED`, `WONT_FIX`, `DEFERRED`.

## P0 — Release Blockers

| ID | Problem | Root cause | Required change | Verification | Status | Commit |
|---|---|---|---|---|---|---|
| P0-3A | Landlord/Buyer/Seller/Contractor panels: HTTP 500 on every page | 4 `PanelProvider.php` files import `App\Filament\App\Pages\EditProfile` (wrong panel's class) and register empty `->pages([])` | Import each panel's own `EditProfile` class; add to `->pages([...])`; create the class where missing (Buyer, Seller/Contractor) | Restarted dev server, logged in as all 9 personas — 0 HTTP/JS errors, full suite still 558/0/32 | **FIXED** | `2159108` |
| P0-3B | Tenant panel: HTTP 500 on login | `Tenant/Pages/Dashboard.php:23` queried nonexistent `properties.tenant_id` | Resolve current property via active `Lease`; resolve MaintenanceRequest/WorkOrder counts via the `Tenant` record, not `auth()->id()` directly | Same live pass as P0-3A | **FIXED** | `2159108` |
| P0-3C | `App`/Staff panels: HTTP 500 on dashboard/transactions | `transaction_amount`/`transaction_date` used in 6 files instead of the real columns `amount`/`date` — far more widespread than first found (Staff Dashboard, Staff Transactions resource, 2 Staff widgets, TransactionService, App Dashboard) | Fixed column name in all 6 files | Same live pass as P0-3A, plus explicit `/staff/dashboard` and `/staff/transactions` checks | **FIXED** | `2159108` |
| P0-3D | *(new, found while fixing P0-3B)* Tenant dashboard blade manually echoed `$this->getHeaderWidgets()`/a mis-keyed `getWidgets()` array — crashed once P0-3B was fixed and execution reached the view | `resources/views/filament/tenant/dashboard.blade.php`; Filament 5's `<x-filament-panels::page>` already auto-renders header/footer widgets via its schema system | Removed both manual echoes; renamed custom `getWidgets()` → standard `getFooterWidgets()` override | Same live pass as P0-3A | **FIXED** | `2159108` |
| P0-2 | Live cross-tenant IDOR in Tenant Maintenance Requests | `MaintenanceRequestResource.php` has no `getEloquentQuery()` scope; can't copy sibling pattern verbatim since `MaintenanceRequest.tenant_id` → `tenants.id`, not `users.id` like `Lease`/`Payment` | Added `getEloquentQuery()` scoped through the `Tenant` record | Created 2 test Tenant records + 1 MaintenanceRequest each via tinker, authenticated as tenant 1, confirmed query returns only their own record; cleaned up test data after | **FIXED** | `50e1d3f` |
| P0-1 | Filament authorization fails open — only 5/45 resources have a Policy | Missing Policy classes for 40/45 resources, **plus two deeper pre-existing bugs found while fixing this**: (1) `config/filament-shield.php` generated permissions as `View:Property` (PascalCase+colon) while `RolesSeeder.php` matched against `view_property` (snake_case) — a total, silent, case-sensitivity mismatch that meant **every role except super_admin has had zero permissions granted, ever**, independent of anything in this session; (2) the 5 pre-existing hand-written policies were themselves missing `viewAny`/`create`/`delete` methods (PropertyPolicy had none of the three) — since Property is used across 5+ panels, this alone would have blocked property browsing for every non-admin role even after (1) was fixed | Ran `shield:generate --option=policies_and_permissions` across all 9 panels (after enabling `discover_all_resources/widgets/pages` in shield config); registered a `FilamentShield::buildPermissionKeyUsing()` closure in `AppServiceProvider::boot()` producing `action_model` snake_case (Shield's config can't do this directly — `_` as separator conflicts with snake-family cases); registered the flagged `RolePolicy` via `Gate::policy()`; completed the 4 incomplete hand-written policies (Property/Appointment/Booking/Image) preserving their existing team/ownership `view`/`update` logic; fixed `RolesSeeder.php`'s `communication_hub`→`message` and `contractor`→`user` naming (both resources use different underlying models than their name suggests) and added missing `view_any_X` companions (buyer/tenant `review`, buyer `booking`, seller `image`) that were silently hiding nav items even with correct permission names | Ran full test suite 3x (0 net regressions, 1 test fixed to seed real permissions + set explicit Filament panel context); direct `Gate`/`can()` checks confirm correct allow/deny per role (staff: view+create yes, delete no; buyer: view property yes, create/delete/view-users no); live: all 9 panels + Staff dashboard/transactions load with 0 errors, Contractor list now shows real data, Tenant Communication Hub/Maintenance Request nav items restored, staff correctly gets 403 on `/admin/users` | **FIXED** | pending commit |
| P0-4 | Owner Property Submission & Property Request forms unreachable | Built (`PropertySubmissionForm`, `PropertyRequestForm`) but never routed, and not linked from any nav | Added routes `/submit-property`, `/property-request`; added "List Your Property"/"Request a Property" nav entries under Properties (`MenuSeeder.php`, re-seeded); found and fixed a real submission-blocking bug while verifying: `PropertySubmissionForm`'s blade view had **no input at all** for `year_built`, which validation marked `required` — every real submission would have failed silently forever. Added the missing input and relaxed the rule to `nullable` (matching the fallback default already coded in `submit()`) | End-to-end: filled + submitted both forms via Playwright, confirmed real `Buyer`/`Property` records created in the DB, then deleted the test records | **FIXED** | pending commit |
| P0-5 | Multi-tenant portal-sync leak (RightMove/Zoopla/OnTheMarket) | Services read a single global API key and `Property::all()` with no `team_id` filter | Scope sync to the correct team/agency per the existing `PortalIntegration` system | Configure 2 teams' keys, run sync, confirm no cross-team property push | VERIFIED, scope-dependent on demo tenancy | — |
| P0-6 | `APP_URL=http://localhost` will leak into canonical/OG/sitemap | `.env`/`.env.example` default | Set correct `APP_URL` in the actual demo/deploy environment; regenerate sitemap | Check `public/sitemap.xml` and page source for real domain | VERIFIED, environment-provisioning task | — |
| P0-7 | Brand-name collision: 3 different names visible in one click-through | `site_name` vs `footer_copyright` GeneralSettings fields never reconciled; layout hardcodes "Dar Hadhramaut" | Pick one public identity, apply consistently, pull from `GeneralSettings::site_name` everywhere | Load home/panel/footer, confirm single consistent name | VERIFIED, READY to fix | — |
| P0-8 | Dev PHP resolves to 8.4.0; Composer needs ≥8.4.1 | Environment/tooling mismatch, not app code | Use `herd/bin/php84/php.exe` locally; verify actual demo host PHP version before deploy | `php -v` on the actual deploy host | VERIFIED (workaround known) | N/A |

## P1 — Critical (see `AQAR_PRE_RELEASE_AUDIT_REPORT.md` Section 5 for full detail)

| ID | Problem | Status |
|---|---|---|
| P1-1 | No rate limiting on login/register | DISCOVERED |
| P1-2 | `DigitalSignatureService` missing `LeaseAgreement` import — `is_signed` never updates | DISCOVERED |
| P1-3 | E-signature flags directly PATCH-able via API | DISCOVERED |
| P1-4 | No Filament action to trigger e-signature flow | DISCOVERED |
| P1-5 | Deleted `LeaseAgreementService` template logic, no replacement | DISCOVERED — needs product decision first |
| P1-6 | Staff has no functional Maintenance Request UI | DISCOVERED |
| P1-7 | Landlord has `update_rental_application` permission, no panel | DISCOVERED — depends on P0-3A scope decision |
| P1-8 | Agent Matching Service has zero UI entry point | DISCOVERED |
| P1-9 | `Property::needsSyncing()` compares column to literal string | DISCOVERED |
| P1-10 | Two non-integrated Lease/LeaseAgreement models | DISCOVERED — Large, needs design conversation |
| P1-11/12 | Hardcoded `$`/USD in notifications & payment tables | DISCOVERED |
| P1-13 | No in-panel language switcher | DISCOVERED |
| P1-14 | ~96% of Livewire flash messages untranslated | DISCOVERED |
| P1-15/16 | Zero test coverage: Payments/Stripe, functional auth | DISCOVERED |
| P1-17 | `.env.example` OCTANE_SERVER=swoole (only RoadRunner installed) | DISCOVERED |
| P1-18/19 | No dynamic SEO on `/properties`, `/news/{slug}`; sitemap stale/incomplete | DISCOVERED |
| P1-20 | No locale-prefixed URL routing (hreflang blocked structurally) | DISCOVERED — Large/roadmap |
| P1-21 | `APP_DEBUG=true` | DISCOVERED — environment-provisioning task |

## New findings from live verification (not in the original audit)

| ID | Problem | Status |
|---|---|---|
| P2-NEW-1 | Zero `Tenant` records and zero `MaintenanceRequest` records exist anywhere in seed data — the demo `tenant@darhadhramaut.ye` persona has no linked `Tenant` row, so their Maintenance Requests list is empty even after P0-2/P0-3 are fixed (safe-empty, not a leak, but a demo-realism gap) | DISCOVERED — needs a `TenantSeeder`/`UserSeeder` update, tracked for Phase 4 (Demo Data) |
| P2-NEW-2 | `tenants` table has its own `name`/`email`/`password` columns, separate from `users` — a `Tenant` is not automatically 1:1 with a `User` account even when linked via `user_id`; worth a design-conversation note similar to the Lease/LeaseAgreement split (P1-10) | DISCOVERED — logged in `docs/AQAR_DECISIONS.md`, no fix planned yet |

## P2/P3/P4 — Important / Polish / Future

Tracked in full in `AQAR_PRE_RELEASE_AUDIT_REPORT.md` Sections 6, 9, 11–16. Not duplicated here to avoid drift between two lists — pull items into this table with a status the moment work starts on them.

---

**Update discipline:** when a task's status changes, update this file AND, for P0/P1 items, log the change in `docs/AQAR_PROGRESS.md` with date + commit.
