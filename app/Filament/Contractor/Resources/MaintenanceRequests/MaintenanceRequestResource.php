<?php

namespace App\Filament\Contractor\Resources\MaintenanceRequests;

use App\Filament\Contractor\Resources\MaintenanceRequests\Pages;
use App\Filament\Staff\Resources\MaintenanceRequests\MaintenanceRequestResource as StaffMaintenanceRequestResource;
use App\Models\Contractor;
use App\Models\MaintenanceRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench';

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مهمة صيانة' : 'Work Order';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مهام الصيانة' : 'Work Orders';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    /**
     * The contractor persona manages the Contractor profile record that
     * carries their email; assigned jobs are requests with contractor_id
     * pointing at it.
     */
    protected static function currentContractorId(): ?int
    {
        return Contractor::where('email', auth()->user()?->email)->value('id');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->options(fn () => StaffMaintenanceRequestResource::getStatusOptions())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(app()->getLocale() === 'ar' ? 'المهمة' : 'Title')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('property.title')
                    ->label(app()->getLocale() === 'ar' ? 'العقار' : 'Property')
                    ->limit(35),
                TextColumn::make('priority')
                    ->label(app()->getLocale() === 'ar' ? 'الأولوية' : 'Priority')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => StaffMaintenanceRequestResource::getPriorityOptions()[$state] ?? $state),
                TextColumn::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => StaffMaintenanceRequestResource::getStatusOptions()[$state] ?? $state),
                TextColumn::make('requested_date')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ الطلب' : 'Requested')
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->options(fn () => StaffMaintenanceRequestResource::getStatusOptions()),
            ])
            ->recordActions([
                Action::make('start')
                    ->label(app()->getLocale() === 'ar' ? 'بدء العمل' : 'Start work')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (MaintenanceRequest $record) => $record->status === 'pending')
                    ->action(fn (MaintenanceRequest $record) => $record->update(['status' => 'in_progress'])),
                Action::make('complete')
                    ->label(app()->getLocale() === 'ar' ? 'إنجاز المهمة' : 'Mark complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (MaintenanceRequest $record) => $record->status === 'in_progress')
                    ->action(fn (MaintenanceRequest $record) => $record->update(['status' => 'completed', 'completed_at' => now()])),
                EditAction::make(),
            ])
            ->emptyStateHeading(app()->getLocale() === 'ar'
                ? 'لا توجد مهام موكلة إليك حاليًا'
                : 'No work orders are assigned to you yet');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('property');

        return static::currentContractorId()
            ? $query->where('contractor_id', static::currentContractorId())
            : $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenanceRequests::route('/'),
            'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
