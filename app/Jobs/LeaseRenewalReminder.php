<?php

namespace App\Jobs;

use App\Models\Lease;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LeaseRenewalReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService)
    {
        $leasesUpForRenewal = Lease::where('status', 'active')
            ->where('end_date', '<=', now()->addDays(30))
            ->get();

        foreach ($leasesUpForRenewal as $lease) {
            // tenant_id is nullable -- a lease can exist before a tenant is
            // assigned (CheckLeaseNotifications already guards against this
            // for the same query; this job didn't).
            if ($lease->tenant) {
                $notificationService->sendLeaseRenewalReminder($lease->tenant, $lease);
            }
        }
    }
}