<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $type = $this->appointment->appointmentType->name ?? __('Appointment');
        $when = $this->appointment->appointment_date;

        return (new MailMessage)
            ->subject(__(':type appointment reminder', ['type' => $type]))
            ->greeting(__('Hello :name,', ['name' => $this->appointment->name]))
            ->line(__('This is a reminder of your upcoming :type appointment.', ['type' => $type]))
            ->line(__('Date: :date', ['date' => $when->translatedFormat('j F Y')]))
            ->line(__('Time: :time', ['time' => $when->translatedFormat('g:i A')]))
            ->line(__('Please contact us if you need to reschedule.'))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->getKey(),
            'type' => $this->appointment->appointmentType->name ?? 'Appointment',
            'date' => $this->appointment->appointment_date->format('Y-m-d'),
            'time' => $this->appointment->appointment_date->format('H:i'),
        ];
    }
}
