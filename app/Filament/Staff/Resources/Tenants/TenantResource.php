<?php

namespace App\Filament\Staff\Resources\Tenants;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Staff\Resources\Tenants\Pages\ListTenants;
use App\Filament\Staff\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Staff\Resources\Tenants\Pages\ViewTenant;
use App\Filament\Staff\Resources\Tenants\Pages\EditTenant;
use App\Filament\Staff\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مستأجر' : 'Tenant';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مستأجرون' : 'Tenants';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

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
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('edit'),
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
                // averageRating() is a computed aggregate (reviews()->avg('rating')),
                // not a relationship — Eloquent's magic attribute access requires
                // any non-column method it resolves to return a Relation, so
                // TextColumn::make('averageRating') alone (which reads it via
                // that same magic access) fatally errors for every row. ->state()
                // calls the method directly instead. Also null-safe: a tenant
                // with zero reviews gets avg()===null, not 0.
                TextColumn::make('averageRating')
                    ->label(__('filament.fields.average_rating'))
                    ->state(fn ($record) => $record->averageRating())
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : number_format($state, 2)),
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
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'view' => ViewTenant::route('/{record}'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
