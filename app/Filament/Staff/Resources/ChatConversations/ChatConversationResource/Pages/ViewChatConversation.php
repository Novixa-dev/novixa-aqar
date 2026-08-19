<?php

namespace App\Filament\Staff\Resources\ChatConversations\ChatConversationResource\Pages;

use App\Filament\Staff\Resources\ChatConversations\ChatConversationResource;
use App\Models\ChatMessage;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components;

class ViewChatConversation extends ViewRecord
{
    protected static string $resource = ChatConversationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Conversation Info')
                    ->schema([
                        Components\TextEntry::make('session_id')
                            ->label(__('filament.fields.session_id')),
                        Components\TextEntry::make('user.name')
                            ->label(__('filament.fields.user')),
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'escalated' => 'warning',
                                'closed' => 'gray',
                            }),
                        Components\TextEntry::make('assignedAgent.name')
                            ->label(__('filament.fields.assigned_agent'))
                            ->default('Not assigned'),
                        Components\TextEntry::make('created_at')
                            ->label(__('filament.fields.started_at'))
                            ->dateTime(),
                        Components\TextEntry::make('escalated_at')
                            ->label(__('filament.fields.escalated_at'))
                            ->dateTime()
                            ->default('—'),
                    ])
                    ->columns(2),
                Components\Section::make('Messages')
                    ->schema([
                        Components\RepeatableEntry::make('messages')
                            ->schema([
                                Components\TextEntry::make('sender_type')
                                    ->label(__('filament.fields.from'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'user' => 'primary',
                                        'bot' => 'gray',
                                        'agent' => 'success',
                                        default => 'gray',
                                    }),
                                Components\TextEntry::make('message')
                                    ->label(__('filament.fields.message'))
                                    ->columnSpanFull(),
                                Components\TextEntry::make('created_at')
                                    ->label(__('filament.fields.time'))
                                    ->dateTime(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
