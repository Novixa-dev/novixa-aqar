<?php

use App\Jobs\SendEmailCampaign;
use App\Models\EmailCampaign;
use App\Models\Neighborhood;
use App\Services\NeighborhoodDataService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('portal-integrations:sync')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

// Each of these three commands now filters internally to only the teams whose
// own RightMoveSettings/OnTheMarketSettings/ZooplaSettings row matches the
// --frequency tier being run, and dispatches one sync job per team using that
// team's own API key (see the commands + RightMoveService/OnTheMarketService/
// ZooplaApiService::forSettings()). The previous Schedule::when() gates each
// checked only the *first* active settings row in the whole table, so with
// more than one team configured, only one team's frequency preference (and,
// separately, only one team's API key) ever actually took effect - the root
// cause of AQAR_PRE_RELEASE_AUDIT_REPORT.md's P0-5. No ->when() gate is
// needed here anymore: an empty match is a fast no-op inside the command.

Schedule::command('rightmove:sync-properties', ['--frequency' => 'hourly'])
    ->hourly()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('RightMove hourly sync failed'))
    ->onSuccess(fn () => Log::info('RightMove hourly sync completed'));

Schedule::command('rightmove:sync-properties', ['--frequency' => 'daily'])
    ->daily()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('RightMove daily sync failed'))
    ->onSuccess(fn () => Log::info('RightMove daily sync completed'));

Schedule::command('onthemarket:sync-properties', ['--frequency' => 'hourly'])
    ->hourly()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('OnTheMarket hourly sync failed'))
    ->onSuccess(fn () => Log::info('OnTheMarket hourly sync completed'));

Schedule::command('onthemarket:sync-properties', ['--frequency' => 'daily'])
    ->daily()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('OnTheMarket daily sync failed'))
    ->onSuccess(fn () => Log::info('OnTheMarket daily sync completed'));

Schedule::command('onthemarket:sync-properties', ['--frequency' => 'weekly'])
    ->weekly()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('OnTheMarket weekly sync failed'))
    ->onSuccess(fn () => Log::info('OnTheMarket weekly sync completed'));

Schedule::command('zoopla:sync-properties', ['--frequency' => 'hourly'])
    ->hourly()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('Zoopla hourly sync failed'))
    ->onSuccess(fn () => Log::info('Zoopla hourly sync completed'));

Schedule::command('zoopla:sync-properties', ['--frequency' => 'daily'])
    ->daily()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('Zoopla daily sync failed'))
    ->onSuccess(fn () => Log::info('Zoopla daily sync completed'));

Schedule::command('zoopla:sync-properties', ['--frequency' => 'weekly'])
    ->weekly()
    ->withoutOverlapping()
    ->onFailure(fn () => Log::error('Zoopla weekly sync failed'))
    ->onSuccess(fn () => Log::info('Zoopla weekly sync completed'));

Schedule::job(new \App\Jobs\LeaseRenewalReminder)->daily();

Schedule::call(function () {
    $campaigns = EmailCampaign::where('status', 'scheduled')
        ->where('scheduled_at', '<=', now())
        ->get();

    foreach ($campaigns as $campaign) {
        $campaign->update(['status' => 'sending']);
        dispatch(new SendEmailCampaign($campaign));
    }
})->everyMinute();

Schedule::call(function () {
    $neighborhoods = Neighborhood::all();
    $service = app(NeighborhoodDataService::class);
    foreach ($neighborhoods as $neighborhood) {
        $property = $neighborhood->properties()->first();
        if ($property && ($freshData = $service->getNeighborhoodData($property->postal_code))) {
            $neighborhood->update([
                'median_income' => $freshData['median_income'],
                'population' => $freshData['population'],
                'walk_score' => $freshData['walk_score'],
                'transit_score' => $freshData['transit_score'],
                'last_updated' => now(),
            ]);
        }
    }
})->daily();
