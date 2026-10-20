<?php

namespace App\Filament\Staff\Resources\PropertyModerations\Pages;

use App\Filament\Staff\Resources\PropertyModerations\PropertyModerationResource;
use Filament\Resources\Pages\ListRecords;

class ListPropertyModerations extends ListRecords
{
    protected static string $resource = PropertyModerationResource::class;

    // No create button: this resource moderates existing property
    // submissions, it doesn't create new ones. Its form only ever
    // collected a handful of the properties table's required-NOT-NULL
    // columns (title/location/price/bedrooms/bathrooms/area_sqft/
    // year_built/property_type/status), so every "Create" submission
    // failed with a SQL error -- the right fix is removing the action
    // this screen was never meant to have, not completing the form.
    protected function getHeaderActions(): array
    {
        return [];
    }
}