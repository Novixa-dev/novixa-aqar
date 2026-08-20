# 13 — Dependency & Package Audit Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Composer Package Inventory & Audit

Audited [`composer.json`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/composer.json).

| Package | Version | Purpose | Status / Risk | Recommendation |
|---|---|---|---|---|
| `laravel/framework` | `^13.0` | Core Application Framework | Stable | Keep |
| `filament/filament` | `^5.0` | Admin Panel | Stable | Keep |
| `livewire/livewire` | `^4.0` | Frontend Component Reactivity | Stable | Keep |
| `spatie/laravel-permission` | `^6.0` | Role & Permission Management | Stable | Keep |
| `spatie/laravel-medialibrary` | `^11.7` | Media Uploads & Processing | Stable | Keep |
| `spatie/laravel-sitemap` | `^8.2` | Dynamic Sitemap Generation | Stable | Keep |
| `barryvdh/laravel-ide-helper` | `^3.7` | IDE Code Autocompletion | Dev Only | Keep in `require-dev` |
| `stripe/stripe-php` | `^20.0` | Payment Gateway SDK | Production Ready | Keep |

---

## 2. NPM Package Inventory & Audit

Audited [`package.json`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/package.json).

| Package | Version | Purpose | Status |
|---|---|---|---|
| `tailwindcss` | `^4.1.17` | Utility-first CSS styling | Active |
| `vite` | `^8.0.14` | Frontend asset bundler | Active |
| `@google/model-viewer` | `^4.2.0` | 3D GLTF/GLB Property Renderer | Active |
| `leaflet` | `^1.9.4` | Interactive Neighborhood Mapping | Active |
| `three` | `^0.182.0` | 3D Graphics Engine | Active |

---

## 3. Package Name & Metadata Action

- **Issue**: [`composer.json#L2`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/composer.json#L2) specifies `"name": "liberu-real-estate/real-estate-laravel"`.
- **Action Required**: Update package name to `"name": "novixa/aqar"` prior to creating the new GitHub repository.
