# TASK-010 — Owner Property Submission

## Status
IN_PROGRESS

## Objective
Enhance the owner/landlord property submission workflow (`PropertySubmissionForm`), enabling property owners in Al-Mukalla and Yemen to submit listing applications with Arabic localization, neighborhood selection, media uploads, and pending status agent review.

## Why
Allowing property owners to submit their properties directly expands listing inventory for real estate offices while ensuring all incoming submissions are placed in `pending` status for agent verification and approval before public publication.

## Scope
### In scope
- Localized Arabic form UI for property owners (`property-submission-form.blade.php`).
- Form validation for Title, Location, Price (`ر.ي`), Neighborhood, Bedrooms, Bathrooms, Area, Property Type, and Contact Phone.
- Support for selecting Al-Mukalla neighborhoods.
- Image and video upload attachments via Spatie Media Library.
- Setting property status to `pending` upon creation.
- Automated feature tests verifying property submission and status lifecycle.

### Out of scope
- Automated AI photo enhancement (out of MVP scope).

## Existing Implementation
- Component: `app/Livewire/PropertySubmissionForm.php`
- View: `resources/views/livewire/property-submission-form.blade.php`
- Model: `app/Models/Property.php`

## Data Model Impact
- None. Reuses existing `properties`, `neighborhoods`, and `media` tables.

## UI/UX Impact
- Fully responsive, RTL Arabic property submission wizard/form with step indicators, photo upload previews, and instant validation feedback.

## Localization Impact
- Arabic form headers, field placeholders, neighborhood labels, property category selections, and flash success notifications (`تم إرسال طلب إضافة العقار بنجاح وهو قيد المراجعة`).

## Acceptance Criteria
- [ ] Form validates title, price, location, neighborhood, area, and owner phone.
- [ ] Submitted property receives `pending` status.
- [ ] Photos/videos attached cleanly to media collection.
- [ ] Feature tests pass cleanly.

## Verification
```bash
php vendor/bin/phpunit --filter OwnerPropertySubmissionTest
php vendor/bin/phpunit
```

## Files Changed
- `app/Livewire/PropertySubmissionForm.php`
- `resources/views/livewire/property-submission-form.blade.php`
- `tests/Feature/OwnerPropertySubmissionTest.php`
- `docs/tasks/TASK-010-OWNER-SUBMISSION-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
