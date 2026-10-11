<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $properties;
    protected $isPersonalized;

    public function __construct($properties, $isPersonalized = false)
    {
        $this->properties = is_array($properties) ? $properties : [$properties];
        $this->isPersonalized = $isPersonalized;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject($this->isPersonalized ? __('Properties picked for you') : __('New properties matching your search'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line($this->isPersonalized
                ? __('Here are some properties we think you will like:')
                : __('New properties matching your criteria have been listed:'));

        foreach ($this->properties as $property) {
            $message->line('**'.$property->title.'** — '.$property->formatted_price.($property->location ? ' — '.$property->location : ''));
        }

        return $message
            ->action(__('Browse properties'), url('/properties'))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return array_map(function ($property) {
            return [
                'property_id' => $property->id,
                'title' => $property->title,
                'price' => $property->price,
                'location' => $property->location,
            ];
        }, $this->properties);
    }
}