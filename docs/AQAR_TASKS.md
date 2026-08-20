# Aqar Rescue — Task List

Live tracker. Source of truth for priority/root-cause detail is `AQAR_PRE_RELEASE_AUDIT_REPORT.md`; this file tracks execution status. Every `FIXED` entry must name its verification method and commit. Statuses: `DISCOVERED`, `VERIFIED`, `READY`, `IN_PROGRESS`, `FIXED`, `BLOCKED`, `WONT_FIX`, `DEFERRED`.

## P0 — Release Blockers

| ID | Problem | Root cause | Required change | Verification | Status | Commit |
|---|---|---|---|---|---|---|
| P0-3A | Landlord/Buyer/Seller/Contractor panels: HTTP 500 on every page | 4 `PanelProvider.php` files import `App\Filament\App\Pages\EditProfile` (wrong panel's class) and register empty `->pages([])` | Import each panel's own `EditProfile` class; add to `->pages([...])`; create the class where missing (Buyer, Seller/Contractor) | Log in as each of the 4 roles, load dashboard, confirm no 500 + check `laravel.log` clean | VERIFIED (root cause), READY to fix | — |
| P0-3B | Tenant panel: HTTP 500 on login | `Tenant/Pages/Dashboard.php:23` queries nonexistent `properties.tenant_id` | Resolve current property via active `Lease`, not a direct FK | Log in as tenant, load dashboard, confirm no 500 | VERIFIED (root cause), READY to fix | — |
| P0-3C | `App` panel: HTTP 500 on dashboard load | `App/Widgets/StatsOverview.php` sums `transactions.transaction_amount` (real column: `amount`) | Fix column name | Load `/app/dashboard`, confirm no 500 | VERIFIED (root cause), READY to fix | — |
| P0-2 | Live cross-tenant IDOR in Tenant Maintenance Requests | `MaintenanceRequestResource.php` has no `getEloquentQuery()` scope (siblings `LeaseResource`/`PaymentResource` do) | Add tenant-scoped `getEloquentQuery()` override | Log in as two different tenants, confirm each only sees their own requests | VERIFIED, READY to fix | — |
| P0-1 | Filament authorization fails open — only 5/45 resources have a Policy | `isAuthorizationStrict` never set; most resources have no Policy class | Generate/write Policies for all 45 resources, or set `->authorizationStrict()` per panel as interim stop-gap | Attempt CRUD as a role without the relevant permission; confirm denied | VERIFIED, READY to fix | — |
| P0-4 | Owner Property Submission & Property Request forms unreachable | Built (`PropertySubmissionForm`, `PropertyRequestForm`) but never routed | Add routes `/submit-property`, `/property-request` (or confirm intentional exclusion with product) | Visit both URLs, submit each form, confirm success | VERIFIED, READY to fix | — |
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

## P2/P3/P4 — Important / Polish / Future

Tracked in full in `AQAR_PRE_RELEASE_AUDIT_REPORT.md` Sections 6, 9, 11–16. Not duplicated here to avoid drift between two lists — pull items into this table with a status the moment work starts on them.

---

**Update discipline:** when a task's status changes, update this file AND, for P0/P1 items, log the change in `docs/AQAR_PROGRESS.md` with date + commit.
