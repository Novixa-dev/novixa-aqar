<?php

namespace App\Filament\Staff\Resources\PropertyFeatures;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\PropertyFeatures\Pages\ListPropertyFeatures;
use App\Filament\Staff\Resources\PropertyFeatures\Pages\CreatePropertyFeature;
use App\Filament\Staff\Resources\PropertyFeatures\Pages\EditPropertyFeature;
use App\Filament\Staff\Resources\PropertyFeatureResource\Pages;
use App\Models\PropertyFeature;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PropertyFeatureResource extends Resource
{
    protected static ?string $model = PropertyFeature::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'ميزة عقار' : 'Property Feature';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'ميزات عقارات' : 'Property Features';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required(),
                TextInput::make('feature_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.name')
                    ->sortable(),
                TextColumn::make('feature_name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPropertyFeatures::route('/'),
            'create' => CreatePropertyFeature::route('/create'),
            'edit' => EditPropertyFeature::route('/{record}/edit'),
        ];
    }
}
