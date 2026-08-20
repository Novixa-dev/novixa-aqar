<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncOnTheMarketProperties;
use App\Models\OnTheMarketSettings;

class SyncOnTheMarketPropertiesCommand extends Command
{
    protected $signature = 'onthemarket:sync-properties {--frequency=hourly : Only sync teams whose sync_frequency matches this tier}';
    protected $description = 'Sync properties with OnTheMarket';

    public function handle()
    {
        // See SyncRightMovePropertiesCommand for the full P0-5 rationale - same
        // per-team-settings, per-frequency-tier fix, same shape.
        $frequency = $this->option('frequency');

        $settingsRows = OnTheMarketSettings::active()->where('sync_frequency', $frequency)->get();

        if ($settingsRows->isEmpty()) {
            $this->info("No active OnTheMarket settings due for the '{$frequency}' tier; nothing to sync.");
            return;
        }

        foreach ($settingsRows as $settings) {
            $this->info("Dispatching OnTheMarket property sync for team {$settings->team_id}...");
            SyncOnTheMarketProperties::dispatch($settings);
        }

        $this->info("OnTheMarket property sync job dispatched for {$settingsRows->count()} team(s).");
    }
}
