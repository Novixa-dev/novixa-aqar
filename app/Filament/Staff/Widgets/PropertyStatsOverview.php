<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $totalProperties = Property::count();
        $activeListings = Property::whereIn('status', ['active', 'For Sale', 'For Rent', 'available'])->count() ?: $totalProperties;
        $totalBookings = Booking::count();
        $totalRevenue = Transaction::sum('amount') ?: 0;

        return [
            Stat::make('Total Properties', $totalProperties)
                ->description('All listings in system')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),
            Stat::make('Active Listings', $activeListings)
                ->description('Published & available')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Total Bookings', $totalBookings)
                ->description('Customer visits & tours')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Total financial transactions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
