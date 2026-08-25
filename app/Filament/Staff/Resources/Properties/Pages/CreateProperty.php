<?php

namespace App\Filament\Staff\Resources\Properties\Pages;

use App\Filament\Staff\Resources\Properties\PropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    /**
     * The form has no team_id/user_id field (nothing in this codebase's
     * Property forms exposes one), so without this every property created
     * here was saved with both columns NULL. PropertyPolicy::update()/
     * delete() require either direct user_id ownership or a shared
     * team_id — with both null, the creating staff member (or anyone
     * else) could never edit or delete the record again immediately
     * after creating it. Verified live: reproduced, then fixed.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] ??= auth()->user()?->current_team_id;

        return $data;
    }
}
