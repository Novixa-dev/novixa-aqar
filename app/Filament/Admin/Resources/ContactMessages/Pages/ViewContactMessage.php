<?php

namespace App\Filament\Admin\Resources\ContactMessages\Pages;

use App\Filament\Admin\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->update(['is_read' => true]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(app()->getLocale() === 'ar' ? 'الاسم' : 'Name'),
                TextEntry::make('email')
                    ->label(app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email')
                    ->copyable(),
                TextEntry::make('phone')
                    ->label(app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone'),
                TextEntry::make('interest')
                    ->label(app()->getLocale() === 'ar' ? 'نوع الاهتمام' : 'Interest')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'buying' => app()->getLocale() === 'ar' ? 'شراء عقار' : 'Buying a property',
                        'selling' => app()->getLocale() === 'ar' ? 'بيع عقار' : 'Selling a property',
                        'renting' => app()->getLocale() === 'ar' ? 'استئجار عقار' : 'Renting a property',
                        'other' => app()->getLocale() === 'ar' ? 'أخرى / استشارات' : 'Other inquiries',
                        default => '—',
                    }),
                TextEntry::make('message')
                    ->label(app()->getLocale() === 'ar' ? 'الرسالة' : 'Message')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(app()->getLocale() === 'ar' ? 'وقت الإرسال' : 'Sent at')
                    ->dateTime(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label(app()->getLocale() === 'ar' ? 'رد' : 'Reply')
                ->icon('heroicon-o-envelope')
                ->color('primary')
                ->url(fn () => 'mailto:' . $this->getRecord()->email),
        ];
    }
}
