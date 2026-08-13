<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeders_run_cleanly()
    {
        $this->seed(\Database\Seeders\DefaultTeamSeeder::class);
        $this->seed(\Database\Seeders\RolesSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\PropertyCategorySeeder::class);
        $this->seed(\Database\Seeders\NeighborhoodSeeder::class);
        $this->seed(\Database\Seeders\PropertySeeder::class);
        $this->seed(\Database\Seeders\LeadAndBuyerSeeder::class);

        $this->assertGreaterThan(0, DB::table('properties')->count());
        $this->assertGreaterThan(0, DB::table('neighborhoods')->count());
        $this->assertGreaterThan(0, DB::table('leads')->count());
        $this->assertGreaterThan(0, DB::table('buyers')->count());
    }
}
