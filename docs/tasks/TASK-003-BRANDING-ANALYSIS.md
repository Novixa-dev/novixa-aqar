# TASK-003 — Branding & Identity (`Aqar / عقاري`)

## Status
COMPLETED

## Objective
Establish the **Aqar / عقاري** identity ("نظام إدارة وتسويق العقارات") across core framework configuration, Spatie site settings defaults, Filament panel providers, HTML title metadata, and front-end header/navigation blade components.

## Why
Transforming the upstream application into the turnkey **Aqar / عقاري** product requires replacing legacy template brand placeholders with the official bilingual Arabic/English identity.

## Scope
### In scope
- Update application default name in `config/app.php` to `Aqar / عقاري`.
- Update Spatie `GeneralSettings` model and database settings migration fallbacks.
- Update `SiteSettingsSeeder` to use `Aqar / عقاري` and Yemen defaults.
- Brand Filament Admin & Auxiliary Panel Providers with `brandName('عقاري | Aqar')`.
- Update `welcome.blade.php`, `header.blade.php`, `navigation.blade.php`, and `CalendarIntegrationService.php`.

### Out of scope
- Full Arabic translation dictionary overhaul (handled in TASK-004).
- RTL layout structural adjustment (handled in TASK-005).

## Existing Implementation
- Config file: `config/app.php`
- Settings model: `app/Settings/GeneralSettings.php`
- Settings migration: `database/settings/2024_01_01_000001_create_settings_table.php`
- Panel Provider: `app/Providers/Filament/AdminPanelProvider.php`

## Design Decision
Use `Aqar / عقاري` as the universal bilingual brand string for site-wide settings and HTML titles, and `عقاري | Aqar` for Filament panel header branding to ensure prominent Arabic brand visibility while preserving clean layout balance.

## Data Model Impact
- Updated default fallback values for `general.site_name` and `general.footer_copyright` in Spatie settings tables/seeders.

## UI/UX Impact
- Browser tab titles now show `Aqar / عقاري - نظام إدارة وتسويق العقارات`.
- Filament Admin and User Panels render `عقاري | Aqar` branding header.

## Localization Impact
- Establishes `Aqar` (English) and `عقاري` (Arabic) as standard brand identity tokens across the codebase.

## Acceptance Criteria
- [x] Application fallback name updated to `Aqar / عقاري`.
- [x] Spatie `GeneralSettings` fallback defaults updated.
- [x] Filament panel brand set to `عقاري | Aqar`.
- [x] Front-end titles and navigation alt texts updated.
- [x] Automated test suite passes cleanly.

## Verification
### Automated
```bash
php artisan test
```
Result: **670 PASSED**, 32 SKIPPED, 0 FAILED (2,338 assertions).

## Files Changed
- `config/app.php`
- `app/Settings/GeneralSettings.php`
- `database/settings/2024_01_01_000001_create_settings_table.php`
- `database/seeders/SiteSettingsSeeder.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/AppPanelProvider.php`
- `app/Providers/Filament/AgentPanelProvider.php`
- `resources/views/welcome.blade.php`
- `resources/views/components/header.blade.php`
- `resources/views/components/navigation.blade.php`
- `app/Services/CalendarIntegrationService.php`

## Result
COMPLETED & VERIFIED
