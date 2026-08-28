<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Page;
use App\Models\Property;
use App\Models\Tenant;

class Dashboard extends Page
{
    protected string $view = 'filament.landlord.dashboard';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public $totalProperties;
    public $occupiedProperties;
    public $totalTenants;

    public function mount(): void
    {
        $this->totalProperties = Property::where('user_id', auth()->id())->count();
        $this->occupiedProperties = Property::where('user_id', auth()->id())->where('status', 'occupied')->count();
        $this->totalTenants = Tenant::whereHas('leases.property', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add any landlord-specific widgets here
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add any landlord-specific widgets here
        ];
    }
}