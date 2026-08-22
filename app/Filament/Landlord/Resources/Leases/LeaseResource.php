<?php

namespace App\Filament\Landlord\Resources\Leases;

use App\Filament\Landlord\Resources\Leases\Pages;
use App\Models\Lease;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقد إيجار' : 'Lease';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقود الإيجار' : 'Leases';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('property.title')
                ->label(app()->getLocale() === 'ar' ? 'العقار' : 'Property')
                ->disabled(),
            TextInput::make('tenant.name')
                ->label(app()->getLocale() === 'ar' ? 'المستأجر' : 'Tenant')
                ->disabled(),
            DatePicker::make('start_date')->disabled(),
            DatePicker::make('end_date')->disabled(),
            TextInput::make('rent_amount')
                ->disabled()
                ->suffix(config('currencies.default')),
            Select::make('status')
                ->disabled()
                ->options(fn () => app()->getLocale() === 'ar' ? [
                    'active' => 'ساري',
                    'expired' => 'منتهي',
                    'terminated' => 'مفسوخ',
                ] : [
                    'active' => 'Active',
                    'expired' => 'Expired',
                    'terminated' => 'Terminated',
                ]),
            Textarea::make('terms')
                ->label(app()->getLocale() === 'ar' ? 'الشروط' : 'Terms')
                ->disabled()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label(app()->getLocale() === 'ar' ? 'العقار' : 'Property')
                    ->limit(40),
                TextColumn::make('tenant.name')
                    ->label(app()->getLocale() === 'ar' ? 'المستأجر' : 'Tenant'),
                TextColumn::make('start_date')
                    ->label(app()->getLocale() === 'ar' ? 'البداية' : 'Start')
                    ->date(),
                TextColumn::make('end_date')
                    ->label(app()->getLocale() === 'ar' ? 'النهاية' : 'End')
                    ->date(),
                TextColumn::make('rent_amount')
                    ->label(app()->getLocale() === 'ar' ? 'الإيجار' : 'Rent')
                    ->money(strtolower(config('currencies.default'))),
                TextColumn::make('status')
                    ->formatStateUsing(\App\Support\StatusLabels::for('lease'))
                    ->color(\App\Support\StatusLabels::color('lease'))
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->emptyStateHeading(app()->getLocale() === 'ar'
                ? 'لا توجد عقود إيجار على عقاراتك بعد'
                : 'No leases on your properties yet');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('property', fn (Builder $q) => $q->where('user_id', auth()->id()))
            ->with(['property', 'tenant']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeases::route('/'),
            'view' => Pages\ViewLease::route('/{record}'),
        ];
    }
}
