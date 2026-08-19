<?php

namespace App\Filament\Staff\Resources\Favorites;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\Favorites\Pages\ListFavorites;
use App\Filament\Staff\Resources\Favorites\Pages\CreateFavorite;
use App\Filament\Staff\Resources\Favorites\Pages\EditFavorite;
use Filament\Forms;
use Filament\Tables;
use App\Models\Favorite;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use App\Filament\Staff\Resources\FavoriteResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\FavoriteResource\RelationManagers;

class FavoriteResource extends Resource
{
    protected static ?string $model = Favorite::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مفضلة' : 'Favorite';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مفضلات' : 'Favorites';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('property_id')
                    ->relationship('property', 'title'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label(__('filament.fields.properties'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->searchable()
                    ->sortable(),
                
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
            'index' => ListFavorites::route('/'),
            'create' => CreateFavorite::route('/create'),
            'edit' => EditFavorite::route('/{record}/edit'),
        ];
    }
}
