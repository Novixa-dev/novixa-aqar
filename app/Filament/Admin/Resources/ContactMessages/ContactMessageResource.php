<?php

namespace App\Filament\Admin\Resources\ContactMessages;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Admin\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'رسالة تواصل' : 'Contact Message';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'صندوق التواصل' : 'Contact Inbox';
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_read', false)->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('message')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('is_read')
                    ->badge()
                    ->label(app()->getLocale() === 'ar' ? 'مقروءة' : 'Read')
                    ->colors(fn () => [
                        'warning' => false,
                        'success' => true,
                    ])
                    ->formatStateUsing(fn ($state) => $state
                        ? (app()->getLocale() === 'ar' ? 'مقروءة' : 'Read')
                        : (app()->getLocale() === 'ar' ? 'جديدة' : 'New')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_read')
                    ->label(app()->getLocale() === 'ar' ? 'مقروءة' : 'Read'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markRead')
                    ->label(app()->getLocale() === 'ar' ? 'تحديد كمقروءة' : 'Mark read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ContactMessage $record) => ! $record->is_read)
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => true])),
                Action::make('markUnread')
                    ->label(app()->getLocale() === 'ar' ? 'تحديد كغير مقروءة' : 'Mark unread')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->visible(fn (ContactMessage $record) => $record->is_read)
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => false])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
