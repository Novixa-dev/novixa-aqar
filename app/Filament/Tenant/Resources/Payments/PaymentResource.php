<?php

namespace App\Filament\Tenant\Resources\Payments;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use App\Filament\Tenant\Resources\Payments\Pages\ListPayments;
use App\Filament\Tenant\Resources\Payments\Pages\CreatePayment;
use App\Filament\Tenant\Resources\Payments\Pages\ViewPayment;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\PaymentResource\Pages;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'دفعة' : 'Payment';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'دفعات' : 'Payments';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'المدفوعات' : 'Payments';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->suffix(config('currencies.default')),
                DatePicker::make('payment_date')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد الانتظار',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                    ] : [
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                Select::make('payment_method')
                    ->required()
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'credit_card' => 'بطاقة ائتمان',
                        'bank_transfer' => 'تحويل بنكي',
                        'paypal' => 'PayPal',
                    ] : [
                        'credit_card' => 'Credit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'paypal' => 'PayPal',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('payment_method'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'pending' => 'قيد الانتظار',
                        'completed' => 'مكتمل',
                        'failed' => 'فشل',
                    ] : [
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tenant_id', auth()->id());
    }
}
