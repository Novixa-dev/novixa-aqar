<?php

namespace App\Filament\Staff\Resources\KeyLocations;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Staff\Resources\KeyLocations\Pages\ListKeyLocations;
use App\Filament\Staff\Resources\KeyLocations\Pages\CreateKeyLocation;
use App\Filament\Staff\Resources\KeyLocations\Pages\EditKeyLocation;
use App\Models\KeyLocation;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Staff\Resources\KeyLocationResource\Pages;

class KeyLocationResource extends Resource
{
    protected static ?string $model = KeyLocation::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'موقع رئيسي' : 'Key Location';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مواقع رئيسية' : 'Key Locations';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('location_name')
                    ->required()
                    ->label(__('filament.fields.location_name')),
                TextInput::make('address')
                    ->required()
                    ->label(__('filament.fields.address')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location_name')->label(__('filament.fields.location_name')),
                TextColumn::make('address')->label(__('filament.fields.address')),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeyLocations::route('/'),
            'create' => CreateKeyLocation::route('/create'),
            'edit' => EditKeyLocation::route('/{record}/edit'),
        ];
    }
}
