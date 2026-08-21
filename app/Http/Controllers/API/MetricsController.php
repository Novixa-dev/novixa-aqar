<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MetricsController extends Controller
{
    public function index(): JsonResponse
    {
        // Deliberately minimal: this endpoint is unauthenticated (k8s probes /
        // uptime checks), so it must not disclose app versions, environment
        // names, or PHP version. Health booleans only.
        $cacheStatus = $this->getCacheStatus();
        $dbStatus = $this->getDatabaseStatus();

        return response()->json([
            'status' => ($cacheStatus === 'healthy' && $dbStatus === 'healthy') ? 'ok' : 'degraded',
            'uptime' => $this->getUptime(),
            'cache_status' => $cacheStatus,
            'db_status' => $dbStatus,
        ]);
    }

    private function getUptime(): string
    {
        $start = Cache::remember('app_start_time', 86400, fn() => now()->toIso8601String());
        return $start;
    }

    private function getCacheStatus(): string
    {
        try {
            Cache::put('_health_check', true, 10);
            return Cache::get('_health_check') ? 'healthy' : 'degraded';
        } catch (\Exception) {
            return 'unhealthy';
        }
    }

    private function getDatabaseStatus(): string
    {
        try {
            DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception) {
            return 'unhealthy';
        }
    }
}
