<?php

namespace App\Filament\Staff\Resources\Transactions;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\Transactions\Pages\ViewTransaction;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\TransactionResource\Pages;
use App\Filament\Staff\Resources\TransactionResource\RelationManagers;
use App\Filament\Staff\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Staff\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Staff\Resources\Transactions\Pages\CreateTransaction;
use App\Services\TransactionService;
use Filament\Notifications\Notification;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'معاملة' : 'Transaction';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'معاملات' : 'Transactions';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->label(__('filament.fields.property'))
                    ->relationship('property', 'title')
                    ->required(),
                // Transaction's belongsTo(User::class, 'buyer_id') method is
                // actually named user(), not buyer() — 'buyer' here isn't a
                // real relationship on the model.
                Select::make('buyer_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('seller_id')
                    ->relationship('seller', 'name')
                    ->required(),
                DatePicker::make('date')
                    ->label(__('filament.fields.transaction_date'))
                    ->required(),
                TextInput::make('amount')
                    ->type('number')
                    ->label(__('filament.fields.transaction_amount'))
                    ->required(),
                Select::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        Transaction::STATUS_PENDING => 'قيد الانتظار',
                        Transaction::STATUS_IN_PROGRESS => 'قيد التنفيذ',
                        Transaction::STATUS_COMPLETED => 'مكتملة',
                        Transaction::STATUS_CANCELLED => 'ملغاة',
                    ] : [
                        Transaction::STATUS_PENDING => 'Pending',
                        Transaction::STATUS_IN_PROGRESS => 'In Progress',
                        Transaction::STATUS_COMPLETED => 'Completed',
                        Transaction::STATUS_CANCELLED => 'Cancelled',
                    ])
                    ->required(),
                TextInput::make('commission_amount')
                    ->type('number')
                    ->label(__('filament.fields.commission_amount'))
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('filament.fields.buyer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('seller.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('filament.fields.transaction_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(strtolower(config('currencies.default')))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->formatStateUsing(\App\Support\StatusLabels::for('transaction'))
                    ->color(\App\Support\StatusLabels::color('transaction'))
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->money(strtolower(config('currencies.default')))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('generateDocument')
                    ->label(__('filament.fields.generate_document'))
                    // Only offered once a sale-agreement template exists.
                    ->visible(fn (): bool => TransactionService::saleAgreementTemplate() !== null)
                    ->action(function (Transaction $record, TransactionService $transactionService) {
                        $document = $transactionService->generateContractualDocument($record);

                        Notification::make()
                            ->title($document
                                ? (app()->getLocale() === 'ar' ? 'تم إنشاء عقد البيع.' : 'Sale agreement generated.')
                                : (app()->getLocale() === 'ar' ? 'لا يوجد قالب لعقد البيع.' : 'No sale agreement template exists.'))
                            ->{$document ? 'success' : 'warning'}()
                            ->send();
                    }),
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
            'view' => ViewTransaction::route('/{record}'),
        ];
    }
}
