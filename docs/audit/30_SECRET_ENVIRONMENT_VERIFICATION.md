# 30 — Secret & Environment Configuration Verification Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Purpose**: Verification of Environment Templates, Secrets Safety & Production Readiness  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team (DevOps / Security Auditor)  

---

## 1. Executive Summary

A comprehensive security audit of `.env`, `.env.example`, `.env.testing`, `.gitignore`, and configuration files was conducted. No sensitive production secrets or private credentials are hardcoded or tracked in repository history. However, configuration cleanup is required in `.env.example` to resolve duplicate keys and upstream naming defaults.

---

## 2. Environment Variable Classification Matrix

| Variable | Category | Required in Prod? | Default in Example | Production Security Requirement |
|---|---|---|---|---|
| `APP_NAME` | Config | **YES** | `"Aqar"` | Set to official brand name (e.g. `"عقاري - نوفيسا"`). |
| `APP_ENV` | Runtime | **YES** | `local` | **MUST** be set to `production`. |
| `APP_KEY` | **SECRET** | **YES** | *(empty)* | **MUST** be generated via `php artisan key:generate`. |
| `APP_DEBUG` | Security | **YES** | `true` | **MUST** be set to `false`. |
| `APP_URL` | Config | **YES** | `http://localhost` | **MUST** be set to canonical production HTTPS URL (e.g. `https://aqar.novixa.ye`). |
| `DB_CONNECTION` | Database | **YES** | `mysql` | Typically `mysql` or `pgsql`. |
| `DB_HOST` | Database | **YES** | `127.0.0.1` | Set to production DB host/socket. |
| `DB_DATABASE` | Database | **YES** | `liberu_real_estate` (Needs Fix) | Change default to `aqar_prod`. |
| `DB_USERNAME` | Database | **YES** | `root` | Provision dedicated non-root DB user. |
| `DB_PASSWORD` | **SECRET** | **YES** | *(empty)* | Strong generated password (>20 chars). |
| `SESSION_DRIVER` | Runtime | **YES** | `file` / `cookie` (Duplicate in file) | Set to `redis` or `database` for multi-server setups. |
| `SESSION_SECURE_COOKIE` | Security | **YES** | `false` | **MUST** be set to `true` on HTTPS production. |
| `QUEUE_CONNECTION` | Runtime | **YES** | `sync` | Set to `redis` or `database` with worker daemons. |
| `CACHE_DRIVER` | Runtime | **YES** | `file` | Set to `redis` for high concurrency. |
| `MAIL_MAILER` | Mail | **YES** | `smtp` | Set to production mail provider (Postmark/SES/Resend/SMTP). |
| `MAIL_HOST` | Mail | **YES** | `mailpit` | Production SMTP host. |
| `MAIL_USERNAME` | **SECRET** | **YES** | `null` | Production SMTP username. |
| `MAIL_PASSWORD` | **SECRET** | **YES** | `null` | Production SMTP password. |
| `STRIPE_SECRET` | **SECRET** | Optional | *(empty)* | Production Stripe API key if credit card payments enabled. |
| `STRIPE_WEBHOOK_SECRET` | **SECRET** | Optional | *(empty)* | Stripe webhook signing secret. |
| `WALKSCORE_API_KEY` | **SECRET** | Optional | *(empty)* | WalkScore API token. |
| `HOLOGRAPHIC_API_KEY` | **SECRET** | Optional | *(empty)* | Looking Glass API key for 3D holographic tours. |

---

## 3. Discovered Configuration Deficiencies

1. **Duplicate Keys in `.env.example`**:
   - `SESSION_DRIVER` is declared twice (Line 28: `SESSION_DRIVER=file` and Line 30: `SESSION_DRIVER=cookie`).
   - `SESSION_SECURE_COOKIE` is declared twice (Line 22: `SESSION_SECURE_COOKIE=false` and Line 32: `SESSION_SECURE_COOKIE=false`).
2. **Upstream Database Default in `.env.example`**:
   - Line 16 declares `DB_DATABASE=liberu_real_estate`. Should be `DB_DATABASE=aqar`.
3. **Upstream App Name in `.env.testing`**:
   - Line 1 declares `APP_NAME="Liberu Real Estate"`.
   - Line 17 declares `DB_DATABASE=liberu_real_estate_testing`.
4. **Gitignore Protection Verification**:
   - `.gitignore` properly includes `.env`, `.env.backup`, `.env.production`, `storage/*.key`.
   - Local `.env` is correctly untracked.

---

## 4. Production Provisioning Recommendations

- Clean `.env.example` by removing duplicate lines and updating database name defaults.
- Create a deployment script that checks `APP_DEBUG=false` and `SESSION_SECURE_COOKIE=true` before running `php artisan config:cache`.
