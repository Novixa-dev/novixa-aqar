<?php

namespace Database\Seeders;

use App\Models\CommunityEvent;
use App\Models\Property;
use Illuminate\Database\Seeder;

class CommunityEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Factory-generated events have no natural unique key to match on, so
        // running this unconditionally added another 30 events (20 general +
        // 2 each for 5 properties) on every `db:seed`. Staging re-seeds on
        // deploy, so the demo event list grew without bound. Same guard style
        // as PropertySeeder: create the demo set exactly once.
        if (CommunityEvent::exists()) {
            return;
        }

        // Create 20 general community events
        CommunityEvent::factory(20)->create();

        // Create property-specific events for the first 5 properties (if they exist)
        $properties = Property::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(5)
            ->get();

        foreach ($properties as $property) {
            CommunityEvent::factory(2)->forProperty($property)->create();
        }
    }
}
