<?php

namespace App\Filament\Seller\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Property;
use App\Models\Booking;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.seller.dashboard';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    protected $myProperties;
    protected $activeListings;
    protected $totalBookings;

    public function mount(): void
    {
        $this->myProperties = Property::where('user_id', auth()->id())->count();
        $this->activeListings = Property::where('user_id', auth()->id())->active()->count();
        $this->totalBookings = Booking::whereHas('property', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add any seller-specific widgets here
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add any seller-specific widgets here
        ];
    }
}