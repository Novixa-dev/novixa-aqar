# TASK-008 — WhatsApp / Call / Sharing (`wa.me` & Social Sharing)

## Status
COMPLETED

## Objective
Implement dynamic context-aware WhatsApp inquiry links (`wa.me`), standardized phone number sanitization, click-to-call links, and property social sharing / URL copy features across property pages in Aqar.

## Why
In Yemen and the broader GCC real-estate markets, WhatsApp and direct phone calls account for the majority of property lead conversions. Pre-filling WhatsApp messages with property title and ID eliminates friction for buyers/tenants, while multi-channel sharing options enable fast listing distribution via social networks and instant messaging.

## Scope
### In scope
- Standardized WhatsApp URL & phone number sanitization via helper (`AqarContactHelper`).
- Pre-filled Arabic WhatsApp message template containing property title and ID.
- Configurable fallback to `GeneralSettings::site_phone` or property agency phone number.
- Click-to-call link (`tel:`) support.
- Property detail page social sharing bar (WhatsApp, Twitter/X, Facebook, Copy link with visual toast/feedback).
- Property card quick WhatsApp action CTA button.
- Comprehensive automated PHPUnit tests for contact helper and sharing parameters.

### Out of scope
- Automated chat bots or WhatsApp Business API Webhooks (out of MVP scope).

## Existing Implementation
- Settings: `app/Settings/GeneralSettings.php` (`site_phone`)
- View: `resources/views/livewire/property-detail.blade.php`
- Card: `resources/views/livewire/property-list.blade.php`

## Design Decision
Created a centralized helper class `App\Helpers\AqarContactHelper` to provide robust, reusable methods for:
1. `sanitizeForWhatsApp(?string $phone)`: Removes non-digit characters and standardizes Yemeni local prefixes (e.g. `07...` / `7...` -> `967...`) for valid `wa.me` links.
2. `formatForCall(?string $phone)`: Prepares clean `tel:` URIs.
3. `getWhatsAppUrl(?string $phone, string $title, int|string $id)`: Returns pre-filled Arabic `wa.me` inquiry URLs.
4. `getShareUrls(string $url, string $title)`: Returns share URLs for WhatsApp, Twitter/X, Facebook, and Telegram.

## Data Model Impact
- None.

## UI/UX Impact
- Instant direct lead conversion CTAs for WhatsApp and Phone on property detail views and property cards.
- Integrated social share toolbar with copy-to-clipboard functionality and instant feedback.

## Localization Impact
- Pre-filled Arabic template: `السلام عليكم، أستفسر عن العقار: [عنوان العقار] (رقم العقار: #[المعرف])`.
- RTL-compliant toolbar icons and copy actions.

## Acceptance Criteria
- [x] Phone numbers sanitized to valid international format for `wa.me`.
- [x] Pre-filled Arabic WhatsApp message contains title and property ID.
- [x] Click-to-call links use formatted phone.
- [x] Social share toolbar renders correctly on property details page.
- [x] Property cards include quick WhatsApp action.
- [x] Automated test suite passes cleanly.

## Verification
```bash
php vendor/bin/phpunit
```
Result: **706 PASSED**, 32 SKIPPED, **0 FAILED** (2,352 assertions verified).

## Files Changed
- `app/Helpers/AqarContactHelper.php`
- `tests/Unit/AqarContactHelperTest.php`
- `resources/views/livewire/property-detail.blade.php`
- `resources/views/livewire/property-list.blade.php`
- `docs/tasks/TASK-008-WHATSAPP-CALL-SHARING-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
- `docs/project/HANDOVER_NEXT_STEPS.md`

## Result
COMPLETED & VERIFIED

