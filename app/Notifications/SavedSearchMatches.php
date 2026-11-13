<?php

namespace App\Notifications;

use App\Models\SavedSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/** New listings matching one of a user's saved searches (P33-2). */
class SavedSearchMatches extends Notification
{
    use Queueable;

    /** @param Collection<int, \App\Models\Property> $properties */
    public function __construct(
        public readonly SavedSearch $search,
        public readonly Collection $properties,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ar = app()->getLocale() === 'ar';
        $count = $this->properties->count();
        $summary = $this->search->summary();

        $mail = (new MailMessage)
            ->subject($ar ? "عقارات جديدة تطابق بحثك: {$summary}" : "New listings match your search: {$summary}")
            ->greeting($ar ? 'مرحباً '.$notifiable->name : 'Hello '.$notifiable->name)
            ->line($ar
                ? "تم نشر {$count} عقار جديد يطابق بحثك المحفوظ ({$summary}):"
                : "{$count} new ".($count === 1 ? 'listing matches' : 'listings match')." your saved search ({$summary}):");

        foreach ($this->properties->take(5) as $property) {
            $mail->line('• '.$property->title.' — '.number_format((float) $property->price).' '.$property->currency.' — '.route('property.detail', $property->id));
        }

        return $mail
            ->action($ar ? 'عرض كل النتائج' : 'View all results', $this->search->url())
            ->line($ar
                ? 'يمكنك إيقاف هذه التنبيهات أو حذف البحث من صفحة "عمليات البحث المحفوظة": '.route('saved-searches')
                : 'You can turn these alerts off or delete the search on your "Saved searches" page: '.route('saved-searches'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'saved_search_matches',
            'saved_search_id' => $this->search->id,
            'summary' => $this->search->summary(),
            'count' => $this->properties->count(),
            'property_ids' => $this->properties->pluck('id')->take(10)->values()->all(),
            'url' => $this->search->url(),
        ];
    }
}
