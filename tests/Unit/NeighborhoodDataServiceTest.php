<?php

namespace Tests\Unit;

use App\Services\NeighborhoodDataService;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class NeighborhoodDataServiceTest extends TestCase
{
    // The service makes no request at all without a key (see bde9126), so
    // the paths that exercise the HTTP call configure one.
    private function configureKey(): void
    {
        config([
            'services.neighborhood_data.api_key' => 'test-key',
            'services.neighborhood_data.base_uri' => 'https://neighborhood.test',
        ]);
    }

    public function test_get_neighborhood_data_returns_null_without_calling_out_when_no_key_is_configured()
    {
        config(['services.neighborhood_data.api_key' => null]);
        Http::fake();

        $data = (new NeighborhoodDataService())->getNeighborhoodData('12345');

        $this->assertNull($data);
        Http::assertNothingSent();
    }

    public function test_get_neighborhood_data_returns_data_on_success()
    {
        $this->configureKey();

        // Mock the HTTP response
        Http::fake([
            '*/neighborhood/*' => Http::response([
                'median_income' => 80000,
                'population' => 30000,
                'walk_score' => 90,
                'transit_score' => 75,
            ], 200)
        ]);

        $service = new NeighborhoodDataService();
        $data = $service->getNeighborhoodData('12345');

        $this->assertNotNull($data);
        $this->assertEquals(80000, $data['median_income']);
        $this->assertEquals(30000, $data['population']);
        $this->assertEquals(90, $data['walk_score']);
        $this->assertEquals(75, $data['transit_score']);
    }

    public function test_get_neighborhood_data_returns_null_on_failure()
    {
        $this->configureKey();

        // Mock a failed HTTP response
        Http::fake([
            '*/neighborhood/*' => Http::response([], 500)
        ]);

        $service = new NeighborhoodDataService();
        $data = $service->getNeighborhoodData('12345');

        $this->assertNull($data);
    }

    public function test_get_neighborhood_data_handles_exceptions()
    {
        // Mock an exception by not faking the HTTP call
        // This will cause a connection error
        Http::fake([
            '*/neighborhood/*' => function () {
                throw new \Exception('Connection failed');
            }
        ]);

        $service = new NeighborhoodDataService();
        $data = $service->getNeighborhoodData('12345');

        $this->assertNull($data);
    }
}
