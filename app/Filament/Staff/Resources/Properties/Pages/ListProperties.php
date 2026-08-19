<?php

namespace App\Filament\Staff\Resources\Properties\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Filament\Staff\Resources\Properties\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProperties extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importCsv')
                ->label(__('filament.fields.import_csv'))
                ->action(fn () => $this->importCsv()),
            Action::make('syncPortals')
                ->label(__('filament.fields.sync_portals'))
                ->action(fn () => $this->syncPortals()),
        ];
    }

    protected function importCsv()
    {
        // Implement CSV import logic here
    }

    protected function syncPortals()
    {
        // Implement property portal syncing logic here
    }
}
