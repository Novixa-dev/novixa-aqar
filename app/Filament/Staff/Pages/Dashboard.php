<?php

namespace App\Filament\Staff\Pages;

use Filament\Pages\Page;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Transaction;
use App\Filament\Staff\Widgets\PropertyStatsOverview;
use App\Filament\Staff\Widgets\RecentTransactions;
use App\Filament\Staff\Widgets\TopPerformingProperties;

class Dashboard extends Page
{
    protected string $view = 'filament.staff.dashboard';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -2;

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public $startDate;
    public $endDate;
    public $totalProperties;
    public $activeListings;
    public $totalBookings;
    public $totalRevenue;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth();
        $this->endDate = now()->endOfMonth();

        $this->loadDashboardData();
    }

    public function loadDashboardData(): void
    {
        $this->totalProperties = Property::count();
        $this->activeListings = Property::active()->count();
        $this->totalBookings = Booking::whereBetween('date', [$this->startDate, $this->endDate])->count();
        $this->totalRevenue = Transaction::whereBetween('date', [$this->startDate, $this->endDate])->sum('amount');
    }

    public function refreshDashboard(): void
    {
        $this->loadDashboardData();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PropertyStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RecentTransactions::class,
            TopPerformingProperties::class,
        ];
    }
}
