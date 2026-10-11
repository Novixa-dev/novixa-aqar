<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $site_name = 'نوفيكسا عقار | Novixa Aqar';
    public string $site_email = 'info@novixa.dev';
    public ?string $site_phone = '+967 776 248 526';
    public ?string $site_address = 'المكلا - حضرموت';
    public ?string $site_country = 'اليمن';
    public string $site_currency = 'ر.ي';
    public string $site_default_language = 'ar';
    public ?string $facebook_url = null;
    public ?string $twitter_url = null;
    public ?string $github_url = null;
    public ?string $youtube_url = null;
    public string $footer_copyright = '© نوفيكسا عقار. جميع الحقوق محفوظة. تطوير Novixa.';

    public static function group(): string
    {
        return 'general';
    }
}