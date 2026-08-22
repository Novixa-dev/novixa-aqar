<?php

namespace App\Filament\Staff\Resources\MaintenanceRequests;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Filament\Staff\Resources\MaintenanceRequests\Pages\ListMaintenanceRequests;
use App\Filament\Staff\Resources\MaintenanceRequests\Pages\CreateMaintenanceRequest;
use App\Filament\Staff\Resources\MaintenanceRequests\Pages\EditMaintenanceRequest;
use App\Models\MaintenanceRequest;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلب صيانة' : 'Maintenance Request';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلبات الصيانة' : 'Maintenance Requests';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench';

    public static function getStatusOptions(): array
    {
        return app()->getLocale() === 'ar' ? [
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
        ] : [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];
    }

    public static function getPriorityOptions(): array
    {
        return app()->getLocale() === 'ar' ? [
            'low' => 'منخفضة',
            'normal' => 'عادية',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ] : [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(fn () => static::getStatusOptions())
                    ->required()
                    ->default('pending'),
                Select::make('priority')
                    ->options(fn () => static::getPriorityOptions())
                    ->required()
                    ->default('normal'),
                DatePicker::make('requested_date')
                    ->required()
                    ->default(now()),
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('vendor_id')
                    ->label(app()->getLocale() === 'ar' ? 'المقاول' : 'Contractor')
                    ->relationship('contractor', 'company_name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('property.title')
                    ->label(__('filament.fields.property'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tenant.full_name')
                    ->label(app()->getLocale() === 'ar' ? 'المستأجر' : 'Tenant')
                    ->searchable(),
                TextColumn::make('status')
                    ->formatStateUsing(\App\Support\StatusLabels::for('maintenance'))
                    ->badge()
                    ->colors(fn () => [
                        'warning' => 'pending',
                        'info' => 'in_progress',
                        'success' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state) => static::getStatusOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('priority')
                    ->formatStateUsing(\App\Support\StatusLabels::for('priority'))
                    ->badge()
                    ->colors(fn () => [
                        'gray' => 'low',
                        'info' => 'normal',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ])
                    ->formatStateUsing(fn ($state) => static::getPriorityOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('requested_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => static::getStatusOptions()),
                SelectFilter::make('priority')
                    ->options(fn () => static::getPriorityOptions()),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('start')
                    ->label(app()->getLocale() === 'ar' ? 'بدء التنفيذ' : 'Start')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (MaintenanceRequest $record) => $record->status === 'pending')
                    ->action(fn (MaintenanceRequest $record) => $record->update(['status' => 'in_progress'])),
                Action::make('complete')
                    ->label(app()->getLocale() === 'ar' ? 'إكمال' : 'Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (MaintenanceRequest $record) => $record->status === 'in_progress')
                    ->action(fn (MaintenanceRequest $record) => $record->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ])),
            ])
            ->defaultSort('requested_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceRequests::route('/'),
            'create' => CreateMaintenanceRequest::route('/create'),
            'edit' => EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['property', 'tenant']);
    }
}
