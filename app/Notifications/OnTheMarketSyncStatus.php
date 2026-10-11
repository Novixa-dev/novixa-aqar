<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OnTheMarketSyncStatus extends Notification
{
    use Queueable;

    protected $status;
    protected $message;

    public function __construct($status, $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('OnTheMarket sync: :status', ['status' => $this->status]))
            ->line(__('The OnTheMarket sync has completed.'))
            ->line(__('Status: :status', ['status' => $this->status]))
            ->line(__('Details: :message', ['message' => $this->message]))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
        ];
    }
}