<?php

namespace App\Filament\Staff\Resources\RentalApplications;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\RentalApplications\Pages\ListRentalApplications;
use App\Filament\Staff\Resources\RentalApplications\Pages\CreateRentalApplication;
use App\Filament\Staff\Resources\RentalApplications\Pages\EditRentalApplication;
use App\Filament\Staff\Resources\RentalApplicationResource\Pages;
use App\Models\RentalApplication;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalApplicationResource extends Resource
{
    protected static ?string $model = RentalApplication::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلب استئجار' : 'Rental Application';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'طلبات استئجار' : 'Rental Applications';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->required()
                    ->searchable(),
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable(),
                Select::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد المراجعة',
                        'approved' => 'موافق عليه',
                        'rejected' => 'مرفوض',
                    ] : [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Select::make('employment_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'employed' => 'موظف',
                        'self_employed' => 'يعمل لحسابه الخاص',
                        'unemployed' => 'عاطل عن العمل',
                        'student' => 'طالب',
                    ] : [
                        'employed' => 'Employed',
                        'self_employed' => 'Self-employed',
                        'unemployed' => 'Unemployed',
                        'student' => 'Student',
                    ])
                    ->required(),
                TextInput::make('annual_income')
                    ->required()
                    ->numeric()
                    ->suffix(config('currencies.default')),
                DatePicker::make('lease_start_date')
                    ->required(),
                DatePicker::make('lease_end_date')
                    ->required(),
                Select::make('background_check_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد المراجعة',
                        'passed' => 'ناجح',
                        'failed' => 'فاشل',
                    ] : [
                        'pending' => 'Pending',
                        'passed' => 'Passed',
                        'failed' => 'Failed',
                    ]),
                Select::make('credit_report_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'excellent' => 'ممتاز',
                        'good' => 'جيد',
                        'fair' => 'متوسط',
                        'poor' => 'ضعيف',
                        'pending' => 'قيد المراجعة',
                    ] : [
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'pending' => 'Pending',
                    ]),
                Select::make('rental_history_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'good' => 'جيد',
                        'fair' => 'متوسط',
                        'poor' => 'ضعيف',
                        'pending' => 'قيد المراجعة',
                    ] : [
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'pending' => 'Pending',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tenant.name')
                    ->searchable()
                    ->sortable(),
                SelectColumn::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد المراجعة',
                        'approved' => 'موافق عليه',
                        'rejected' => 'مرفوض',
                    ] : [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->sortable(),
                TextColumn::make('employment_status')
                    ->searchable(),
                TextColumn::make('annual_income')
                    ->money(strtolower(config('currencies.default')))
                    ->sortable(),
                TextColumn::make('background_check_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'passed' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('credit_report_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent', 'good' => 'success',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('rental_history_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'good' => 'success',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد المراجعة',
                        'approved' => 'موافق عليه',
                        'rejected' => 'مرفوض',
                    ] : [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('background_check_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'passed' => 'ناجح',
                        'failed' => 'فاشل',
                        'pending' => 'قيد المراجعة',
                    ] : [
                        'passed' => 'Passed',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),
                SelectFilter::make('credit_report_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'good' => 'جيد',
                        'poor' => 'ضعيف',
                        'pending' => 'قيد المراجعة',
                    ] : [
                        'good' => 'Good',
                        'poor' => 'Poor',
                        'pending' => 'Pending',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('approve')
                    ->action(fn (RentalApplication $record) => $record->updateStatus('approved'))
                    ->requiresConfirmation()
                    ->visible(fn (RentalApplication $record): bool => $record->status === 'pending' && $record->isScreeningComplete() && $record->isScreeningPassed()),
                Action::make('reject')
                    ->action(fn (RentalApplication $record) => $record->updateStatus('rejected'))
                    ->requiresConfirmation()
                    ->visible(fn (RentalApplication $record): bool => $record->status === 'pending'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد المراجعة',
                        'approved' => 'موافق عليه',
                        'rejected' => 'مرفوض',
                    ] : [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('employment_status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'employed' => 'موظف',
                        'self_employed' => 'يعمل لحسابه الخاص',
                        'unemployed' => 'عاطل عن العمل',
                        'student' => 'طالب',
                    ] : [
                        'employed' => 'Employed',
                        'self_employed' => 'Self-employed',
                        'unemployed' => 'Unemployed',
                        'student' => 'Student',
                    ]),
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
            'index' => ListRentalApplications::route('/'),
            'create' => CreateRentalApplication::route('/create'),
            'edit' => EditRentalApplication::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Property Management';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['admin', 'staff']);
    }
}
