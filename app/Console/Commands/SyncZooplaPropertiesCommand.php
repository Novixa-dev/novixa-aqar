<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncZooplaProperties;
use App\Models\ZooplaSettings;

class SyncZooplaPropertiesCommand extends Command
{
    protected $signature = 'zoopla:sync-properties {--frequency=hourly : Only sync teams whose sync_frequency matches this tier}';
    protected $description = 'Sync properties with Zoopla';

    public function handle()
    {
        // See SyncRightMovePropertiesCommand for the full P0-5 rationale - same
        // per-team-settings, per-frequency-tier fix, same shape.
        $frequency = $this->option('frequency');

        $settingsRows = ZooplaSettings::active()
            ->whereNotNull('team_id')
            ->where('sync_frequency', $frequency)
            ->get();

        if ($settingsRows->isEmpty()) {
            $this->info("No active, team-assigned Zoopla settings due for the '{$frequency}' tier; nothing to sync.");
            return;
        }

        foreach ($settingsRows as $settings) {
            $this->info("Dispatching Zoopla property sync for team {$settings->team_id}...");
            SyncZooplaProperties::dispatch($settings);
        }

        $this->info("Zoopla property sync job dispatched for {$settingsRows->count()} team(s).");
    }
}