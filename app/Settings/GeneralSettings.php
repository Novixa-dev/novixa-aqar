<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $site_name = 'Novixa Aqar | نوفيكسا عقار';
    public string $site_email = 'contact@novixa-aqar.com';
    public ?string $site_phone = '+967 770 000 000';
    public ?string $site_address = 'المكلا - حضرموت';
    public ?string $site_country = 'اليمن';
    public string $site_currency = 'ر.ي';
    public string $site_default_language = 'ar';
    public ?string $facebook_url = null;
    public ?string $twitter_url = null;
    public ?string $github_url = null;
    public ?string $youtube_url = null;
    public string $footer_copyright = '© 2026 Novixa Aqar (نوفيكسا عقار). جميع الحقوق محفوظة لـ Novixa و AK01REDWAN.';

    public static function group(): string
    {
        return 'general';
    }
}