<?php

namespace App\Filament\Admin\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return \App\Filament\Support\NavigationGroup::Settings;
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'General Settings';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الإعدادات العامة' : 'General Settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Site Information')
                    ->schema([
                        TextInput::make('site_name')
                            ->label(__('filament.fields.site_name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_email')
                            ->label(__('filament.fields.site_email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('site_phone')
                            ->label(__('filament.fields.site_phone'))
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('site_address')
                            ->label(__('filament.fields.site_address'))
                            ->maxLength(255),
                        TextInput::make('site_country')
                            ->label(__('filament.fields.country'))
                            ->maxLength(255),
                        TextInput::make('site_currency')
                            ->label(__('filament.fields.currency_symbol'))
                            ->maxLength(10),
                        TextInput::make('site_default_language')
                            ->label(__('filament.fields.default_language'))
                            ->maxLength(10)
                            ->default('en'),
                    ])
                    ->columns(2),

                Section::make('Social Media Links')
                    ->description('Add your social media profile URLs')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label(__('filament.fields.facebook_url'))
                            ->url()
                            ->maxLength(255),
                        TextInput::make('twitter_url')
                            ->label(__('filament.fields.twitter_url'))
                            ->url()
                            ->maxLength(255),
                        TextInput::make('github_url')
                            ->label(__('filament.fields.github_url'))
                            ->url()
                            ->maxLength(255),
                        TextInput::make('youtube_url')
                            ->label(__('filament.fields.youtube_url'))
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Footer')
                    ->schema([
                        Textarea::make('footer_copyright')
                            ->label(__('filament.fields.copyright_text'))
                            ->required()
                            ->maxLength(500)
                            ->rows(2),
                    ]),
            ]);
    }
}