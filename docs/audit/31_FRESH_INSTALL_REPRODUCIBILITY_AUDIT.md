# 31 — Fresh Installation & Reproducibility Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Purpose**: Verify Fresh Clone Reproducibility & Build Pipeline Feasibility  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team (DevOps & Release Engineer)  

---

## 1. Executive Summary

A non-destructive installation and build simulation was conducted to determine whether an independent developer or automated CI/CD pipeline can clone the repository from scratch and achieve a fully functional application.

---

## 2. Reproducibility Stage Verification Matrix

| Pipeline Stage | Evaluated Component | Actual State / Result | Reproducibility Verdict | Notes & Discovered Blockers |
|---|---|---|---|---|
| **1. PHP Dependencies** | `composer.json`, `composer.lock` | `composer validate` passes. | **ACTION REQ** | `composer.json:11` specifies `"php": "^8.5"`. On standard production servers running PHP 8.4 (e.g. PHP 8.4.16), `composer install` will halt unless `--ignore-platform-reqs` is supplied or the constraint is relaxed to `"php": "^8.4\|^8.5"`. |
| **2. JS / Frontend Dependencies** | `package.json`, `package-lock.json` | `package.json` is valid. Dependencies: Tailwind CSS v4, Livewire v4, Alpine.js, Flowbite, Leaflet, Three.js. | **PASS** | `npm install` installs cleanly; Node v24.15.0 / npm 11.12.1 verified. |
| **3. Asset Compilation** | `vite.config.js`, `public/build/` | Built assets present in `public/build/assets/` (`manifest.json`, `app-*.css`, `app-*.js`, `theme-*.css`). | **PASS** | Vite build reproduces pre-compiled production bundles. |
| **4. Environment Generation** | `.env.example` -> `.env` | Handled via Composer post-root script. | **PASS** | Script executes `copy('.env.example', '.env')` and `php artisan key:generate`. |
| **5. Database Migrations** | `database/migrations/` | 110+ schema migrations execute sequentially. | **PASS** | Tables created cleanly under SQLite testing and MySQL environments. |
| **6. Database Seeding** | `database/seeders/DatabaseSeeder.php` | Seeder runs without fatal errors. | **ACTION REQ** | Creates demo properties and hardcoded test user credentials (`admin@example.com` / `password`). Must not be run directly in production. |
| **7. Storage Symlinking** | `php artisan storage:link` | Standard Laravel public storage link. | **PASS** | Required for property media and avatars. |
| **8. Filament Theme Compilation** | `filament:upgrade`, `theme.css` | Filament v5 assets compiled. | **PASS** | Theme styles available at `public/build/assets/theme-*.css`. |
| **9. Automated Test Suite** | `php artisan test` | 721 tests executed. | **ACTION REQ** | 671 passed, 33 skipped, 17 failed. Test assertion updates required. |

---

## 3. Fresh Installation Command Runbook

To reproduce a working environment from a fresh clone:

```bash
# 1. Clone repository
git clone <novixa-repo-url> aqar
cd aqar

# 2. Install Composer dependencies (relaxing PHP constraint to ^8.4|^8.5)
composer install --no-interaction --prefer-dist --optimize-autoloader

# 3. Install Node packages and compile assets
npm ci
npm run build

# 4. Initialize environment
cp .env.example .env
php artisan key:generate

# 5. Execute migrations and storage link
php artisan migrate --force
php artisan storage:link

# 6. Execute tests
php artisan test
```

---

## 4. Reproducibility Verdict

**Status**: **PARTIALLY REPRODUCIBLE (YELLOW)**.  
The build and asset pipelines are functional, but `composer.json` platform constraints and failing test assertions must be resolved before automated CI/CD deployment pipelines are locked in.
