<?php

namespace App\Support;

use App\Settings\GeneralSettings;

/**
 * Locale-aware access to this deployment's brand. Shared with every view as
 * $brand (see AppServiceProvider), e.g. {{ $brand->name() }}.
 *
 * Contact-detail accessors (email/phone/address) read the admin-editable
 * GeneralSettings row first, falling back to config/branding.php (the
 * .env-driven per-deployment defaults, and what SiteSettingsSeeder seeds
 * GeneralSettings from in the first place) when a value was never set.
 * Before this, GeneralSettings' "General Settings" admin page was a
 * confirmed dead end: edits there never reached any public page, which
 * only ever read config('branding.*').
 */
class Brand
{
    public function name(?string $locale = null): string
    {
        return $this->localized('name', $locale);
    }

    /** "نوفيكسا عقار | Novixa Aqar" — both names, for og:site_name and the like. */
    public function bilingualName(): string
    {
        return $this->name('ar').' | '.$this->name('en');
    }

    public function tagline(?string $locale = null): string
    {
        return $this->localized('tagline', $locale);
    }

    public function description(?string $locale = null): string
    {
        return $this->localized('description', $locale);
    }

    /** Page title with the brand appended: "About Us | Novixa Aqar". */
    public function title(?string $page = null): string
    {
        return $page ? $page.' | '.$this->name() : $this->name().' | '.$this->tagline();
    }

    public function address(?string $locale = null): string
    {
        // GeneralSettings has one site_address field (SiteSettingsSeeder seeds
        // it from the English config value), while config/branding.php's
        // address is genuinely bilingual -- so this can only safely override
        // the localized default once an admin has actually typed something
        // different from what was seeded; otherwise an Arabic visitor would
        // see the seeded English address verbatim.
        $settingsValue = trim((string) (app(GeneralSettings::class)->site_address ?? ''));
        $seededDefault = trim((string) config('branding.address.en'));

        return ($settingsValue !== '' && $settingsValue !== $seededDefault)
            ? $settingsValue
            : $this->localized('address', $locale);
    }

    public function company(): string
    {
        return (string) config('branding.company.name');
    }

    public function companyUrl(): string
    {
        return (string) config('branding.company.url');
    }

    public function email(): string
    {
        $settingsValue = trim((string) (app(GeneralSettings::class)->site_email ?? ''));

        return $settingsValue !== '' ? $settingsValue : (string) config('branding.email');
    }

    public function phone(): string
    {
        $settingsValue = trim((string) (app(GeneralSettings::class)->site_phone ?? ''));

        return $settingsValue !== '' ? $settingsValue : (string) config('branding.phone');
    }

    /** tel: / wa.me friendly digits, e.g. 967776248526. */
    public function phoneDigits(): string
    {
        return preg_replace('/\D/', '', $this->phone());
    }

    public function ogImage(): string
    {
        $image = (string) config('branding.og_image');

        // url(), not asset(): this app sets asset_url to "/", so asset()
        // returns a relative path, and social platforms ignore og:image
        // unless it is absolute.
        return str_starts_with($image, 'http') ? $image : url($image);
    }

    public function indexable(): bool
    {
        return (bool) config('branding.indexable');
    }

    public function themeColor(): string
    {
        return (string) config('branding.theme_color');
    }

    /**
     * Organization + WebSite structured data for the homepage (Google's
     * site-name and sitelinks-search-box signals). RealEstateAgent is the
     * schema.org Organization subtype for a property agency. Built here,
     * not in Blade, because '@context' would be read as a Blade directive.
     */
    public function homepageJsonLd(): string
    {
        $home = url('/');
        $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';

        return json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'RealEstateAgent',
                    '@id' => $home.'#organization',
                    'name' => $this->name(),
                    'alternateName' => $this->name($otherLocale),
                    'url' => $home,
                    'logo' => $this->ogImage(),
                    'image' => $this->ogImage(),
                    'description' => $this->description(),
                    'email' => $this->email(),
                    'telephone' => $this->phone(),
                    'address' => ['@type' => 'PostalAddress', 'streetAddress' => $this->address(), 'addressCountry' => 'YE'],
                    'areaServed' => ['@type' => 'Country', 'name' => 'Yemen'],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $home.'#website',
                    'name' => $this->name(),
                    'url' => $home,
                    'inLanguage' => ['ar', 'en'],
                    'publisher' => ['@id' => $home.'#organization'],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => ['@type' => 'EntryPoint', 'urlTemplate' => url('/properties').'?search={search_term_string}'],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    }

    private function localized(string $key, ?string $locale): string
    {
        $locale ??= app()->getLocale();
        $values = (array) config("branding.{$key}");

        return (string) ($values[$locale] ?? $values['en'] ?? reset($values));
    }
}
