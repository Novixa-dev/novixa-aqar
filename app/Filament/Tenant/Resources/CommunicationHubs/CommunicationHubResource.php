<?php

namespace App\Filament\Tenant\Resources\CommunicationHubs;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Tenant\Resources\CommunicationHubs\Pages\ListCommunicationHub;
use App\Filament\Tenant\Resources\CommunicationHubs\Pages\CreateCommunicationHub;
use App\Filament\Tenant\Resources\CommunicationHubs\Pages\ViewCommunicationHub;
use App\Filament\Tenant\Resources\CommunicationHubResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class CommunicationHubResource extends Resource
{
    protected static ?string $model = Message::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مراسلة' : 'Communication';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مراسلات' : 'Communications';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'مركز التواصل' : 'Communication Hub';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('recipient_id')
                    ->relationship('recipient', 'name')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->maxLength(1000),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.name')->label(__('filament.fields.from')),
                TextColumn::make('recipient.name')->label(__('filament.fields.to')),
                TextColumn::make('content')->limit(50),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
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
            'index' => ListCommunicationHub::route('/'),
            'create' => CreateCommunicationHub::route('/create'),
            'view' => ViewCommunicationHub::route('/{record}'),
        ];
    }
}
