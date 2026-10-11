<?php

namespace App\Support;

/**
 * Locale-aware access to config/branding.php. Shared with every view as
 * $brand (see AppServiceProvider), e.g. {{ $brand->name() }}.
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
        return $this->localized('address', $locale);
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
        return (string) config('branding.email');
    }

    public function phone(): string
    {
        return (string) config('branding.phone');
    }

    /** tel: / wa.me friendly digits, e.g. 967776248526. */
    public function phoneDigits(): string
    {
        return preg_replace('/\D/', '', $this->phone());
    }

    public function ogImage(): string
    {
        $image = (string) config('branding.og_image');

        return str_starts_with($image, 'http') ? $image : asset($image);
    }

    public function indexable(): bool
    {
        return (bool) config('branding.indexable');
    }

    private function localized(string $key, ?string $locale): string
    {
        $locale ??= app()->getLocale();
        $values = (array) config("branding.{$key}");

        return (string) ($values[$locale] ?? $values['en'] ?? reset($values));
    }
}
