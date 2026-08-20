# 27 — Branding & Identity Consistency Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Intended Brand Standard**: **Novixa** / **Aqar (عقاري)** / **نوفيكسا عقار**  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team  

---

## 1. Executive Summary

This audit evaluates the consistency of the project's brand presentation across all user-facing, administrative, email, metadata, database, and infrastructure touchpoints.

---

## 2. Branding Touchpoint Evaluation Matrix

| Touchpoint Area | Evaluated File / Component | Current Brand String | Consistency Status | Required Rectification |
|---|---|---|---|---|
| **Filament Admin Panel** | [`AdminPanelProvider.php#L39`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Providers/Filament/AdminPanelProvider.php#L39) | `شركة الأفق العقارية | لوحة التحكم الإدارية` | **INCONSISTENT** | Update brandName to `'نوفيكسا عقار | لوحة الإدارة العليا'` or `'نوفيكسا عقار | Novixa Aqar'`. |
| **Filament Role Panels (8 Panels)** | `Agent`, `App`, `Buyer`, `Contractor`, `Landlord`, `Seller`, `Staff`, `Tenant` Providers | `نوفيكسا عقار | Novixa Aqar` | **CONSISTENT** | Complies with Novixa branding standard. |
| **Public Frontend Layout** | [`resources/views/layouts/app.blade.php#L8`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/layouts/app.blade.php#L8) | `شركة الأفق العقارية | أفق اليمن للعقارات والاستثمار` | **INCONSISTENT** | Replace fallback title with `'عقاري | منصة العقارات الأولى في اليمن - نوفيكسا'` / `'Novixa Aqar | Premier Real Estate Platform'`. |
| **Welcome / Landing Page** | [`resources/views/welcome.blade.php#L7`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/welcome.blade.php#L7) | `Aqar / عقاري - نظام إدارة وتسويق العقارات` | **CONSISTENT** | Matches product name. |
| **Component Layout** | [`resources/views/components/layouts/app.blade.php#L10`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/components/layouts/app.blade.php#L10) | `{{ config('app.name') }}` | **CONSISTENT** | Dynamic bind to `APP_NAME`. |
| **Property Detail Title** | [`resources/views/livewire/property-detail.blade.php#L3`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/livewire/property-detail.blade.php#L3) | `{{ $property->title }} | {{ app(\App\Settings\GeneralSettings::class)->site_name }}` | **CONSISTENT** | Dynamic bind to Settings. |
| **Settings Table Default** | [`2024_01_01_000001_create_settings_table.php#L15`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/settings/2024_01_01_000001_create_settings_table.php#L15) | `'site_name' => 'Aqar'` | **CONSISTENT** | Configured as `Aqar`. |
| **Environment Default (Prod/Dev)** | [`.env.example#L1`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.env.example#L1) | `APP_NAME="Aqar"` | **CONSISTENT** | Properly set to Aqar. |
| **Environment Default (Testing)** | [`.env.testing#L1`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.env.testing#L1) | `APP_NAME="Liberu Real Estate"` | **INCONSISTENT** | Change to `APP_NAME="Aqar Testing"`. |
| **Email Sender Name** | [`.env.example#L47`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/.env.example#L47) | `MAIL_FROM_NAME="${APP_NAME}"` | **CONSISTENT** | Automatically follows `APP_NAME`. |
| **Composer Package ID** | [`composer.json#L2`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/composer.json#L2) | `liberu-real-estate/real-estate-laravel` | **INCONSISTENT** | Update to `novixa/aqar`. |
| **Kubernetes ConfigMap** | [`k8s/configmap.yaml#L13`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/k8s/configmap.yaml#L13) | `APP_NAME: "Liberu Real Estate"` | **INCONSISTENT** | Update to `APP_NAME: "Novixa Aqar"`. |
| **Footer & Contact Details** | [`resources/views/layouts/app.blade.php#L170-L210`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/resources/views/layouts/app.blade.php) | Yemen Al-Mukalla Branches & Contact Info | **CONSISTENT** | Al-Mukalla, Sana'a, and Aden branches localized. |
| **WhatsApp Direct Link** | [`app/Helpers/AqarContactHelper.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/app/Helpers/AqarContactHelper.php) | Yemen prefix (+967) formatted | **CONSISTENT** | Pre-fills Arabic inquiry messages. |

---

## 3. Brand Identity Conflicts Identified

1. **"شركة الأفق العقارية" (Al-Ofuq Real Estate)**: Appears in `AdminPanelProvider.php` and `layouts/app.blade.php`. This appears to be an earlier demo/client placeholder name that must be normalized to **نوفيكسا عقار (Novixa Aqar)**.
2. **"Liberu Real Estate"**: Appears in `.env.testing` and `k8s/configmap.yaml`.

---

## 4. Brand Release Checklist

- [ ] Update `AdminPanelProvider.php` line 39 to `'نوفيكسا عقار | لوحة الإدارة'`.
- [ ] Update `resources/views/layouts/app.blade.php` line 8 title fallback to `'عقاري | نوفيكسا للعقارات'`.
- [ ] Update `.env.testing` line 1 to `APP_NAME="Aqar Testing"`.
- [ ] Update `k8s/configmap.yaml` line 13 to `APP_NAME: "Novixa Aqar"`.
- [ ] Update `composer.json` line 2 to `"name": "novixa/aqar"`.
