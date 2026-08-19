<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', config('app.name', 'Dar Hadhramaut Real Estate | دار حضرموت العقارية'));
        $this->migrator->add('general.site_email', 'info@darhadhramaut.ye');
        $this->migrator->add('general.site_phone', '+967 776 248 526');
        $this->migrator->add('general.site_address', '');
        $this->migrator->add('general.site_country', '');
        $this->migrator->add('general.site_currency', 'ر.ي');
        $this->migrator->add('general.site_default_language', 'ar');
        $this->migrator->add('general.facebook_url', null);
        $this->migrator->add('general.twitter_url', null);
        $this->migrator->add('general.github_url', null);
        $this->migrator->add('general.youtube_url', null);
        $this->migrator->add('general.footer_copyright', '© ' . date('Y') . ' دار حضرموت العقارية. جميع الحقوق محفوظة.');
    }
};