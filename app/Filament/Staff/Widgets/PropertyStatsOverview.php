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
        $activeListings = Property::active()->count() ?: $totalProperties;
        $totalBookings = Booking::count();
        $totalRevenue = Transaction::sum('amount') ?: 0;

        $ar = app()->getLocale() === 'ar';

        return [
            Stat::make($ar ? 'إجمالي العقارات' : 'Total Properties', $totalProperties)
                ->description($ar ? 'جميع العقارات في النظام' : 'All listings in system')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),
            Stat::make($ar ? 'الإعلانات النشطة' : 'Active Listings', $activeListings)
                ->description($ar ? 'منشورة ومتاحة' : 'Published & available')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make($ar ? 'إجمالي الحجوزات' : 'Total Bookings', $totalBookings)
                ->description($ar ? 'زيارات وجولات العملاء' : 'Customer visits & tours')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
            Stat::make($ar ? 'إجمالي الإيرادات' : 'Total Revenue', app(\App\Services\CurrencyService::class)->format($totalRevenue))
                ->description($ar ? 'إجمالي المعاملات المالية' : 'Total financial transactions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
