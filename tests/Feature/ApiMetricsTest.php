<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiMetricsTest extends TestCase
{
    public function test_metrics_endpoint_returns_200()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertStatus(200);
    }

    public function test_metrics_endpoint_returns_health_status()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertJsonStructure(['status', 'uptime', 'cache_status', 'db_status']);
    }

    public function test_metrics_endpoint_does_not_leak_sensitive_details()
    {
        // The endpoint is unauthenticated (k8s probes), so it must never
        // disclose app versions, environment names, or the PHP version.
        $response = $this->getJson('/api/metrics');

        $response->assertJsonMissing(['app_name']);
        $response->assertJsonMissing(['app_version']);
        $response->assertJsonMissing(['environment']);
        $response->assertJsonMissing(['php_version']);
    }

    public function test_metrics_endpoint_returns_cache_status()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertJsonStructure(['cache_status']);
    }

    public function test_metrics_endpoint_returns_db_status()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertJsonStructure(['db_status']);
    }

    public function test_metrics_endpoint_requires_no_authentication()
    {
        $response = $this->getJson('/api/metrics');

        $response->assertStatus(200);
    }

    public function test_metrics_endpoint_is_rate_limited()
    {
        // throttle:30,1 — the 31st request inside one minute must be rejected.
        for ($i = 0; $i < 30; $i++) {
            $this->getJson('/api/metrics')->assertStatus(200);
        }

        $this->getJson('/api/metrics')->assertStatus(429);
    }
}
