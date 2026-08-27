<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class PlaidIntegration extends Page
{
    protected string $view = 'filament.pages.plaid-integration';

    protected static bool $shouldRegisterNavigation = false;

    public ?string $plaidClientId = null;

    public ?string $plaidSecret = null;

    public ?string $plaidEnvironment = null;

    public function mount(): void
    {
        $this->plaidClientId = config('services.plaid.client_id');
        $this->plaidSecret = config('services.plaid.secret');
        $this->plaidEnvironment = config('services.plaid.env');
    }

    public function render(): View
    {
        return view($this->view, [
            'plaidClientId' => $this->plaidClientId,
            'plaidSecret' => $this->plaidSecret,
            'plaidEnvironment' => $this->plaidEnvironment,
        ]);
    }
}
