<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkOrderCreated extends Notification
{
    use Queueable;

    protected $workOrder;

    public function __construct(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A work order has been created for your maintenance request.')
            ->line('Work order: ' . $this->workOrder->title)
            ->line('Status: ' . $this->workOrder->status)
            ->action('View Maintenance Request', url('/maintenance-requests/' . $this->workOrder->maintenance_request_id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'work_order_id' => $this->workOrder->id,
            'maintenance_request_id' => $this->workOrder->maintenance_request_id,
            'title' => $this->workOrder->title,
            'status' => $this->workOrder->status,
        ];
    }
}
