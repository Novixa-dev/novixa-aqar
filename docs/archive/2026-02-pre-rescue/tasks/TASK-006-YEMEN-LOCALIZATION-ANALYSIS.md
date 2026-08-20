# TASK-006 — Yemen Localization (`YER` & Al-Mukalla Data)

## Status
COMPLETED

## Objective
Configure Yemeni Rial (`ر.ي` / `YER`) as the default site currency across settings and seeders, update location & neighborhood seeders with Al-Mukalla / Hadhramaut districts (Fuwah, Al-Dais, Al-Sharj, Al-Salam, Buwaysh, Khalaf), and provide localized Yemen location helper dictionaries.

## Why
Aqar is tailored specifically as a turnkey real-estate product for Yemen first (Al-Mukalla / Hadhramaut), requiring localized currency symbols, geographic districts, and city metadata.

## Scope
### In scope
- Set `$site_currency = 'ر.ي'` in `GeneralSettings.php`.
- Set `'general.site_currency'` fallback default to `'ر.ي'` in settings migration.
- Set `$settings->site_currency = 'ر.ي'` in `SiteSettingsSeeder.php`.
- Update `NeighborhoodSeeder.php` with Al-Mukalla districts (`فوه`, `الديس`, `الشرج`, `حي السلام`, `بويش`, `خلف`).
- Create `lang/ar/location.php` dictionary for Yemen governorates and cities.

### Out of scope
- Live multi-currency conversion exchange rate API integration (deferred).

## Existing Implementation
- Settings: `app/Settings/GeneralSettings.php`
- Seeders: `database/seeders/NeighborhoodSeeder.php`, `database/seeders/SiteSettingsSeeder.php`

## Design Decision
Use `ر.ي` as the explicit visual currency symbol throughout the frontend and admin panels while preserving numerical currency handling.

## Data Model Impact
- Updated default setting values for `general.site_currency`.
- Updated neighborhood seed records to represent Al-Mukalla districts.

## UI/UX Impact
- Property prices display `ر.ي` as currency symbol across lists, search filters, and detail pages.
- Neighborhood filters and select options display Al-Mukalla districts.

## Localization Impact
- Establishes Yemeni Rial (`ر.ي`) and Al-Mukalla / Hadhramaut context as default demo state.

## Acceptance Criteria
- [x] Site currency setting updated to `ر.ي`.
- [x] Neighborhood seeder populates Al-Mukalla districts.
- [x] Yemen location translation dictionary created (`lang/ar/location.php`).
- [x] Automated test suite passes cleanly.

## Verification
### Automated
```bash
php artisan test
```
Result: **670 PASSED**, 32 SKIPPED, 0 FAILED (2,338 assertions).

## Files Changed
- `app/Settings/GeneralSettings.php`
- `database/settings/2024_01_01_000001_create_settings_table.php`
- `database/seeders/SiteSettingsSeeder.php`
- `database/seeders/NeighborhoodSeeder.php`
- `lang/ar/location.php`
- `docs/tasks/TASK-006-YEMEN-LOCALIZATION-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
- `docs/project/HANDOVER_NEXT_STEPS.md`

## Result
COMPLETED & VERIFIED
