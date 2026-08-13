# TASK-014 — Public UI & Component Polish

## Status
IN_PROGRESS

## Objective
Enhance and refine the public UI components, responsive layout drawers, Arabic typography alignment, property card micro-animations, and CTA buttons to deliver a state-of-the-art visual experience localized for Yemen & Hadramout real estate clients.

## Why
Delivering an aesthetically pleasing, modern, and perfectly aligned RTL user experience enhances user trust, conversion rates on WhatsApp/call actions, and provides a polished presentation for real estate offices.

## Scope
### In scope
- Enhanced typography and spacing rules in `resources/css/app.css` (Cairo / Tajawal / Inter font hierarchy, custom RTL utilities, smooth micro-interactions).
- Polished navbar & mobile drawer menu in `resources/views/components/home-navbar.blade.php`.
- Enhanced property listing cards in `resources/views/livewire/property-list.blade.php` and `resources/views/components/property-card.blade.php` (if present).
- Polished CTA buttons (WhatsApp brand green `bg-emerald-600 hover:bg-emerald-700`, direct call action, owner submission form button styling).
- Automated feature test `UiPolishTest.php` verifying public page render, RTL indicators, and essential UI structural elements.
- Documentation and workflow tracking update.

### Out of scope
- Complete replacement of backend admin panel theme (Filament handles admin theme).

## Existing Implementation
- `resources/css/app.css`
- `resources/views/layouts/app.blade.php`
- `resources/views/components/home-navbar.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/livewire/property-list.blade.php`

## Data Model Impact
- None. UI/CSS blade changes only.

## UI/UX Impact
- Responsive, elegant RTL layout with vibrant micro-interactions, clean cards, polished badges, and high contrast action buttons.

## Localization Impact
- 100% Arabic/RTL visual symmetry across all viewports.

## Acceptance Criteria
- [ ] Custom CSS styling in `app.css` loaded cleanly.
- [ ] Mobile navbar toggle drawer opens and closes smoothly.
- [ ] Property cards display clear badges (`للبيع` / `للإيجار`), Yemeni Rial formatting, and location icons.
- [ ] WhatsApp CTA buttons use distinct brand styling.
- [ ] Feature test `UiPolishTest.php` passes cleanly.

## Verification
```bash
php vendor/bin/phpunit --filter UiPolishTest
php vendor/bin/phpunit
```

## Files Changed
- `resources/css/app.css`
- `resources/views/components/home-navbar.blade.php`
- `tests/Feature/UiPolishTest.php`
- `docs/tasks/TASK-014-UI-POLISH-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
