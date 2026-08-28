<?php

namespace App\Filament\Staff\Pages;

use Filament\Pages\Page;
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

    // Headline stats (Total Properties / Active Listings / Total Bookings /
    // Total Revenue) are owned entirely by the PropertyStatsOverview header
    // widget below — see the note in filament/staff/dashboard.blade.php for
    // why this page no longer also loads and renders them itself.

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
