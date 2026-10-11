<?php

namespace App\Notifications;

use App\Models\Lease;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaseRenewalReminder extends Notification
{
    use Queueable;

    protected $lease;

    public function __construct(Lease $lease)
    {
        $this->lease = $lease;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Lease renewal reminder'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your lease for :address is up for renewal on :date.', [
                'address' => $this->lease->property->address ?? $this->lease->property->title ?? '',
                'date' => $this->lease->end_date?->translatedFormat('j F Y') ?? '',
            ]))
            ->action(__('View lease'), url('/tenant/leases/'.$this->lease->id))
            ->line(__('Please contact us to discuss renewal options.'))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return [
            'lease_id' => $this->lease->id,
            'property_address' => $this->lease->property->address,
            'end_date' => $this->lease->end_date->toDateString(),
        ];
    }
}