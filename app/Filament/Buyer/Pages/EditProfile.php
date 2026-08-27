<?php

namespace App\Filament\Buyer\Pages;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected static bool $shouldRegisterNavigation = false;

    // Pin to the original slug: the inherited default ('profile') collides
    // with the unrelated, separately auto-discovered PagesTenantProfile
    // page in this same panel, which silently wins the route and shadows
    // this page entirely.
    protected static ?string $slug = 'edit-profile';
}
