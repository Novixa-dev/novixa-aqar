<?php

namespace Tests\Unit;

use App\Services\WalkScoreService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WalkScoreServiceTest extends TestCase
{
    protected $walkScoreService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walkScoreService = new WalkScoreService();
    }

    public function test_returns_null_when_api_key_not_configured(): void
    {
        Config::set('services.walkscore.api_key', null);

        Http::fake();

        $result = $this->walkScoreService->getWalkScore(
            'Al Mukalla, Hadhramaut',
            14.5425,
            49.1242
        );

        // No key means no real data. The service must say so rather than
        // invent scores the visitor cannot tell from real ones.
        $this->assertNull($result);

        // And it must not make a network call at all - an unconfigured
        // integration that still dials out costs a request timeout on
        // every page load.
        Http::assertNothingSent();
    }

    public function test_returns_null_when_api_request_fails(): void
    {
        Config::set('services.walkscore.api_key', 'test_key');
        Config::set('services.walkscore.base_uri', 'https://api.walkscore.com');

        Http::fake([
            'api.walkscore.com/*' => Http::response([], 500),
        ]);

        $result = $this->walkScoreService->getWalkScore(
            '123 Main St, Seattle',
            47.6062,
            -122.3321
        );

        $this->assertNull($result);
    }

    public function test_returns_null_when_provider_reports_non_success_status(): void
    {
        Config::set('services.walkscore.api_key', 'test_key');
        Config::set('services.walkscore.base_uri', 'https://api.walkscore.com');

        // Status 2 = score not available for this location. Walk Score
        // returns HTTP 200 with a non-1 status for unsupported or
        // uncalculated locations, which is exactly what a Yemeni
        // coordinate would produce.
        Http::fake([
            'api.walkscore.com/*' => Http::response([
                'status' => 2,
                'walkscore' => null,
            ], 200),
        ]);

        $result = $this->walkScoreService->getWalkScore(
            'Al Mukalla, Hadhramaut',
            14.5425,
            49.1242
        );

        $this->assertNull($result);
    }

    public function test_returns_null_when_provider_omits_a_walk_score(): void
    {
        Config::set('services.walkscore.api_key', 'test_key');
        Config::set('services.walkscore.base_uri', 'https://api.walkscore.com');

        Http::fake([
            'api.walkscore.com/*' => Http::response([
                'status' => 1,
                'description' => 'Test',
            ], 200),
        ]);

        $result = $this->walkScoreService->getWalkScore(
            '123 Main St, Seattle',
            47.6062,
            -122.3321
        );

        $this->assertNull($result);
    }

    public function test_returns_real_scores_when_provider_succeeds(): void
    {
        Config::set('services.walkscore.api_key', 'test_key');
        Config::set('services.walkscore.base_uri', 'https://api.walkscore.com');

        Http::fake([
            'api.walkscore.com/*' => Http::response([
                'status' => 1,
                'walkscore' => 88,
                'description' => "Very Walkable",
                'transit' => ['score' => 72, 'description' => 'Excellent Transit'],
                'bike' => ['score' => 65, 'description' => 'Bikeable'],
            ], 200),
        ]);

        $result = $this->walkScoreService->getWalkScore(
            '123 Main St, Seattle',
            47.6062,
            -122.3321
        );

        $this->assertIsArray($result);
        $this->assertSame(88, $result['walk_score']);
        $this->assertSame('Very Walkable', $result['walk_description']);
        $this->assertSame(72, $result['transit_score']);
        $this->assertSame('Excellent Transit', $result['transit_description']);
        $this->assertSame(65, $result['bike_score']);
        $this->assertSame('Bikeable', $result['bike_description']);
    }

    public function test_validates_scores_within_range(): void
    {
        Config::set('services.walkscore.api_key', 'test_key');
        Config::set('services.walkscore.base_uri', 'https://api.walkscore.com');

        // Provider response with out-of-range scores.
        Http::fake([
            'api.walkscore.com/*' => Http::response([
                'status' => 1,
                'walkscore' => 150, // Invalid: > 100
                'description' => 'Test',
                'transit' => ['score' => -10, 'description' => 'Test'], // Invalid: < 0
                'bike' => ['score' => 75, 'description' => 'Test'], // Valid
            ], 200),
        ]);

        $result = $this->walkScoreService->getWalkScore(
            '123 Main St, Seattle',
            47.6062,
            -122.3321
        );

        // Scores should be clamped to valid range
        $this->assertEquals(100, $result['walk_score']); // Clamped from 150
        $this->assertEquals(0, $result['transit_score']); // Clamped from -10
        $this->assertEquals(75, $result['bike_score']); // Unchanged (valid)
    }
}
