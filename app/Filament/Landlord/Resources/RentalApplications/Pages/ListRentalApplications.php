<?php

namespace App\Filament\Landlord\Resources\RentalApplications\Pages;

use App\Filament\Landlord\Resources\RentalApplications\RentalApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListRentalApplications extends ListRecords
{
    protected static string $resource = RentalApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
