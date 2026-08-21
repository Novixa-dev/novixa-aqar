<?php

namespace App\Filament\Contractor\Resources\MaintenanceRequests\Pages;

use App\Filament\Contractor\Resources\MaintenanceRequests\MaintenanceRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceRequests extends ListRecords
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
