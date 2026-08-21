<?php

namespace App\Filament\Admin\Resources\ZooplaSettings;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use App\Filament\Admin\Resources\ZooplaSettings\Pages\ListZooplaSettings;
use App\Filament\Admin\Resources\ZooplaSettings\Pages\CreateZooplaSettings;
use App\Filament\Admin\Resources\ZooplaSettings\Pages\EditZooplaSettings;
use App\Models\ZooplaSettings;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Resources\ZooplaSettingsResource\Pages;

class ZooplaSettingsResource extends Resource
{
    protected static ?string $model = ZooplaSettings::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعداد Zoopla' : 'Zoopla Setting';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات Zoopla' : 'Zoopla Settings';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات Zoopla' : 'Zoopla Settings';
    }

    protected static ?string $modelLabel = 'Zoopla Setting';

    protected static ?string $pluralModelLabel = 'Zoopla Settings';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        // Hidden: UK property-portal integration, not relevant to the Yemen demo.
        // The underlying model/table/scheduled sync job are kept intentionally.
        return false;
    }

    public static function canAccess(): bool
    {
        // Fail closed: nav-hiding alone still leaves the pages reachable by
        // direct URL. This integration is consciously disabled for the Yemen
        // demo; re-enable both methods together if it ever ships.
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('api_key')
                    ->required()
                    ->label(__('filament.fields.zoopla_api_key'))
                    ->password(),
                TextInput::make('base_uri')
                    ->required()
                    ->label(__('filament.fields.zoopla_base_uri'))
                    ->url(),
                TextInput::make('feed_id')
                    ->nullable()
                    ->label(__('filament.fields.feed_id')),
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
                TextColumn::make('feed_id')->label(__('filament.fields.feed_id')),
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
            'index' => ListZooplaSettings::route('/'),
            'create' => CreateZooplaSettings::route('/create'),
            'edit' => EditZooplaSettings::route('/{record}/edit'),
        ];
    }
}
