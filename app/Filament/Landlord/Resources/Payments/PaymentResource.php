<?php

namespace App\Filament\Landlord\Resources\Payments;

use App\Filament\Landlord\Resources\Payments\Pages;
use App\Models\Payment;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'دفعة' : 'Payment';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'المدفوعات' : 'Payments';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // A form only hydrates the record's own attributes, so a dotted
            // 'tenant.name' field rendered empty; read the relation directly.
            TextInput::make('tenant_name')
                ->label(app()->getLocale() === 'ar' ? 'المستأجر' : 'Tenant')
                ->afterStateHydrated(fn (TextInput $component, ?Payment $record) => $component->state($record?->tenant?->name))
                ->dehydrated(false)
                ->disabled(),
            TextInput::make('amount')
                ->label(app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount')
                ->disabled(),
            TextInput::make('status')
                ->label(__('filament.fields.status'))
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(app()->getLocale() === 'ar' ? 'المستأجر' : 'Tenant'),
                TextColumn::make('amount')
                    ->label(app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount')
                    ->money(strtolower(config('currencies.default')))
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->label(app()->getLocale() === 'ar' ? 'التاريخ' : 'Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(app()->getLocale() === 'ar' ? 'الطريقة' : 'Method')
                    ->badge(),
                TextColumn::make('status')
                    ->formatStateUsing(\App\Support\StatusLabels::for('payment'))
                    ->color(\App\Support\StatusLabels::color('payment'))
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->emptyStateHeading(app()->getLocale() === 'ar'
                ? 'لا توجد مدفوعات مسجلة على عقاراتك بعد'
                : 'No payments recorded on your properties yet');
    }

    /**
     * Payments belong to tenant users; scope them to users holding a lease
     * on a property owned by the current landlord.
     */
    public static function getEloquentQuery(): Builder
    {
        $propertyIds = \App\Models\Property::query()
            ->where('user_id', auth()->id())
            ->select('id');

        $tenantIds = \App\Models\Lease::query()
            ->whereIn('property_id', $propertyIds)
            ->select('tenant_id');

        return parent::getEloquentQuery()
            ->whereIn('tenant_id', $tenantIds)
            ->with('tenant');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
