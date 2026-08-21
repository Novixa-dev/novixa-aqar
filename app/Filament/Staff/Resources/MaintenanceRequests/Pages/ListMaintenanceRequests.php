<?php

namespace App\Filament\Staff\Resources\MaintenanceRequests\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Staff\Resources\MaintenanceRequests\MaintenanceRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceRequests extends ListRecords
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
