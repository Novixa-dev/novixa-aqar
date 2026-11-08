<?php

namespace App\Filament\Admin\Resources\Menus;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Admin\Resources\Menus\Pages\ListMenus;
use App\Filament\Admin\Resources\Menus\Pages\CreateMenu;
use App\Filament\Admin\Resources\Menus\Pages\EditMenu;
use App\Models\Menu;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'قائمة' : 'Menu';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'قوائم' : 'Menus';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bars-4';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label(__('filament.fields.name')),
                TextInput::make('url')
                    ->required()
                    ->label(__('filament.fields.url')),
                Select::make('parent_id')
                    ->label(__('filament.fields.parent_menu'))
                    ->options(Menu::pluck('name', 'id'))
                    ->nullable(),
                TextInput::make('order')
                    ->integer()
                    ->label(__('filament.fields.order'))
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('filament.fields.name')),
                TextColumn::make('url')
                    ->label(__('filament.fields.url')),
                TextColumn::make('parent.name')
                    ->label(__('filament.fields.parent_menu')),
                TextColumn::make('order')
                    ->sortable()
                    ->label(__('filament.fields.order')),
            ])
            ->filters([
                //
            ]);
    }

    public static function getLabel(): string
    {
        return 'Menu';
    }

    public static function getPluralLabel(): string
    {
        return 'Menus';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}