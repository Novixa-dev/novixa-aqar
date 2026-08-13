<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Staff\Widgets\PropertyStatsOverview;
use App\Filament\App\Widgets\LatestProperties;
use App\Filament\App\Widgets\RecentBookings;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            PropertyStatsOverview::class,
            LatestProperties::class,
            RecentBookings::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
