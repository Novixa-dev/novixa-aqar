<?php

namespace App\Filament\Admin\Resources\OnTheMarketSettings;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Admin\Resources\OnTheMarketSettings\Pages\ListOnTheMarketSettings;
use App\Filament\Admin\Resources\OnTheMarketSettings\Pages\CreateOnTheMarketSettings;
use App\Filament\Admin\Resources\OnTheMarketSettings\Pages\EditOnTheMarketSettings;
use App\Models\OnTheMarketSettings;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Resources\OnTheMarketSettingsResource\Pages;

class OnTheMarketSettingsResource extends Resource
{
    protected static ?string $model = OnTheMarketSettings::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعداد OnTheMarket' : 'OnTheMarket Setting';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات OnTheMarket' : 'OnTheMarket Settings';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';
    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات OnTheMarket' : 'On The Market Settings';
    }

    protected static ?string $modelLabel = 'On The Market Setting';

    protected static ?string $pluralModelLabel = 'On The Market Settings';

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
                    ->label(__('filament.fields.onthemarket_api_key'))
                    ->password(),
                TextInput::make('base_uri')
                    ->required()
                    ->label(__('filament.fields.onthemarket_base_uri'))
                    ->url(),
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
                TextColumn::make('sync_frequency')->label(__('filament.fields.sync_frequency')),
                ToggleColumn::make('is_active')->label(__('filament.fields.active')),
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
            'index' => ListOnTheMarketSettings::route('/'),
            'create' => CreateOnTheMarketSettings::route('/create'),
            'edit' => EditOnTheMarketSettings::route('/{record}/edit'),
        ];
    }
}
