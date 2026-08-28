<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use App\Models\Transaction;
use Carbon\Carbon;

class TopPerformingProperties extends ChartWidget
{
    public function getHeading(): string
    {
        return app()->getLocale() === 'ar' ? 'أفضل العقارات أداءً' : 'Top Performing Properties';
    }

    protected function getData(): array
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $topProperties = Property::withSum(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }], 'amount')
            ->orderByDesc('transactions_sum_amount')
            ->take(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => app()->getLocale() === 'ar' ? 'الإيرادات' : 'Revenue',
                    'data' => $topProperties->pluck('transactions_sum_amount')->toArray(),
                ],
            ],
            'labels' => $topProperties->pluck('title')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}