<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaseAgreementReady extends Notification
{
    use Queueable;

    protected $leaseAgreementId;

    public function __construct($leaseAgreementId)
    {
        $this->leaseAgreementId = $leaseAgreementId;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Your lease agreement is ready'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? '']))
            ->line(__('Your lease agreement is ready for signing.'))
            // There is no per-agreement page for tenants; their lease list
            // in the tenant panel shows it.
            ->action(__('View my leases'), url('/tenant/leases'))
            ->salutation(__('Kind regards,')."\n".app(\App\Support\Brand::class)->name());
    }

    public function toArray($notifiable)
    {
        return [
            'lease_agreement_id' => $this->leaseAgreementId,
            'message' => 'Your lease agreement is ready for signing.',
        ];
    }
}