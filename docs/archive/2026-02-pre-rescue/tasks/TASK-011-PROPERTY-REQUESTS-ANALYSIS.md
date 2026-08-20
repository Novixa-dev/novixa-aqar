# TASK-011 — Property Requests & Buyer Inquiries

## Status
IN_PROGRESS

## Objective
Provide a dedicated custom property request submission workflow (`طلبات العقارات والبحث المخصص`), enabling clients and buyers in Al-Mukalla and Yemen to specify their target property specs, preferred neighborhoods, budget range, and contact details.

## Why
In the Yemeni real estate market, many buyers have specific unlisted requirements (e.g. villa in Fuwah, apartment near Al-Mukalla Corniche, land in Al-Shafe'i). Capturing structured property requests enables agencies to match buyers with existing listings or source targeted properties.

## Scope
### In scope
- Livewire component `PropertyRequestForm` (`app/Livewire/PropertyRequestForm.php`).
- Form validation for Name, Phone, Email, Property Category, Neighborhood, Min/Max Price (`ر.ي`), Bedrooms, Bathrooms, and Notes.
- Storing structured buyer search criteria inside `buyers` database table.
- Localized Arabic RTL request view layout (`property-request-form.blade.php`).
- Automated feature tests verifying property request submission.

### Out of scope
- Third-party external CRM API synchronization (out of MVP scope).

## Existing Implementation
- Model: `app/Models/Buyer.php`
- Service: `app/Services/PropertyMatchingService.php`

## Data Model Impact
- Uses existing `buyers` table with `search_criteria` JSON column.

## UI/UX Impact
- Responsive Arabic request form with step indicators, budget range inputs in `ر.ي`, neighborhood dropdown, and instant submission confirmation.

## Localization Impact
- Arabic form headers, labels, placeholders, category options, and flash success message (`تم تسجيل طلب البحث عن عقارك بنجاح. سيقوم مستشارنا بمطابقة طلبك والتواصل معك قريباً.`).

## Acceptance Criteria
- [ ] Form validates name, phone, property category, price range, and location.
- [ ] `Buyer` record created with `search_criteria` array containing filtered preferences.
- [ ] Feature tests pass cleanly.

## Verification
```bash
php vendor/bin/phpunit --filter PropertyRequestTest
php vendor/bin/phpunit
```

## Files Changed
- `app/Livewire/PropertyRequestForm.php`
- `resources/views/livewire/property-request-form.blade.php`
- `tests/Feature/PropertyRequestTest.php`
- `docs/tasks/TASK-011-PROPERTY-REQUESTS-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
