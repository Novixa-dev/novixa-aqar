<?php

namespace App\Filament\Staff\Resources\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'غرفة' : 'Room';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الغرف' : 'Rooms';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('room_number')
                    ->label(__('filament.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('size')
                    ->label(__('filament.fields.area_sqft'))
                    ->required()
                    ->numeric()
                    ->suffix('m²'),
                TextInput::make('rent')
                    ->required()
                    ->numeric()
                    ->suffix(config('currencies.default')),
                Toggle::make('is_available')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room_number')
                    ->label(__('filament.fields.name')),
                TextColumn::make('size')
                    ->label(__('filament.fields.area_sqft'))
                    ->suffix(' m²'),
                TextColumn::make('rent')
                    ->formatStateUsing(fn ($state) => app(\App\Services\CurrencyService::class)->format($state)),
                IconColumn::make('is_available')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
