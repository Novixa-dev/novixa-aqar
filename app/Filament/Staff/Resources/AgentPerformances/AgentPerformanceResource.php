<?php

namespace App\Filament\Staff\Resources\AgentPerformances;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Staff\Resources\AgentPerformances\Pages\ListAgentPerformance;
use App\Filament\Staff\Resources\AgentPerformances\Pages\ViewAgentPerformance;
use App\Filament\Staff\Resources\AgentPerformanceResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgentPerformanceResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'أداء الوكيل' : 'Agent Performance';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'أداء الوكلاء' : 'Agent Performances';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $tenantOwnershipRelationshipName = 'currentTeam';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('agent');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('listings_count')
                    ->label(__('filament.fields.listings'))
                    ->counts('properties')
                    ->sortable(),
                TextColumn::make('properties_sold_count')
                    ->label(__('filament.fields.properties_sold'))
                    ->counts('properties', function (Builder $query) {
                        $query->where('status', 'sold');
                    })
                    ->sortable(),
                TextColumn::make('appointments_count')
                    ->label(__('filament.fields.appointments'))
                    ->counts('appointments')
                    ->sortable(),
                TextColumn::make('average_rating')
                    ->label(__('filament.fields.avg_rating'))
                    ->avg('reviews', 'rating')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
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
            'index' => ListAgentPerformance::route('/'),
            'view' => ViewAgentPerformance::route('/{record}'),
        ];
    }
}