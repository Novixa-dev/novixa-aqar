<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WalkScoreService
{
    protected $baseUri;

    protected $apiKey;

    public function __construct()
    {
        $this->baseUri = config('services.walkscore.base_uri');
        $this->apiKey = config('services.walkscore.api_key');
    }

    /**
     * Fetch walkability, transit and bike scores for a location.
     *
     * Returns null whenever real scores are not available: no API key
     * configured, the request failed, or the provider returned nothing
     * usable. Callers must treat null as "no data" and render the
     * not-available state (property-detail.blade.php already does). This
     * service never invents scores - a fabricated walkability number is
     * indistinguishable from a real one to a visitor, which makes it
     * worse than showing nothing.
     *
     * Coverage note: Walk Score covers the United States and Canada only.
     * Yemeni locations are flagged "Unsupported Country" by the provider
     * itself (verified against Al Mukalla and Sana'a), so leaving
     * WALKSCORE_API_KEY empty is the correct configuration for this
     * product's primary market, and the not-available state is the
     * honest, permanent outcome there.
     *
     * @param  string  $address
     * @param  float  $latitude
     * @param  float  $longitude
     */
    public function getWalkScore($address, $latitude, $longitude): ?array
    {
        $apiKey = config('services.walkscore.api_key') ?? $this->apiKey;
        $baseUri = config('services.walkscore.base_uri') ?? $this->baseUri;

        if (empty($apiKey)) {
            return null;
        }

        try {
            // Deliberately short: this runs inside a web request, so a slow
            // or unreachable optional integration must not hold the page
            // load open (same rationale as OPENAI_TIMEOUT).
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->get("{$baseUri}/score", [
                    'format' => 'json',
                    'address' => $address,
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'wsapikey' => $apiKey,
                ]);

            if ($response->failed()) {
                Log::warning('Walk Score API request failed', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $data = $response->json();

            // Walk Score signals coverage and quota problems in the body,
            // not the HTTP status: status 1 is the only success case.
            // Anything else (2 = still calculating, 30/31 = bad request or
            // internal error, 41 = quota exceeded, 42 = blocked) means we
            // have no score to show.
            $providerStatus = $data['status'] ?? null;
            if ($providerStatus !== null && (int) $providerStatus !== 1) {
                Log::warning('Walk Score API returned a non-success status', [
                    'provider_status' => $providerStatus,
                ]);

                return null;
            }

            $walkScore = $this->validateScore($data['walkscore'] ?? null);

            if ($walkScore === null) {
                return null;
            }

            return [
                'walk_score' => $walkScore,
                'walk_description' => $data['description'] ?? null,
                'transit_score' => $this->validateScore($data['transit']['score'] ?? null),
                'transit_description' => $data['transit']['description'] ?? null,
                'bike_score' => $this->validateScore($data['bike']['score'] ?? null),
                'bike_description' => $data['bike']['description'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Walk Score fetch failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Validate and clamp a score to the 0-100 range.
     *
     * @param  mixed  $score
     */
    protected function validateScore($score): ?int
    {
        if ($score === null) {
            return null;
        }

        $score = (int) $score;

        return max(0, min(100, $score));
    }
}
