<?php

namespace App\Services;

use App\Models\Property;
use App\Models\OnTheMarketSettings;
use App\Exceptions\OnTheMarketApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use RuntimeException;

class OnTheMarketService
{
    protected ?string $baseUri = null;
    protected ?string $apiKey = null;
    protected ?int $teamId = null;

    /**
     * Configure this service instance for one team's OnTheMarket integration.
     * See RightMoveService::forSettings() - same P0-5 fix, same shape.
     */
    public function forSettings(OnTheMarketSettings $settings): static
    {
        $this->baseUri = $settings->base_uri;
        $this->apiKey = $settings->api_key;
        $this->teamId = $settings->team_id;

        return $this;
    }

    protected function ensureConfigured(): void
    {
        if ($this->teamId === null) {
            throw new RuntimeException('OnTheMarketService must be configured via forSettings() before use.');
        }
    }

    public function fetchProperties(): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get("{$this->baseUri}/listings");

        if ($response->failed()) {
            throw new OnTheMarketApiException('Failed to fetch properties from OnTheMarket', $response->status());
        }

        return $response->json();
    }

    public function syncAllProperties(): array
    {
        $this->ensureConfigured();

        $properties = Property::where('team_id', $this->teamId)->get();
        $results = [];

        foreach ($properties as $property) {
            try {
                $results[] = $this->syncProperty($property);
            } catch (Exception $e) {
                Log::error("Failed to sync property {$property->id} with OnTheMarket: " . $e->getMessage());
                $results[] = ['id' => $property->id, 'status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }

    public function syncProperty(Property $property): array
    {
        $onTheMarketId = $property->onthemarket_id;

        if ($onTheMarketId) {
            return $this->updateListing($onTheMarketId, $property);
        } else {
            $result = $this->createListing($property);
            $property->update(['onthemarket_id' => $result['id']]);
            return $result;
        }
    }

    protected function createListing(Property $property): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post("{$this->baseUri}/listings", $this->preparePropertyData($property));

        if ($response->failed()) {
            throw new OnTheMarketApiException('Failed to create listing on OnTheMarket', $response->status());
        }

        return $response->json();
    }

    protected function updateListing(string $listingId, Property $property): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->put("{$this->baseUri}/listings/{$listingId}", $this->preparePropertyData($property));

        if ($response->failed()) {
            throw new OnTheMarketApiException('Failed to update listing on OnTheMarket', $response->status());
        }

        return $response->json();
    }

    public function deleteListing(string $listingId): bool
    {
        $this->ensureConfigured();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->delete("{$this->baseUri}/listings/{$listingId}");

        if ($response->failed()) {
            throw new OnTheMarketApiException('Failed to delete listing on OnTheMarket', $response->status());
        }

        return true;
    }

    protected function preparePropertyData(Property $property): array
    {
        return [
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
        ];
    }
}