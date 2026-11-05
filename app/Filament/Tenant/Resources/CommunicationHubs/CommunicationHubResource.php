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
use Illuminate\Database\Eloquent\Builder;

class CommunicationHubResource extends Resource
{
    protected static ?string $model = Message::class;

    // Every sibling resource in this panel (Lease/Payment/MaintenanceRequest)
    // scopes itself to the current user in its own getEloquentQuery() --
    // panel-level Filament tenancy is off here (Features::teams() is
    // disabled, see TenantPanelProvider.php), so there is no other safety
    // net. This resource had none, which meant any tenant could see and
    // open every message between every user in the entire system by simply
    // opening this page: a live, reachable cross-user privacy leak.
    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->id();

        return parent::getEloquentQuery()
            ->where(fn (Builder $query) => $query
                ->where('sender_id', $userId)
                ->orWhere('recipient_id', $userId));
    }

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
                    ->label(__('filament.fields.to'))
                    ->relationship(
                        'recipient',
                        'name',
                        // Restrict who a tenant can message to users on their
                        // own team -- unrestricted, this let a tenant message
                        // literally any user in the system, across teams.
                        modifyQueryUsing: fn ($query) => $query
                            ->whereHas('teams', fn ($q) => $q->whereKey(auth()->user()->currentTeam?->id))
                            ->whereKeyNot(auth()->id()),
                    )
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
