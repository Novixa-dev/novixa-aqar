<?php

namespace App\Filament\Staff\Widgets;

use App\Models\User;
use App\Models\Property;
use App\Models\Appointment;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentPerformanceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $topAgent = User::role('agent')
            ->withCount(['properties' => function ($query) {
                $query->where('status', 'sold');
            }])
            ->orderByDesc('properties_count')
            ->first();

        $ar = app()->getLocale() === 'ar';

        return [
            Stat::make($ar ? 'إجمالي الوكلاء' : 'Total Agents', User::role('agent')->count()),
            Stat::make($ar ? 'إجمالي العقارات' : 'Total Properties', Property::count()),
            Stat::make($ar ? 'العقارات المباعة' : 'Properties Sold', Property::where('status', 'sold')->count()),
            Stat::make($ar ? 'مواعيد هذا الشهر' : 'Appointments This Month', Appointment::whereMonth('appointment_date', now()->month)->count()),
            Stat::make($ar ? 'متوسط تقييم الوكلاء' : 'Average Agent Rating', number_format(Review::avg('rating'), 2)),
            Stat::make($ar ? 'أفضل وكيل أداءً' : 'Top Performing Agent', $topAgent ? $topAgent->name : ($ar ? 'غير متوفر' : 'N/A'))
                ->description($topAgent ? $topAgent->properties_count . ($ar ? ' عقار مباع' : ' properties sold') : ''),
        ];
    }
}