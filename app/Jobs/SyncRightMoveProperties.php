<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\RightMoveSettings;
use App\Services\RightMoveService;
use Illuminate\Support\Facades\Log;

class SyncRightMoveProperties implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected RightMoveSettings $settings)
    {
        //
    }

    public function handle(RightMoveService $rightMoveService)
    {
        Log::info('Starting RightMove property sync', ['team_id' => $this->settings->team_id]);
        try {
            $result = $rightMoveService->forSettings($this->settings)->syncAllProperties();
            Log::info('RightMove property sync completed', ['team_id' => $this->settings->team_id, 'result' => $result]);
        } catch (Exception $e) {
            Log::error('RightMove sync job failed: ' . $e->getMessage(), ['team_id' => $this->settings->team_id]);
            throw $e;
        }
    }
}