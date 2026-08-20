<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\WorkOrder;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        // MaintenanceRequest.tenant_id -> tenants.id, not users.id.
        $tenantId = Tenant::where('user_id', auth()->id())->value('id');

        return [
            Stat::make('Total Maintenance Requests', MaintenanceRequest::where('tenant_id', $tenantId)->count()),
            Stat::make('Pending Requests', MaintenanceRequest::where('tenant_id', $tenantId)->where('status', 'pending')->count()),
            Stat::make('Completed Requests', MaintenanceRequest::where('tenant_id', $tenantId)->where('status', 'completed')->count()),
            Stat::make('Open Work Orders', WorkOrder::whereHas('maintenanceRequest', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })->whereIn('status', ['pending', 'in_progress'])->count())
                ->description('Current open work orders')
                ->descriptionIcon('heroicon-s-wrench')
                ->color('danger'),
            Stat::make('Completed Work Orders', WorkOrder::whereHas('maintenanceRequest', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })->where('status', 'completed')->count())
                ->description('Total completed work orders')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),
        ];
    }
}