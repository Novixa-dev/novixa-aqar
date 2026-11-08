<?php

namespace App\Filament\Admin\Resources\Branches;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Admin\Resources\Branches\Pages\ListBranches;
use App\Filament\Admin\Resources\Branches\Pages\CreateBranch;
use App\Filament\Admin\Resources\Branches\Pages\EditBranch;
use App\Models\Branch;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'فرع' : 'Branch';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'فروع' : 'Branches';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label(__('filament.fields.name')),
                TextInput::make('address')
                    ->required()
                    ->label(__('filament.fields.address')),
                TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->label(__('filament.fields.phone_number')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('filament.fields.name')),
                TextColumn::make('address')
                    ->searchable()
                    ->label(__('filament.fields.address')),
                TextColumn::make('phone_number')
                    ->label(__('filament.fields.phone_number')),
            ])
            ->filters([
                //
            ]);
    }

    public static function getLabel(): string
    {
        return 'Branch';
    }

    public static function getPluralLabel(): string
    {
        return 'Branches';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-building-office';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }
}
