<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings\GeneralSettings;

class SiteSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = app(GeneralSettings::class);

        $settings->site_name = config('app.name', 'Aqar / عقاري');
        $settings->site_email = 'info@aqar.ye';
        $settings->site_phone = '+967 770 000 000';
        $settings->site_address = 'Al-Mukalla, Hadhramaut, Yemen';
        $settings->site_country = 'Yemen';
        $settings->site_currency = 'ر.ي';
        $settings->site_default_language = 'ar';
        $settings->facebook_url = null;
        $settings->twitter_url = null;
        $settings->github_url = null;
        $settings->youtube_url = null;
        $settings->footer_copyright = '© ' . date('Y') . ' ' . config('app.name', 'Aqar / عقاري') . '. All rights reserved.';

        $settings->save();
    }
}
