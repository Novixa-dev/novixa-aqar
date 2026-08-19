<?php

namespace App\Filament\Staff\Resources\EnergyConsumptions;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\EnergyConsumptions\Pages\ListEnergyConsumptions;
use App\Filament\Staff\Resources\EnergyConsumptions\Pages\CreateEnergyConsumption;
use App\Filament\Staff\Resources\EnergyConsumptions\Pages\EditEnergyConsumption;
use App\Filament\Staff\Resources\EnergyConsumptionResource\Pages;
use App\Models\EnergyConsumption;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnergyConsumptionResource extends Resource
{
    protected static ?string $model = EnergyConsumption::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'استهلاك الطاقة' : 'Energy Consumption';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'بيانات استهلاك الطاقة' : 'Energy Consumptions';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required(),
                DatePicker::make('consumption_date')
                    ->required(),
                TextInput::make('electricity_usage')
                    ->required()
                    ->numeric()
                    ->suffix('kWh'),
                TextInput::make('gas_usage')
                    ->required()
                    ->numeric()
                    ->suffix('m³'),
                TextInput::make('water_usage')
                    ->required()
                    ->numeric()
                    ->suffix('m³'),
                TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد الانتظار',
                        'paid' => 'مدفوع',
                        'overdue' => 'متأخر',
                    ] : [
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ])
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->searchable(),
                TextColumn::make('consumption_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('electricity_usage')
                    ->numeric()
                    ->suffix('kWh'),
                TextColumn::make('gas_usage')
                    ->numeric()
                    ->suffix('m³'),
                TextColumn::make('water_usage')
                    ->numeric()
                    ->suffix('m³'),
                TextColumn::make('total_cost')
                    ->money('usd')
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
            'index' => ListEnergyConsumptions::route('/'),
            'create' => CreateEnergyConsumption::route('/create'),
            'edit' => EditEnergyConsumption::route('/{record}/edit'),
        ];
    }
}