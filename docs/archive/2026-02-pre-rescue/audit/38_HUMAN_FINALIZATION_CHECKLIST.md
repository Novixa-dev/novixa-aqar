# 38 — Human Owner Finalization Checklist

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Target Audience**: Novixa Business Owner, Lead Product Manager & Release Engineer  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team  

---

## 1. Executive Summary

This checklist outlines the non-code, operational, legal, credential, and branding items that **require human action** by the Novixa business owner and operations team before public production launch.

---

## 2. Actionable Owner Checklist

### A. Branding & Visual Assets

| Item | Specification / Format | Location in Codebase | Status |
|---|---|---|---|
| [ ] **Official Vector Logo** | High-res SVG & transparent PNG (light & dark mode variants) | `public/images/logo.svg`, `public/images/logo-dark.svg` | Pending Owner Asset |
| [ ] **Favicon Suite** | Multi-size favicon (`.ico`, 16x16, 32x32, 180x180 `apple-touch-icon.png`) | `public/favicon.ico`, `public/apple-touch-icon.png` | Pending Owner Asset |
| [ ] **Default OpenGraph Banner** | 1200 x 630 px JPEG/PNG with Novixa Aqar branding and taglines | `public/images/og-default.jpg` | Pending Owner Asset |
| [ ] **Hero Section Background** | High-res photography of Mukalla / Yemeni real estate | `public/images/hero-bg.jpg` | Pending Owner Asset |
| [ ] **Real Property Media** | Replace demo property photos with active agency listings | Live admin panel / media library | Operational |

---

### B. Business & Contact Identity

| Item | Description | Location in Codebase | Status |
|---|---|---|---|
| [ ] **Official WhatsApp Number** | Business WhatsApp number with Yemen country code (e.g. `+967 77X XXX XXX`) | `.env` (`WHATSAPP_NUMBER`) & General Settings | Required |
| [ ] **Branch Contact Details** | Actual street addresses, phone numbers, and working hours for Mukalla, Aden, Sana'a | Live Admin Settings / Branch Management | Required |
| [ ] **Support Email Address** | Official customer service email (`support@novixa.ye` or `info@novixa.ye`) | `.env` (`MAIL_FROM_ADDRESS`) | Required |
| [ ] **Terms of Service & Privacy Policy** | Verified legal text customized for Yemeni real estate regulations | `resources/views/pages/terms.blade.php`, `privacy.blade.php` | Legal Review |
| [ ] **Commercial Registration Details** | Company CR number, tax number, and official licensed brokerage entity name | Footer & About Page | Required |

---

### C. Cloud Infrastructure & Third-Party API Keys

| Service | Purpose | Target Environment Variable | Priority |
|---|---|---|---|
| [ ] **Transactional Mailer** | Postmark / AWS SES / Mailgun / Resend SMTP credentials | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` | **CRITICAL** |
| [ ] **Google Analytics 4** | Web traffic measurement and conversion tracking | `GOOGLE_TAG_MANAGER_ID` or `GA_MEASUREMENT_ID` | **HIGH** |
| [ ] **Sentry Exception Monitoring**| Real-time runtime crash alerting | `SENTRY_LARAVEL_DSN` | **HIGH** |
| [ ] **Payment Gateway (Optional)** | Stripe live keys for online tenancy deposits | `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` | Optional |
| [ ] **Map Tiles / Geo API** | Mapbox / Leaflet custom tile provider credentials | Configured in `config/services.php` | Medium |
| [ ] **Holographic / 3D Key** | Looking Glass API credentials (if cloud 3D rendering used) | `HOLOGRAPHIC_API_KEY` | Optional |

---

### D. Private GitHub Repository & Server Setup

| Step | Action Required | Responsible Party | Status |
|---|---|---|---|
| [ ] **Create Private GitHub Repo** | Create `https://github.com/novixa/aqar.git` under the Novixa GitHub Organization | Novixa Git Admin | Required |
| [ ] **Configure GitHub Secrets** | Add production SSH deployment keys, DB passwords, and Sentry DSN to GitHub Actions Secrets | DevOps Lead | Required |
| [ ] **Provision Production Server** | Deploy Ubuntu 24.04 LTS VPS with Nginx, PHP 8.4+, MySQL 8.0, Redis, and Certbot SSL | DevOps Lead | Required |
| [ ] **Create First Super Admin** | Run `php artisan make:filament-user` on production server with strong password | Novixa System Admin | **CRITICAL** |
