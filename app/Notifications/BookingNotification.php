<?php

namespace App\Notifications;

use App\Support\StatusLabels;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $action;

    public function __construct(Booking $booking, string $action)
    {
        $this->booking = $booking;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $property = $this->booking->property->title ?? '';
        $status = StatusLabels::label('booking', $this->action);

        $message = (new MailMessage)
            ->subject(__('Viewing booking: :status', ['status' => $status]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']));

        $message = match ($this->action) {
            'reminder' => $message->line(__('This is a reminder of your viewing of :property.', ['property' => $property])),
            'feedback_request' => $message->line(__('Thank you for viewing :property. We would value your feedback.', ['property' => $property])),
            default => $message->line(__('Your viewing booking for :property is now: :status.', ['property' => $property, 'status' => $status])),
        };

        return $message
            ->line(__('Date: :date', ['date' => $this->booking->date->translatedFormat('j F Y')]))
            ->line(__('Time: :time', ['time' => $this->booking->time->translatedFormat('g:i A')]))
            ->action(__('View property'), url('/properties/'.$this->booking->property_id))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'action' => $this->action,
            'property' => $this->booking->property->title,
            'date' => $this->booking->date->format('Y-m-d'),
            'time' => $this->booking->time->format('H:i'),
        ];
    }

}