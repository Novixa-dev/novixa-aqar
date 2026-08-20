<?php

namespace App\Services;

use Exception;
use App\Models\Property;
use App\Models\ZooplaSettings;
use Illuminate\Support\Facades\Log;

class ZooplaPortalSyncService
{
    protected $zooplaApiService;

    public function __construct(ZooplaApiService $zooplaApiService)
    {
        $this->zooplaApiService = $zooplaApiService;
    }

    public function syncProperties(ZooplaSettings $settings)
    {
        $this->zooplaApiService->forSettings($settings);

        // team_id is nullable on zoopla_settings (added after the fact - see
        // AQAR_PRE_RELEASE_AUDIT_REPORT.md P0-5) so an unconfigured settings
        // row can't accidentally sync every team's properties.
        if ($settings->team_id === null) {
            Log::warning('Skipping Zoopla sync: settings row has no team_id assigned', ['zoopla_settings_id' => $settings->id]);
            return ['synced' => 0, 'failed' => 0];
        }

        $properties = Property::where('team_id', $settings->team_id)->needsSyncing()->get();
        $synced = 0;
        $failed = 0;

        foreach ($properties as $property) {
            try {
                $success = $property->zoopla_id
                    ? $this->updateProperty($property)
                    : $this->uploadProperty($property);

                if ($success) {
                    $synced++;
                } else {
                    $failed++;
                }
            } catch (Exception $e) {
                Log::error('Error syncing property with Zoopla', [
                    'property_id' => $property->id,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        return ['synced' => $synced, 'failed' => $failed];
    }

    protected function updateProperty(Property $property)
    {
        $success = $this->zooplaApiService->updateProperty($property);
        if ($success) {
            $property->last_synced_at = now();
            $property->save();
        }
        return $success;
    }

    protected function uploadProperty(Property $property)
    {
        $success = $this->zooplaApiService->uploadProperty($property);
        if ($success) {
            $property->last_synced_at = now();
            $property->save();
        }
        return $success;
    }
}