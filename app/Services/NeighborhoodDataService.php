<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class NeighborhoodDataService
{
    protected $baseUri;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUri = config('services.neighborhood_data.base_uri');
        $this->apiKey = config('services.neighborhood_data.api_key');
    }

    public function getNeighborhoodData($zipCode)
    {
        // No key configured: this is a deliberate, visible "not connected"
        // state (see .env.example), not an error. Without this guard every
        // property-detail request tries to reach the placeholder
        // NEIGHBORHOOD_DATA_BASE_URI (api.example.com) and blocks the whole
        // page load for a full DNS/connect timeout before failing anyway.
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            // Deliberately short: this call runs inside a web request, so a
            // slow or unreachable optional integration must not hold the
            // page load open (same rationale as OPENAI_TIMEOUT).
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(5)->connectTimeout(3)->get("{$this->baseUri}/neighborhood/{$zipCode}");

            if ($response->failed()) {
                throw new Exception('Failed to fetch neighborhood data');
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Neighborhood data fetch failed: ' . $e->getMessage());
            return null;
        }
    }
}