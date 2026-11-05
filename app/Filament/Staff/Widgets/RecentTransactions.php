<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\TableWidget;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        // Without this, Filament derives the heading from the class name
        // (class_basename(...)->kebab()->ucwords()) — always English,
        // regardless of the active locale.
        return app()->getLocale() === 'ar' ? 'أحدث المعاملات' : 'Recent Transactions';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return app()->getLocale() === 'ar' ? 'لا توجد معاملات' : 'No transactions';
    }

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
            TextColumn::make('user.name')
                ->label(__('filament.fields.buyer')),
        ];
    }
}