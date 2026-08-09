<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $site_name = 'Aqar / عقاري';
    public string $site_email = 'info@aqar.ye';
    public ?string $site_phone = '+967 770 000 000';
    public ?string $site_address = 'المكلا - حضرموت';
    public ?string $site_country = 'اليمن';
    public string $site_currency = 'ر.ي';
    public string $site_default_language = 'ar';
    public ?string $facebook_url = null;
    public ?string $twitter_url = null;
    public ?string $github_url = null;
    public ?string $youtube_url = null;
    public string $footer_copyright = '© Aqar (عقاري). جميع الحقوق محفوظة.';

    public static function group(): string
    {
        return 'general';
    }
}