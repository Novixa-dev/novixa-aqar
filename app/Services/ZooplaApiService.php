<?php

namespace App\Services;

use App\Models\Property;
use App\Models\ZooplaSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use RuntimeException;

class ZooplaApiService
{
    protected ?string $baseUri = null;
    protected ?string $apiKey = null;
    protected ?int $teamId = null;

    /**
     * Configure this service instance for one team's Zoopla integration.
     * The constructor previously always called ZooplaSettings::first() -
     * a single global settings row/API key used for every team's properties.
     * See RightMoveService::forSettings() for the same P0-5 fix, same shape.
     */
    public function forSettings(ZooplaSettings $settings): static
    {
        $this->baseUri = $settings->base_uri;
        $this->apiKey = $settings->api_key;
        $this->teamId = $settings->team_id;

        return $this;
    }

    protected function ensureConfigured(): void
    {
        if ($this->teamId === null) {
            throw new RuntimeException('ZooplaApiService must be configured via forSettings() before use.');
        }
    }

    public function uploadProperty(Property $property): bool
    {
        $this->ensureConfigured();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post("{$this->baseUri}/listings", $this->preparePropertyData($property));

            if ($response->failed()) {
                Log::error('Failed to upload property to Zoopla', [
                    'property_id' => $property->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }

            $data = $response->json();
            $property->update(['zoopla_id' => $data['id'] ?? null]);

            Log::info('Property uploaded to Zoopla', [
                'property_id' => $property->id,
                'zoopla_id' => $property->zoopla_id,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Exception uploading property to Zoopla', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function updateProperty(Property $property): bool
    {
        $this->ensureConfigured();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->put("{$this->baseUri}/listings/{$property->zoopla_id}", $this->preparePropertyData($property));

            if ($response->failed()) {
                Log::error('Failed to update property on Zoopla', [
                    'property_id' => $property->id,
                    'zoopla_id' => $property->zoopla_id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }

            Log::info('Property updated on Zoopla', [
                'property_id' => $property->id,
                'zoopla_id' => $property->zoopla_id,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Exception updating property on Zoopla', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function deleteProperty(Property $property): bool
    {
        $this->ensureConfigured();

        if (!$property->zoopla_id) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->delete("{$this->baseUri}/listings/{$property->zoopla_id}");

            if ($response->failed()) {
                Log::error('Failed to delete property from Zoopla', [
                    'property_id' => $property->id,
                    'zoopla_id' => $property->zoopla_id,
                    'status' => $response->status(),
                ]);
                return false;
            }

            $property->update(['zoopla_id' => null]);

            Log::info('Property deleted from Zoopla', ['property_id' => $property->id]);

            return true;
        } catch (Exception $e) {
            Log::error('Exception deleting property from Zoopla', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function preparePropertyData(Property $property): array
    {
        $data = [
            'propertyType' => $property->property_type,
            'description' => $property->description,
            'price' => $property->price,
            'address' => [
                'street' => $property->location,
                'postalCode' => $property->postal_code,
                'country' => $property->country,
            ],
            'bedrooms' => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'area' => $property->area_sqft,
            'yearBuilt' => $property->year_built,
            'status' => $property->status,
            // Add more fields as required by Zoopla API
        ];

        Log::info('Preparing Zoopla property data', [
            'property_id' => $property->id,
            'zoopla_id' => $property->zoopla_id,
            'data' => $data,
        ]);

        return $data;
    }
}
