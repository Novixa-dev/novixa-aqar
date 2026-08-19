<?php

namespace App\Filament\Admin\Resources\Branches\Pages;

use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\Branches\BranchResource;
use Filament\Tables;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label(__('filament.fields.name'))->sortable(),
            TextColumn::make('address')->label(__('filament.fields.address'))->sortable(),
            TextColumn::make('phone_number')->label(__('filament.fields.phone_number'))->sortable(),
        ];
    }
}
