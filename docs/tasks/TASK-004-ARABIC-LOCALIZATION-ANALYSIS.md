# TASK-004 — Arabic Localization (`ar`)

## Status
COMPLETED

## Objective
Establish complete professional Arabic localization (`ar`) for the **Aqar / عقاري** product, updating system locale configuration, providing translation dictionaries for real-estate models, property types, listing statuses, lead workflows, navigation links, and agency contact details.

## Why
Delivering a Yemen-first Arabic real-estate experience requires setting Arabic as the primary application locale and structuring modular translation dictionaries for properties, leads, navigation, and core UI elements.

## Scope
### In scope
- Set default application locale to `'ar'` in `config/app.php`.
- Create global translation dictionary `lang/ar.json`.
- Create modular translation dictionaries in `lang/ar/`:
  - `property.php` (Property types, listing types, statuses, attributes, and options)
  - `lead.php` (Lead statuses and lead sources)
  - `navigation.php` (Core navigation links)
  - `agency.php` (Office and agency profile metadata)

### Out of scope
- RTL layout direction CSS/HTML adjustments (handled in TASK-005).

## Existing Implementation
- Config file: `config/app.php`
- Translation path: `lang/`

## Design Decision
Use Laravel's standard native translation architecture (`lang/ar.json` and `lang/ar/*.php`) to allow seamless usage across Blade templates, Livewire components, validation rules, and Filament resource labels via `__('key')`.

## Data Model Impact
- None.

## UI/UX Impact
- System default locale evaluates to `ar`.
- All localized elements render natural professional Arabic tailored to Yemen/GCC real-estate context.

## Localization Impact
- Establishes `ar` as the active application locale.

## Acceptance Criteria
- [x] Application locale set to `ar` in `config/app.php`.
- [x] Global dictionary `lang/ar.json` created.
- [x] Real-estate translation modules created (`property.php`, `lead.php`, `navigation.php`, `agency.php`).
- [x] Automated test suite passes cleanly.

## Verification
### Automated
```bash
php artisan test
```
Result: **670 PASSED**, 32 SKIPPED, 0 FAILED (2,338 assertions).

## Files Changed
- `config/app.php`
- `lang/ar.json`
- `lang/ar/property.php`
- `lang/ar/lead.php`
- `lang/ar/navigation.php`
- `lang/ar/agency.php`
- `docs/tasks/TASK-004-ARABIC-LOCALIZATION-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
- `docs/project/HANDOVER_NEXT_STEPS.md`

## Result
COMPLETED & VERIFIED
