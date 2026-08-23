<?php

namespace App\Filament\Agent\Widgets;

use App\Models\Property;
use App\Models\Booking;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $agentId = auth()->id();

        $myListings = Property::where('agent_id', $agentId)->count();
        $activeListings = Property::where('agent_id', $agentId)->active()->count();
        $openLeads = Lead::whereIn('status', ['new', 'contacted', 'qualified'])->count();
        $monthBookings = Booking::whereHas('property', fn ($q) => $q->where('agent_id', $agentId))
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $ar = app()->getLocale() === 'ar';

        return [
            Stat::make($ar ? 'عقاراتي' : 'My Listings', $myListings)
                ->description($ar ? 'العقارات الموكلة إليك' : 'Properties assigned to you')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),
            Stat::make($ar ? 'الإعلانات النشطة' : 'Active Listings', $activeListings)
                ->description($ar ? 'منشورة ومتاحة' : 'Published & available')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make($ar ? 'العملاء المحتملون المفتوحون' : 'Open Leads', $openLeads)
                ->description($ar ? 'بانتظار المتابعة' : 'Awaiting follow-up')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
            Stat::make($ar ? 'زيارات هذا الشهر' : 'Bookings This Month', $monthBookings)
                ->description($ar ? 'جولات عقارية مجدولة' : 'Scheduled property tours')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
