<?php

namespace App\Filament\Landlord\Resources\RentalApplications;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Filament\Landlord\Resources\RentalApplications\Pages\ListRentalApplications;
use App\Filament\Landlord\Resources\RentalApplications\Pages\EditRentalApplication;
use App\Models\RentalApplication;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RentalApplicationResource extends Resource
{
    protected static ?string $model = RentalApplication::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلب إيجار' : 'Rental Application';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلبات الإيجار' : 'Rental Applications';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    public static function getStatusOptions(): array
    {
        return app()->getLocale() === 'ar' ? [
            'pending' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ] : [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options(fn () => static::getStatusOptions())
                    ->required(),
                Textarea::make('decision_notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label(__('filament.fields.property'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tenant.name')
                    ->label(app()->getLocale() === 'ar' ? 'المتقدم' : 'Applicant')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors(fn () => [
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => static::getStatusOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('application_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('desired_move_in_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => static::getStatusOptions()),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('approve')
                    ->label(app()->getLocale() === 'ar' ? 'قبول' : 'Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (RentalApplication $record) => $record->status === 'pending')
                    ->action(function (RentalApplication $record) {
                        $record->update([
                            'status' => 'approved',
                            'decided_at' => now(),
                            'decided_by' => auth()->id(),
                        ]);
                    }),
                Action::make('reject')
                    ->label(app()->getLocale() === 'ar' ? 'رفض' : 'Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (RentalApplication $record) => $record->status === 'pending')
                    ->action(function (RentalApplication $record) {
                        $record->update([
                            'status' => 'rejected',
                            'decided_at' => now(),
                            'decided_by' => auth()->id(),
                        ]);
                    }),
            ])
            ->defaultSort('application_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRentalApplications::route('/'),
            'edit' => EditRentalApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('property', fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->with(['property', 'tenant']);
    }
}
