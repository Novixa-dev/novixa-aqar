<?php

namespace App\Filament\Contractor\Resources\MaintenanceRequests\Pages;

use App\Filament\Contractor\Resources\MaintenanceRequests\MaintenanceRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceRequest extends EditRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
