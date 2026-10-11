<?php

namespace App\Notifications;

use App\Support\StatusLabels;
use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestSubmitted extends Notification
{
    use Queueable;

    protected $maintenanceRequest;

    public function __construct(MaintenanceRequest $maintenanceRequest)
    {
        $this->maintenanceRequest = $maintenanceRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Maintenance request received'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your maintenance request has been submitted successfully.'))
            ->line(__('Title: :title', ['title' => $this->maintenanceRequest->title]))
            ->line(__('Status: :status', ['status' => StatusLabels::label('maintenance', $this->maintenanceRequest->status)]))
            ->action(__('View request'), url('/tenant/maintenance-requests/'.$this->maintenanceRequest->id.'/edit'))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'title' => $this->maintenanceRequest->title,
            'status' => $this->maintenanceRequest->status,
        ];
    }
}