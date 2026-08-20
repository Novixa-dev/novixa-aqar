<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\OnTheMarketSettings;
use App\Services\OnTheMarketService;
use Illuminate\Support\Facades\Log;

class SyncOnTheMarketProperties implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected OnTheMarketSettings $settings)
    {
        //
    }

    public function handle(OnTheMarketService $onTheMarketService)
    {
        Log::info('Starting OnTheMarket property sync', ['team_id' => $this->settings->team_id]);
        try {
            $result = $onTheMarketService->forSettings($this->settings)->syncAllProperties();
            Log::info('OnTheMarket property sync completed', ['team_id' => $this->settings->team_id, 'result' => $result]);
        } catch (Exception $e) {
            Log::error('OnTheMarket sync job failed: ' . $e->getMessage(), ['team_id' => $this->settings->team_id]);
            throw $e;
        }
    }
}