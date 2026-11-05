<?php

namespace App\Filament\Landlord\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Lease;
use App\Models\Property;

class Dashboard extends BaseDashboard
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
        // 'occupied' has never been a real properties.status value in this
        // codebase -- the real value for a rented property is Property::
        // STATUS_RENTED ('rented'), so this always counted 0 regardless of
        // how many properties were actually rented.
        $this->occupiedProperties = Property::where('user_id', auth()->id())
            ->where('status', Property::STATUS_RENTED)
            ->count();
        // Lease.tenant_id points to users.id (see Lease::tenant()), not
        // tenants.id -- Tenant::leases() joins the wrong id space, so
        // counting through it produced meaningless numbers. Count distinct
        // tenant users directly off the landlord's own leases instead.
        $this->totalTenants = Lease::whereHas('property', function ($query) {
            $query->where('user_id', auth()->id());
        })->distinct('tenant_id')->count('tenant_id');
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