<?php

namespace App\Notifications;

use App\Support\StatusLabels;
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
            ->subject(__('A work order was created for your request'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('A work order has been created for your maintenance request.'))
            ->line(__('Work order: :title', ['title' => $this->workOrder->title]))
            ->line(__('Status: :status', ['status' => StatusLabels::label('maintenance', $this->workOrder->status)]))
            ->action(__('View request'), url('/tenant/maintenance-requests/'.$this->workOrder->maintenance_request_id.'/edit'))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
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
