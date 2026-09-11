<?php

namespace Tests\Unit;

use App\Services\AIDescriptionService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIDescriptionServiceTest extends TestCase
{
    private AIDescriptionService $service;

    private array $propertyData = [
        'property_type' => 'House',
        'bedrooms' => 3,
        'bathrooms' => 2,
        'area_sqft' => 1500,
        'location' => 'Aden',
        'price' => 35000000,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AIDescriptionService();

        // These tests are about the provider path, which the service only
        // takes when a key is configured. Without this they faked an
        // endpoint that was never called and asserted on the local
        // fallback's output while appearing to test the API.
        Config::set('services.openai.api_key', 'test-key');
    }

    public function test_generates_description_with_successful_api_response(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Beautiful 3-bedroom house in Aden with modern amenities.']],
                ],
            ], 200),
        ]);

        $description = $this->service->generateDescription($this->propertyData, 'professional');

        $this->assertSame('Beautiful 3-bedroom house in Aden with modern amenities.', $description);
    }

    /**
     * A failing provider must not fail the request.
     *
     * This test previously asserted the opposite -- that the service threw
     * "Failed to generate AI description". That expectation described a
     * defect rather than a requirement: PropertySubmissionForm calls this
     * with no try/catch, so the throw reached the user as a 500 every time
     * the provider was slow, rate-limited, or (as was the case in this
     * project for its whole life) not configured at all.
     *
     * The contract is now that the caller always gets usable text.
     */
    public function test_falls_back_to_local_generation_on_api_failure(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([], 500),
        ]);

        $description = $this->service->generateDescription($this->propertyData, 'professional');

        $this->assertNotEmpty(trim($description));
        $this->assertStringContainsString('Aden', $description);
    }

    public function test_it_does_not_call_the_provider_when_no_key_is_configured(): void
    {
        Config::set('services.openai.api_key', null);
        Http::fake();

        $description = $this->service->generateDescription($this->propertyData, 'professional');

        $this->assertNotEmpty(trim($description));
        Http::assertNothingSent();
    }

    public function test_generates_description_with_luxury_tone(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Exquisite penthouse with panoramic city views.']],
                ],
            ], 200),
        ]);

        $description = $this->service->generateDescription([
            'property_type' => 'Penthouse',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'area_sqft' => 3000,
            'location' => 'Mukalla',
            'price' => 500000000,
        ], 'luxury');

        $this->assertSame('Exquisite penthouse with panoramic city views.', $description);
    }

    public function test_the_tone_changes_the_locally_generated_text(): void
    {
        Config::set('services.openai.api_key', null);

        $professional = $this->service->generateDescription($this->propertyData, 'professional');
        $luxury = $this->service->generateDescription($this->propertyData, 'luxury');
        $casual = $this->service->generateDescription($this->propertyData, 'casual');

        $this->assertNotSame($professional, $luxury);
        $this->assertNotSame($professional, $casual);
        $this->assertNotSame($luxury, $casual);
    }
}
