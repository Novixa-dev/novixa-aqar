<?php

namespace App\Filament\Tenant\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\WorkOrder;
use App\Filament\Tenant\Widgets\DashboardStatsOverview;
use App\Filament\Tenant\Widgets\RecentMaintenanceRequests;

class Dashboard extends BaseDashboard
{
    public $currentProperty;
    public $rentDueDate;
    public $openWorkOrders;
    public $completedWorkOrders;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.tenant.dashboard';

    public function mount(): void
    {
        // `properties` has no `tenant_id` column — a property only relates to a
        // tenant indirectly through an active Lease (Lease.tenant_id -> users.id).
        $activeLease = Lease::where('tenant_id', auth()->id())
            ->where('status', 'active')
            ->latest('start_date')
            ->first();

        $this->currentProperty = $activeLease?->property;
        $this->rentDueDate = null;

        // MaintenanceRequest.tenant_id -> tenants.id, not users.id, so it must be
        // resolved through the Tenant record, not auth()->id() directly.
        $tenantId = Tenant::where('user_id', auth()->id())->value('id');

        $this->openWorkOrders = $tenantId
            ? WorkOrder::whereHas('maintenanceRequest', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })->whereIn('status', ['pending', 'in_progress'])->count()
            : 0;
        $this->completedWorkOrders = $tenantId
            ? WorkOrder::whereHas('maintenanceRequest', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })->where('status', 'completed')->count()
            : 0;
    }

    public function getHeaderWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            RecentMaintenanceRequests::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
        ];
    }
}
