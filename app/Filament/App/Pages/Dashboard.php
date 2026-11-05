<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Transaction;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public $totalProperties;
    public $activeListings;
    public $totalBookings;
    public $totalRevenue;

    public function mount(): void
    {
        $this->totalProperties = Property::count();
        $this->activeListings = Property::active()->count();
        $this->totalBookings = Booking::count();
        $this->totalRevenue = Transaction::sum('amount');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add any app-specific widgets here
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add any app-specific widgets here
        ];
    }
}
