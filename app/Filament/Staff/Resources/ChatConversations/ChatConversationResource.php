<?php

namespace App\Filament\Staff\Resources\ChatConversations;

use App\Models\ChatConversation;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Staff\Resources\ChatConversations\ChatConversationResource\Pages;

class ChatConversationResource extends Resource
{
    protected static ?string $model = ChatConversation::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'محادثة' : 'Chat Conversation';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'محادثات' : 'Chat Conversations';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Support';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'المحادثات' : 'Chat Conversations';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Conversation Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label(__('filament.fields.user'))
                            ->disabled(),
                        Forms\Components\TextInput::make('session_id')
                            ->label(__('filament.fields.session_id'))
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options(fn () => app()->getLocale() === 'ar' ? [
                                'active' => 'نشط',
                                'escalated' => 'تم تصعيده',
                                'closed' => 'مغلق',
                            ] : [
                                'active' => 'Active',
                                'escalated' => 'Escalated',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        Forms\Components\Select::make('assigned_agent_id')
                            ->relationship('assignedAgent', 'name')
                            ->label(__('filament.fields.assigned_agent'))
                            ->searchable(),
                        Forms\Components\DateTimePicker::make('escalated_at')
                            ->label(__('filament.fields.escalated_at'))
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament.fields.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.fields.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'escalated' => 'warning',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedAgent.name')
                    ->label(__('filament.fields.assigned_agent'))
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('messages_count')
                    ->counts('messages')
                    ->label(__('filament.fields.messages'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('escalated_at')
                    ->label(__('filament.fields.escalated_at'))
                    ->dateTime()
                    ->sortable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.started_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(fn () => app()->getLocale() === 'ar' ? [
                        'active' => 'نشط',
                        'escalated' => 'تم تصعيده',
                        'closed' => 'مغلق',
                    ] : [
                        'active' => 'Active',
                        'escalated' => 'Escalated',
                        'closed' => 'Closed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListChatConversations::route('/'),
            'view' => Pages\ViewChatConversation::route('/{record}'),
            'edit' => Pages\EditChatConversation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'escalated')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
