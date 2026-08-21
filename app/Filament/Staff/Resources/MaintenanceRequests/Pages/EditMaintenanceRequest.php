<?php

namespace App\Filament\Staff\Resources\MaintenanceRequests\Pages;

use App\Filament\Staff\Resources\MaintenanceRequests\MaintenanceRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceRequest extends EditRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
