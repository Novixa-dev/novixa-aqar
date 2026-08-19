<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings\GeneralSettings;

class SiteSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = app(GeneralSettings::class);

        $settings->site_name = 'Dar Hadhramaut Real Estate | دار حضرموت العقارية';
        $settings->site_email = 'info@darhadhramaut.ye';
        $settings->site_phone = '+967 776 248 526';
        $settings->site_address = 'Al-Mukalla, Hadhramaut, Yemen';
        $settings->site_country = 'Yemen';
        $settings->site_currency = 'ر.ي';
        $settings->site_default_language = 'ar';
        $settings->facebook_url = null;
        $settings->twitter_url = null;
        $settings->github_url = null;
        $settings->youtube_url = null;
        $settings->footer_copyright = '© ' . date('Y') . ' Dar Hadhramaut Real Estate. All rights reserved. Developed by Novixa.';

        $settings->save();
    }
}
