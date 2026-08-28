<?php

namespace App\Filament\Contractor\Pages;

use Filament\Pages\Page;
use App\Models\Contractor;
use App\Models\MaintenanceRequest;

class Dashboard extends Page
{
    protected string $view = 'filament.contractor.dashboard';

    public $openJobs;
    public $completedJobs;
    public $pendingPayments;

    public function mount(): void
    {
        // The contractor persona manages a Contractor profile record keyed
        // by email (same lookup MaintenanceRequestResource::currentContractorId()
        // uses) — MaintenanceRequest.contractor_id points at contractors.id,
        // not users.id, so auth()->id() cannot be used directly here.
        // Previously every line below was commented out (referencing a
        // App\Models\Job class that doesn't exist in this codebase), which
        // left $this->openJobs/etc. permanently null — the Blade view
        // rendered three stat cards with blank values for every contractor.
        $contractorId = Contractor::where('email', auth()->user()?->email)->value('id');

        $this->openJobs = MaintenanceRequest::where('contractor_id', $contractorId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        $this->completedJobs = MaintenanceRequest::where('contractor_id', $contractorId)
            ->where('status', 'completed')
            ->count();
        $this->pendingPayments = MaintenanceRequest::where('contractor_id', $contractorId)
            ->where('payment_status', 'pending')
            ->count();
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'openJobs' => $this->openJobs,
            'completedJobs' => $this->completedJobs,
            'pendingPayments' => $this->pendingPayments,
        ];
    }
}
