# 39 — Final Release Gate & Master Blocker Matrix

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Purpose**: Definitive Independent Release Gate Decision & Transition Playbook  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team  

---

## 1. Master Release Blocker Matrix

| ID | Issue Description | Severity | Timing | Evidence | Blocking? | Required Action |
|---|---|---|---|---|---|---|
| **BLK-01** | **Livewire v4 Automated Test Assertions Failing** | **HIGH** | BEFORE PRODUCTION | 17 failed tests out of 721 in `php artisan test` (`VirtualTourTest`, `LivewireCalculatorsTest`, `PropertySubmissionFormTest`, etc.) | **YES** | Update Livewire 4 event dispatch and error assertions to achieve 100% test pass rate. |
| **BLK-02** | **Privileged Admin & Demo Accounts in Default Seeder** | **CRITICAL** | BEFORE PRODUCTION | `UserSeeder.php` creates `admin@example.com` with password `'password'` and super_admin privileges. | **YES** | Separate into `ProductionSeeder` (schema only) and `DemoSeeder`. Use `make:filament-user` in production. |
| **BLK-03** | **PHP Version Constraint in `composer.json`** | **MEDIUM** | BEFORE NEW REPOSITORY | `composer.json:11` specifies `"php": "^8.5"` on PHP 8.4 runtime. | **YES** | Update constraint to `"php": "^8.4\|^8.5"`. |
| **BLK-04** | **Inconsistent Branding in Admin Panel Provider & App Layout** | **MEDIUM** | BEFORE NEW REPOSITORY | `AdminPanelProvider.php:39` and `layouts/app.blade.php:8` contain `'شركة الأفق العقارية'` instead of Novixa Aqar. | **YES** | Normalize brandName and title fallback to `'نوفيكسا عقار'` / `'عقاري'`. |
| **BLK-05** | **Composer Package Name Still Uses Upstream ID** | **LOW** | BEFORE NEW REPOSITORY | `composer.json:2` declares `"name": "liberu-real-estate/real-estate-laravel"`. | **YES** | Change to `"name": "novixa/aqar"`. |
| **BLK-06** | **Unrestricted `robots.txt` Configuration** | **LOW** | BEFORE PRODUCTION | `public/robots.txt` does not disallow `/admin`, `/app`, or `/api`. | **YES** | Update `public/robots.txt` with disallowed administrative paths and sitemap link. |
| **BLK-07** | **Remaining Upstream References in Config & Testing** | **LOW** | BEFORE NEW REPOSITORY | `.env.testing` has `APP_NAME="Liberu Real Estate"`; `.env.example` has `DB_DATABASE=liberu_real_estate`; K8s has `agent.liberu.co.uk`. | **YES** | Clean references per Report 26. |
| **BLK-08** | **Missing Standalone Attribution & License File** | **LOW** | BEFORE NEW REPOSITORY | No root `ATTRIBUTION.md` formally documenting upstream MIT origin. | **NO** | Add `ATTRIBUTION.md` crediting Curtis Delicata / Liberu Real Estate base. |
| **BLK-09** | **30 Legacy Root Markdown Files** | **INFO** | BEFORE NEW REPOSITORY | 30 markdown docs floating in root directory. | **NO** | Move legacy files to `docs/archive/`. |
| **BLK-10** | **Production Mailer & Redis Infrastructure Setup** | **HIGH** | BEFORE PRODUCTION | Default `.env` uses `sync` queue and `mailpit`. | **YES** | Provision production SMTP and Redis instance on host. |

---

## 2. Definitive Release Gate Answers

### A. Is the application technically ready?
> **PARTIALLY**  
> The core domain architecture, database migrations, security filters, Arabic RTL UI, YER currency, and Filament multi-panel features are robust and complete. However, 17 Livewire test helper regressions must be resolved to restore test suite green status.

### B. Is it ready to leave the old repository?
> **YES**  
> The code is sufficiently customized and decoupled from the open-source upstream codebase. It can immediately transition to the private Novixa GitHub organization.

### C. Is the Git history safe to preserve?
> **UNCERTAIN (Moderate Privacy & Cleanliness Risk)**  
> Preserving history carries upstream developer email addresses and intermediate refactoring commits. While no active production credentials leaked into history, fresh history is cleaner for enterprise IP.

### D. Should the new Novixa repository use fresh Git history?
> **RECOMMENDATION: YES (Fresh Git History)**  
> Archive the existing repository as an internal development backup, and initialize the new `novixa/aqar` repository as a clean `v1.0.0` commit. This provides minimal clone size, clean commit attribution, and professional IP separation.

### E. Is the branding fully clean?
> **PARTIALLY**  
> Public pages and 8 role panels are branded as Novixa Aqar. Two files (`AdminPanelProvider.php` and `resources/views/layouts/app.blade.php`) still contain the placeholder string *"شركة الأفق العقارية"*, and `.env.testing` contains *"Liberu Real Estate"*.

### F. Is ownership/license/attribution sufficiently verified?
> **YES / REQUIRES FORMAL ATTRIBUTION FILE**  
> The upstream MIT license fully permits Novixa's private commercialization and customization. A formal `ATTRIBUTION.md` should be added to maintain standard MIT copyright notice compliance.

### G. Is a fresh clone reproducible?
> **YES (With PHP Constraint Rectification)**  
> Build assets, migrations, and dependencies install cleanly, provided `composer.json` specifies `"php": "^8.4|^8.5"`.

### H. Is production deployment technically ready?
> **PARTIALLY**  
> Production deployment requires:
> 1. Production `.env` provisioning (`APP_DEBUG=false`, HTTPS SSL, SMTP credentials, Redis).
> 2. Decoupling demo seeders from default migration runbooks.
> 3. Creating the initial super-admin via interactive CLI.

---

## 3. Real Blockers vs Safely Deferred Items

### EXACT REMAINING BLOCKERS (Must Fix Before Production Launch):
1. **Fix 17 failing Livewire test assertions** in `php artisan test`.
2. **Quarantine `UserSeeder.php`** from `DatabaseSeeder.php` to prevent creating default password `'password'` backdoors.
3. **Change `composer.json`** package name to `novixa/aqar` and PHP constraint to `^8.4|^8.5`.
4. **Normalize branding** in `AdminPanelProvider.php` and `layouts/app.blade.php`.
5. **Update `public/robots.txt`** to protect administrative and API routes.
6. **Provision Production Environment** (`APP_ENV=production`, `APP_DEBUG=false`, TLS/SSL, Production SMTP).

### CAN SAFELY WAIT UNTIL AFTER LAUNCH:
1. Octane / RoadRunner server runtime tuning.
2. Cloudflare CDN edge caching for 3D GLB assets.
3. AI valuation model weight retraining with live market transactions.
4. SMS OTP gateway for phone verification.

---

## 4. FINAL RELEASE GATE DECISION

```
================================================================================
                    FINAL RELEASE GATE VERDICT:
                 YELLOW — RELEASE CANDIDATE (RC-1)
================================================================================
```

### Justification:
The platform represents an exceptionally well-engineered, highly localized, and feature-complete real-estate product. It is ready to transition into Novixa's private repository today. Production deployment is blocked only by 6 clearly scoped, fast-to-fix technical remediations (test assertion updates, seeder separation, branding string normalization, and environment provisioning).

---

## 5. Recommended Execution Sequence

```mermaid
sequenceDiagram
    autonumber
    actor Engineer as Release Engineer
    participant Code as Local Repository
    participant Git as Private Novixa GitHub
    participant Prod as Production Server

    Note over Engineer,Code: PHASE 1: Pre-Separation Fixes
    Engineer->>Code: 1. Update composer.json (novixa/aqar, php ^8.4|^8.5)
    Engineer->>Code: 2. Normalize branding in AdminPanelProvider & layouts/app
    Engineer->>Code: 3. Quarantine UserSeeder from DatabaseSeeder
    Engineer->>Code: 4. Fix 17 Livewire test assertions (Verify 100% pass)
    Engineer->>Code: 5. Update robots.txt & add ATTRIBUTION.md

    Note over Engineer,Git: PHASE 2: Repository Separation
    Engineer->>Code: 6. Backup historical repo; re-init fresh git history
    Engineer->>Git: 7. Push main branch & tag v1.0.0 to github.com/novixa/aqar

    Note over Engineer,Prod: PHASE 3: Production Deployment
    Engineer->>Prod: 8. Clone private repo; composer install & npm run build
    Engineer->>Prod: 9. Configure production .env (APP_DEBUG=false, SSL, SMTP)
    Engineer->>Prod: 10. Run migrate & ProductionSeeder; make:filament-user
    Engineer->>Prod: 11. Final production smoke test & DNS switch
```
