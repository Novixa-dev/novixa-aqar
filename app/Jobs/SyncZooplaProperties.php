<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ZooplaSettings;
use App\Services\ZooplaPortalSyncService;
use Illuminate\Support\Facades\Log;

class SyncZooplaProperties implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ZooplaSettings $settings)
    {
        //
    }

    public function handle(ZooplaPortalSyncService $syncService)
    {
        Log::info('Starting Zoopla property sync', ['team_id' => $this->settings->team_id]);
        try {
            $result = $syncService->syncProperties($this->settings);
            Log::info('Zoopla property sync completed', [
                'team_id' => $this->settings->team_id,
                'synced_count' => $result['synced'],
                'failed_count' => $result['failed']
            ]);
        } catch (Exception $e) {
            Log::error('Zoopla property sync failed', ['team_id' => $this->settings->team_id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}