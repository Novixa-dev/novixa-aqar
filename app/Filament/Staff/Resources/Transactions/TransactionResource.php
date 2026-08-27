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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(strtolower(config('currencies.default')))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable()
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
                    ->action(function (Transaction $record, TransactionService $transactionService) {
                        $document = $transactionService->generateContractualDocument($record);
                        // You might want to add a notification or redirect here
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
