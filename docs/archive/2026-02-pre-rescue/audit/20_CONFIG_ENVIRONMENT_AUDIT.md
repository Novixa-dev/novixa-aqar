# 20 — Environment & Configuration Audit Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Environment Variable Inventory & Audit

Audited `.env.example`.

| Key | Development Value | Production Value Requirement | Must Be Secret? | Status |
|---|---|---|---|---|
| `APP_NAME` | `Aqar` | `Aqar` | No | **CONFIGURED** |
| `APP_ENV` | `local` | `production` | No | Required for Prod |
| `APP_KEY` | *(Generated)* | *(Unique 32-byte Key)* | **YES** | Generated via `key:generate` |
| `APP_DEBUG` | `true` | `false` | **YES** | MUST be `false` in Prod |
| `APP_URL` | `http://localhost` | `https://aqar.novixa.com` | No | Set to Novixa domain |
| `DB_CONNECTION` | `mysql` | `mysql` / `pgsql` | No | Active |
| `DB_HOST` | `127.0.0.1` | *(Production DB Host)* | **YES** | Set in Prod |
| `DB_PASSWORD` | *(Empty)* | *(Strong DB Password)* | **YES** | Set in Prod |
| `STRIPE_SECRET` | *(Empty)* | *(Stripe Prod Key)* | **YES** | Optional if using Stripe |
| `MAIL_HOST` | `mailpit` | *(Production SMTP Host)* | **YES** | Configure SMTP |

---

## 2. Configuration Safety Rules

- **Zero Hardcoded Secrets**: All third-party secrets, database keys, and API keys are referenced through environment variables via `env()` calls inside `config/` files.
- **Production Safety Check**: In production, `APP_DEBUG` MUST be explicitly set to `false` in `.env` to prevent exposing stack traces to public users.
