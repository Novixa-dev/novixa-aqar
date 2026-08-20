<?php

namespace App\Filament\Staff\Resources\Tenants\Pages;

use Filament\Facades\Filament;
use Filament\Panel;
use App\Filament\Staff\Resources\Tenants\TenantResource;
use Filament\Resources\Pages\Page;

class EditProfile extends Page
{
    protected static string $resource = TenantResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.tenant.pages.edit-profile';

    protected static ?string $slug = 'edit-profile';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تعديل الملف الشخصي' : 'Edit Profile';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'تعديل الملف الشخصي' : 'Edit Profile';
    }

    public static function getRoutes(): array
    {
        return [
            static::getSlug() => static::getRoutePath(Filament::getCurrentOrDefaultPanel(), Filament::getCurrentOrDefaultPanel(), Filament::getCurrentOrDefaultPanel(), Filament::getCurrentOrDefaultPanel()),
        ];
    }

    public static function getRoutePath(Panel $panel): string
    {
        return '/tenant/edit-profile';
    }
}
