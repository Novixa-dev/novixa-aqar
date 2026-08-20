<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncRightMoveProperties;
use App\Models\RightMoveSettings;

class SyncRightMovePropertiesCommand extends Command
{
    protected $signature = 'rightmove:sync-properties {--frequency=hourly : Only sync teams whose sync_frequency matches this tier}';
    protected $description = 'Sync properties with RightMove';

    public function handle()
    {
        // One job per team's own settings/API key - each job only ever touches
        // that team's properties (RightMoveService::forSettings()). Dispatching
        // a single global job here was the root cause of P0-5 (every team's
        // properties got pushed to whichever one team's key was configured).
        //
        // Filtering by --frequency here (rather than gating the whole command
        // behind Schedule::when(RightMoveSettings::active()->first()->...))
        // also fixes a second bug: the old ->first() check only ever looked at
        // one row, so with more than one team configured, only the first team's
        // frequency preference actually took effect for every team.
        $frequency = $this->option('frequency');

        $settingsRows = RightMoveSettings::active()->where('sync_frequency', $frequency)->get();

        if ($settingsRows->isEmpty()) {
            $this->info("No active RightMove settings due for the '{$frequency}' tier; nothing to sync.");
            return;
        }

        foreach ($settingsRows as $settings) {
            $this->info("Dispatching RightMove property sync for team {$settings->team_id}...");
            SyncRightMoveProperties::dispatch($settings);
        }

        $this->info("RightMove property sync job dispatched for {$settingsRows->count()} team(s).");
    }
}
