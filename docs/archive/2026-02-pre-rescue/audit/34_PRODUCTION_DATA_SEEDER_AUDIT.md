# 34 — Production Database & Seeder Safety Audit

**Product**: Aqar (عقاري) — Novixa Private Edition  
**Audit Purpose**: Categorization of Database Seeders, Demo Data Isolation & Production Seeding Safety  
**Date**: August 15, 2026  
**Auditor**: Independent Senior Release-Gate Team (Security & Database Auditor)  

---

## 1. Executive Summary

This audit evaluates all seeders in `database/seeders/` to determine whether running standard Laravel seeding commands (`php artisan db:seed` or `php artisan migrate --seed`) during production deployment poses security, integrity, or branding risks.

> [!CAUTION]
> **CRITICAL PRODUCTION RISK IDENTIFIED**:  
> Running `DatabaseSeeder` in production will inject 8 demo accounts (including super_admin with password `'password'`), 24 mock properties, artificial leads, and dummy contact phone numbers. Structural and demo seeders must be decoupled immediately.

---

## 2. Comprehensive Seeder Classification Matrix

| Seeder Class | Data Category | Safe for Production? | Records Created | Risk Assessment & Details |
|---|---|---|---|---|
| [`PermissionsSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/PermissionsSeeder.php) | **Foundational / System** | **YES (Required)** | System permissions table | Generates Spatie permissions required for Filament panel authorization and role policies. |
| [`RolesSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/RolesSeeder.php) | **Foundational / System** | **YES (Required)** | Roles (`super_admin`, `staff`, `agent`, `buyer`, `seller`, `landlord`, `tenant`, `contractor`) | Necessary for multi-tenant and multi-panel user authorization. |
| [`PropertyCategorySeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/PropertyCategorySeeder.php) | **Foundational / System** | **YES (Required)** | Categories (Apartment, Villa, Land, Commercial, etc.) | Core categories required for property submission and search filters. |
| [`NeighborhoodSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/NeighborhoodSeeder.php) | **Foundational / Regional** | **YES (Required)** | Al-Mukalla neighborhoods (حي السلام, حي الشرج, حي فوه, 40 شقة) | Essential regional taxonomy for search, dropdowns, and geolocation. |
| [`AppointmentTypeSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/AppointmentTypeSeeder.php) | **Foundational / System** | **YES (Required)** | Appointment types (Viewing, Valuation, Virtual Tour) | Required for booking calendars. |
| [`VirtualTourAppointmentTypeSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/VirtualTourAppointmentTypeSeeder.php) | **Foundational / System** | **YES (Required)** | Virtual tour appointment types | Required for virtual tour bookings. |
| [`MenuSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/MenuSeeder.php) | **Foundational / System** | **YES (Required)** | Navigation menu hierarchy | Builds default navigation bars and footer menus. |
| [`DocumentTemplateSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/DocumentTemplateSeeder.php) | **Foundational / System** | **YES (Required)** | Standard lease/sale agreement templates | Base templates for PDF document generation. |
| [`ComponentSettingsSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/ComponentSettingsSeeder.php) | **Foundational / System** | **YES (Required)** | Component UI configurations | Defaults for homepage hero, counters, and widgets. |
| [`DefaultTeamSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/DefaultTeamSeeder.php) | **Administrative / Hybrid** | **CONDITIONAL** | Default agency team + default branch | Uses dummy address (`123 Main St`) and phone (`+1234567890`). Must be populated with real agency details. |
| [`BranchSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/BranchSeeder.php) | **Demo / Placeholder** | **NO** | Dummy branches with `456 Oak St` | Demo data only. |
| [`UserSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/UserSeeder.php) | **Demo / High Risk** | **STRICTLY NO** | 8 test users with password `'password'` (`admin@example.com`, etc.) | **CRITICAL SECURITY RISK**: Hardcodes super-admin credentials. |
| [`PropertySeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/PropertySeeder.php) | **Demo Data** | **NO** | 24 fictional property listings | Demo showcase data for testing only. |
| [`LeadAndBuyerSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/LeadAndBuyerSeeder.php) | **Demo Data** | **NO** | Dummy leads, viewing requests, and buyer profiles | Demo data for CRM testing only. |
| [`NewsSeeder.php`](file:///c:/Users/ak01redwan/Desktop/mywork/aqar-project/aqar/database/seeders/NewsSeeder.php) | **Demo / Marketing** | **CONDITIONAL** | 6 sample real estate news articles in Arabic | Can serve as initial launch content if reviewed, otherwise replace. |

---

## 3. Recommended Production Seeding Architecture

To guarantee zero accidental contamination in production, refactor the seeding hierarchy into two distinct master classes:

### Architecture Plan:

```
database/seeders/
├── ProductionSeeder.php       # <-- ONLY seeds foundational schema data & prompts for real admin
│   ├── PermissionsSeeder
│   ├── RolesSeeder
│   ├── PropertyCategorySeeder
│   ├── NeighborhoodSeeder
│   ├── AppointmentTypeSeeder
│   ├── VirtualTourAppointmentTypeSeeder
│   ├── MenuSeeder
│   ├── DocumentTemplateSeeder
│   └── ComponentSettingsSeeder
│
└── DemoSeeder.php             # <-- Development / Staging only
    ├── ProductionSeeder
    ├── UserSeeder             (demo accounts)
    ├── PropertySeeder         (24 demo properties)
    ├── LeadAndBuyerSeeder     (demo CRM data)
    └── NewsSeeder             (sample articles)
```

---

## 4. Production Deployment Command Rule

In production deployment runbooks and CI/CD pipelines, **NEVER** run:
```bash
# FORBIDDEN IN PRODUCTION
php artisan migrate:fresh --seed
```

**ALWAYS** run:
```bash
# APPROVED PRODUCTION COMMAND
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
php artisan make:filament-user   # (or dedicated secure admin creation command)
```
