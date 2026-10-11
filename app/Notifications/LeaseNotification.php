<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Lease;

class LeaseNotification extends Notification
{
    use Queueable;

    protected $lease;
    protected $type;

    public function __construct(Lease $lease, string $type)
    {
        $this->lease = $lease;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('/tenant/leases/'.$this->lease->id);
        $message = (new MailMessage)->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']));

        return match ($this->type) {
            'termination' => $message
                ->subject(__('Your lease has ended'))
                ->line(__('Your lease has been terminated.'))
                ->action(__('View lease'), $url)
                ->line(__('Please make sure all move-out steps are completed.'))
                ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name()),
            default => $message
                ->subject(__('Your lease is due for renewal'))
                ->line(__('Your lease is up for renewal.'))
                ->action(__('View lease'), $url)
                ->line(__('Please contact us to discuss renewal options.'))
                ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name()),
        };
    }
}