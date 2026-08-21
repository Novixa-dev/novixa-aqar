<?php

namespace App\Filament\Landlord\Resources\Properties;

use App\Models\Property;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقار' : 'Property';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقاراتي' : 'My Properties';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'عقاراتي' : 'My Properties';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->disabled(),
            TextInput::make('price')->disabled(),
            TextInput::make('status')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(app()->getLocale() === 'ar' ? 'العنوان' : 'Title')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('price')
                    ->label(app()->getLocale() === 'ar' ? 'السعر' : 'Price')
                    ->money(strtolower(config('currencies.default'))),
                TextColumn::make('status')
                    ->label(app()->getLocale() === 'ar' ? 'الحالة' : 'Status')
                    ->badge(),
                TextColumn::make('list_date')
                    ->label(app()->getLocale() === 'ar' ? 'تاريخ العرض' : 'Listed')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->emptyStateHeading(app()->getLocale() === 'ar'
                ? 'لا توجد عقارات مرتبطة بحسابك بعد'
                : 'No properties are linked to your account yet');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'view' => Pages\ViewProperty::route('/{record}'),
        ];
    }
}
