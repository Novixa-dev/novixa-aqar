<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\TableWidget;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Transaction::query()->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('property.title')
                ->label(__('filament.fields.property')),
            TextColumn::make('date')
                ->date(),
            TextColumn::make('amount')
                ->money(strtolower(config('currencies.default'))),
            TextColumn::make('buyer.name')
                ->label(__('filament.fields.buyer')),
        ];
    }
}