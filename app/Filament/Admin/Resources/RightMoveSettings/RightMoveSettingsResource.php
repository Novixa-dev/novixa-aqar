<?php

namespace App\Filament\Admin\Resources\RightMoveSettings;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\RightMoveSettings\Pages\ListRightMoveSettings;
use App\Filament\Admin\Resources\RightMoveSettings\Pages\CreateRightMoveSettings;
use App\Filament\Admin\Resources\RightMoveSettings\Pages\EditRightMoveSettings;
use App\Filament\Admin\Resources\RightMoveSettingsResource\Pages;
use App\Models\RightMoveSettings;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RightMoveSettingsResource extends Resource
{
    protected static ?string $model = RightMoveSettings::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعداد RightMove' : 'RightMove Setting';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات RightMove' : 'RightMove Settings';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات RightMove' : 'RightMove Settings';
    }

    protected static ?string $modelLabel = 'RightMove Setting';

    protected static ?string $pluralModelLabel = 'RightMove Settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        // Hidden: UK property-portal integration, not relevant to the Yemen demo.
        // The underlying model/table/scheduled sync job are kept intentionally.
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('api_key')
                    ->required()
                    ->label(__('filament.fields.rightmove_api_key'))
                    ->password(),
                TextInput::make('base_uri')
                    ->nullable()
                    ->label(__('filament.fields.rightmove_base_uri'))
                    ->url(),
                Select::make('channel')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'sales' => 'مبيعات',
                        'lettings' => 'إيجارات',
                    ] : [
                        'sales' => 'Sales',
                        'lettings' => 'Lettings',
                    ])
                    ->required()
                    ->label(__('filament.fields.channel')),
                Select::make('feed_type')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'full' => 'كامل',
                        'incremental' => 'تزايدي',
                    ] : [
                        'full' => 'Full',
                        'incremental' => 'Incremental',
                    ])
                    ->required()
                    ->label(__('filament.fields.feed_type')),
                Select::make('sync_frequency')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'hourly' => 'كل ساعة',
                        'daily' => 'يومياً',
                        'weekly' => 'أسبوعياً',
                    ] : [
                        'hourly' => 'Hourly',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                    ])
                    ->required()
                    ->label(__('filament.fields.sync_frequency')),
                TextInput::make('feed_url')
                    ->nullable()
                    ->label(__('filament.fields.feed_url'))
                    ->url(),
                Toggle::make('is_active')
                    ->label(__('filament.fields.active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('base_uri')->label(__('filament.fields.base_uri')),
                TextColumn::make('channel')->label(__('filament.fields.channel')),
                TextColumn::make('feed_type')->label(__('filament.fields.feed_type')),
                TextColumn::make('sync_frequency')->label(__('filament.fields.sync_frequency')),
                ToggleColumn::make('is_active')->label(__('filament.fields.active')),
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListRightMoveSettings::route('/'),
            'create' => CreateRightMoveSettings::route('/create'),
            'edit' => EditRightMoveSettings::route('/{record}/edit'),
        ];
    }
}