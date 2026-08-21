<?php

namespace App\Filament\Landlord\Resources\Properties\Pages;

use App\Filament\Landlord\Resources\Properties\PropertyResource;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
