<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings\GeneralSettings;

class SiteSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = app(GeneralSettings::class);

        $settings->site_name = app(\App\Support\Brand::class)->bilingualName();
        $settings->site_email = config('branding.email');
        $settings->site_phone = config('branding.phone');
        $settings->site_address = config('branding.address.en');
        $settings->site_country = 'Yemen';
        $settings->site_currency = 'ر.ي';
        $settings->site_default_language = 'ar';
        $settings->facebook_url = null;
        $settings->twitter_url = null;
        $settings->github_url = null;
        $settings->youtube_url = null;
        $settings->footer_copyright = '© '.date('Y').' '.config('branding.name.en').'. All rights reserved. Developed by '.config('branding.company.name').'.';

        $settings->save();
    }
}
