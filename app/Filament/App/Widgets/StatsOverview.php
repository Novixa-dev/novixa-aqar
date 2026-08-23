<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Transaction;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $ar = app()->getLocale() === 'ar';

        return [
            Stat::make($ar ? 'إجمالي العقارات' : 'Total Properties', Property::count()),
            Stat::make($ar ? 'الإعلانات النشطة' : 'Active Listings', Property::active()->count()),
            Stat::make($ar ? 'إجمالي الحجوزات' : 'Total Bookings', Booking::count()),
            Stat::make($ar ? 'إجمالي الإيرادات' : 'Total Revenue', app(\App\Services\CurrencyService::class)->format(Transaction::sum('amount'))),
        ];
    }
}