<?php

namespace App\Filament\Staff\Resources\MaintenanceRequests\Pages;

use App\Filament\Staff\Resources\MaintenanceRequests\MaintenanceRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MaintenanceRequestResource::class;
}
