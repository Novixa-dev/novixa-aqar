<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // PropertySeeder generates media-library image conversions (resize +
        // watermark) for the bundled demo gallery photos; PHP's stock CLI
        // default (128M) reliably exhausts on this, aborting the whole seed
        // run. Raise it for the duration of seeding only — this never runs
        // in a request lifecycle, so it can't affect production memory use.
        // ini_set() can fail under some SAPI/php.ini `disable_functions`
        // policies; if it does, seeding still fails the same way it did
        // before this fix, just without the raised ceiling.
        if (function_exists('ini_set')) {
            @ini_set('memory_limit', '512M');
        }

        $this->call([
            SiteSettingsSeeder::class,
            PermissionsSeeder::class,
            RolesSeeder::class,
            DefaultTeamSeeder::class,
            UserSeeder::class,
            PropertyCategorySeeder::class,
            NeighborhoodSeeder::class,
            PropertySeeder::class,
            LeadAndBuyerSeeder::class,
            AppointmentTypeSeeder::class,
            VirtualTourAppointmentTypeSeeder::class,
            MenuSeeder::class,
            DocumentTemplateSeeder::class,
            ComponentSettingsSeeder::class,
            NewsSeeder::class,
            DemoWorkflowSeeder::class,
        ]);
    }
}
