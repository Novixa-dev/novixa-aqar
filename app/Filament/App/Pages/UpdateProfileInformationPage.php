<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

/**
 * Auto-discovered duplicate of App\Filament\App\Pages\EditProfile (which
 * now correctly extends Filament\Auth\Pages\EditProfile and is registered
 * via ->profile() in every panel). This page's own mount() called
 * $this->form->fill() without ever implementing HasForms/InteractsWithForms,
 * so it fatally errored on every visit. Rather than maintain two parallel
 * "edit your own profile" implementations, this just forwards to the real
 * one.
 */
class UpdateProfileInformationPage extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        redirect(EditProfile::getUrl());
    }
}
