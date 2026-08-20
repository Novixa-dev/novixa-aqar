# TASK-012 — SEO / OpenGraph Polish

## Status
IN_PROGRESS

## Objective
Enhance search engine optimization (SEO), social media sharing previews (OpenGraph / Twitter Cards), Schema.org real estate structured data (JSON-LD), and automated XML sitemap generation for Aqar.

## Why
When users share property listings on social media (WhatsApp, Facebook, Twitter, Telegram), OpenGraph tags render eye-catching preview cards with property images, title, price (`ر.ي`), and location. Additionally, Schema.org JSON-LD structured data helps Google present rich snippets for real estate listings in Al-Mukalla and Yemen.

## Scope
### In scope
- Master layout `<head>` enhancements with `@stack('meta')` in `resources/views/layouts/app.blade.php`.
- Property detail page OpenGraph meta tags (`og:title`, `og:description`, `og:image`, `og:url`, `twitter:card`) in `property-detail.blade.php`.
- Schema.org `RealEstateListing` JSON-LD markup on property detail pages.
- Artisan command `sitemap:generate` (`app/Console/Commands/GenerateSitemapCommand.php`) using `spatie/laravel-sitemap`.
- Public XML sitemap route `/sitemap.xml` in `routes/web.php`.
- Automated feature tests verifying meta tag generation and sitemap endpoint response.

### Out of scope
- Multi-region geo-targeted subdomains (out of MVP scope).

## Existing Implementation
- Package: `spatie/laravel-sitemap`
- View: `resources/views/layouts/app.blade.php` & `resources/views/livewire/property-detail.blade.php`

## Data Model Impact
- None.

## UI/UX Impact
- Rich social media sharing previews when property URLs are copied or shared via social media or messaging platforms.

## Localization Impact
- Localized Arabic meta descriptions, property titles, Yemeni Rial price formatting (`ر.ي`), and location summaries.

## Acceptance Criteria
- [ ] Master layout contains `@stack('meta')` for dynamic head tags.
- [ ] Property detail page includes `og:title`, `og:image`, `og:description`, and Schema.org JSON-LD snippet.
- [ ] `php artisan sitemap:generate` builds `public/sitemap.xml`.
- [ ] `/sitemap.xml` route returns 200 OK with valid XML.
- [ ] Feature tests pass cleanly.

## Verification
```bash
php vendor/bin/phpunit --filter SeoOpenGraphTest
php vendor/bin/phpunit
```

## Files Changed
- `resources/views/layouts/app.blade.php`
- `resources/views/livewire/property-detail.blade.php`
- `app/Console/Commands/GenerateSitemapCommand.php`
- `routes/web.php`
- `tests/Feature/SeoOpenGraphTest.php`
- `docs/tasks/TASK-012-SEO-OPENGRAPH-ANALYSIS.md`
- `docs/project/WORKFLOW.md`
