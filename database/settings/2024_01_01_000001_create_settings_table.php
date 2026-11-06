<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', config('app.name', config('branding.name.en')));
        $this->migrator->add('general.site_email', config('branding.email'));
        $this->migrator->add('general.site_phone', config('branding.phone'));
        $this->migrator->add('general.site_address', '');
        $this->migrator->add('general.site_country', '');
        $this->migrator->add('general.site_currency', 'ر.ي');
        $this->migrator->add('general.site_default_language', 'ar');
        $this->migrator->add('general.facebook_url', null);
        $this->migrator->add('general.twitter_url', null);
        $this->migrator->add('general.github_url', null);
        $this->migrator->add('general.youtube_url', null);
        $this->migrator->add('general.footer_copyright', '© ' . date('Y') . ' ' . config('branding.name.ar') . '. جميع الحقوق محفوظة.');
    }
};