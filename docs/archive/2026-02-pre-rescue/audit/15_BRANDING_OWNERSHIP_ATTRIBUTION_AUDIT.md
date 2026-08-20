# 15 — Branding, Ownership & License Attribution Audit Report

**Product**: Aqar (عقاري) — Novixa Private Edition  

---

## 1. Branding & Attribution Inventory Table

| Reference | File Location | Line Number | Type | Current Purpose | Must Change? | Recommendation |
|---|---|---|---|---|---|---|
| `liberu-real-estate/real-estate-laravel` | [`composer.json`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/composer.json#L2) | Line 2 | Package Name | Composer package identity | **YES** | Change to `novixa/aqar`. |
| `Liberu Real Estate` | [`database/settings/2024_01_01_000001_create_settings_table.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/settings/2024_01_01_000001_create_settings_table.php#L18) | Line 18 | Default Setting | Initial site name fallback | **YES** | Change fallback to `Aqar`. |
| `Liberu Real Estate` | [`app/Filament/App/Widgets/SocialLinksWidget.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Filament/App/Widgets/SocialLinksWidget.php#L12) | Line 12 | Widget Text | Social links header | **YES** | Change to `Aqar Real Estate`. |
| `liberu_real_estate` | [`k8s/configmap.yaml`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/configmap.yaml) | Multiple | K8s Config | K8s DB / App Labels | **YES** | Update container label to `aqar`. |
| `MIT License` | [`LICENSE.md`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/LICENSE.md) | Root | Open-Source License | Package license declaration | **NO (Legally Required)** | Keep MIT license notice for open-source foundation while maintaining Novixa ownership over customized code. |

---

## 2. Open-Source Compliance & Legal Guidelines

- **MIT License Integrity**: The original open-source base project operates under the permissive **MIT License**.
- **Novixa Customization Rights**: Under MIT terms, Novixa has full legal right to modify, productize, rebrand, and deploy the application commercially as a private product.
- **Attribution Policy**: Maintain original MIT license file in repository root (`LICENSE.md`) while branding all user-facing UI, documentation, metadata, and environment configs under **Novixa** / **Aqar**.
