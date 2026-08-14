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
        $this->call([
//            SiteSettingsSeeder::class,
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
            // Add other seeders here if needed
        ]);
    }
}
