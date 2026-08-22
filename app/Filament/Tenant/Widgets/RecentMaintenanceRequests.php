<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Filament\Tenant\Resources\MaintenanceRequests\MaintenanceRequestResource;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecentMaintenanceRequests extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder | Relation | null
    {
        // MaintenanceRequest.tenant_id -> tenants.id, not users.id.
        $tenantId = Tenant::where('user_id', auth()->id())->value('id');

        return MaintenanceRequest::where('tenant_id', $tenantId)->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title'),
            TextColumn::make('status')
                ->formatStateUsing(\App\Support\StatusLabels::for('maintenance'))
                ->color(\App\Support\StatusLabels::color('maintenance')),
            TextColumn::make('requested_date')->date(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')
                ->url(fn (MaintenanceRequest $record): string => MaintenanceRequestResource::getUrl('edit', ['record' => $record])),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Recent Maintenance Requests';
    }
}