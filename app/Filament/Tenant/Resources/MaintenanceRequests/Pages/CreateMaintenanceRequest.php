<?php

namespace App\Filament\Tenant\Resources\MaintenanceRequests\Pages;

use App\Filament\Tenant\Resources\MaintenanceRequests\MaintenanceRequestResource;
use App\Models\Tenant;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // MaintenanceRequest.tenant_id -> tenants.id, not users.id (see the
        // same lookup in MaintenanceRequestResource::getEloquentQuery()).
        // The form has no tenant_id field, so without this every submission
        // fails a NOT NULL constraint on tenant_id.
        $data['tenant_id'] = Tenant::where('user_id', auth()->id())->value('id');

        return $data;
    }
}