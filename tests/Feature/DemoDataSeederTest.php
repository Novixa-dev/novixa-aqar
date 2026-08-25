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

    public function test_demo_workflow_seeder_populates_every_persona_surface()
    {
        // Same ordering contract as DatabaseSeeder: permissions/roles precede
        // any seeder that assigns roles to users.
        $this->seed(\Database\Seeders\DefaultTeamSeeder::class);
        $this->seed(\Database\Seeders\RolesSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\PropertyCategorySeeder::class);
        $this->seed(\Database\Seeders\PropertySeeder::class);
        $this->seed(\Database\Seeders\DemoWorkflowSeeder::class);

        // Every workflow table the role dashboards surface must have rows.
        foreach (['vendors', 'tenants', 'leases', 'payments', 'maintenance_requests', 'rental_applications', 'bookings'] as $table) {
            $this->assertGreaterThan(0, DB::table($table)->count(), "table {$table} is empty after seeding");
        }

        // The landlord persona owns rental inventory (their panel is scoped
        // by properties.user_id). PropertySeeder writes the legacy 'For
        // Rent' status, normalized by Property::setStatusAttribute() to
        // the canonical 'for_rent' on write — assert against that.
        $landlordId = DB::table('users')->where('email', 'landlord@darhadhramaut.ye')->value('id');
        $this->assertGreaterThan(
            0,
            DB::table('properties')->where('user_id', $landlordId)->where('status', 'for_rent')->count(),
            'landlord persona owns no rentals'
        );

        // The contractor persona has an assigned work order (contractor panel
        // scopes by the contractors-table record carrying their email).
        $contractorEmail = DB::table('users')->where('email', 'contractor@darhadhramaut.ye')->value('email');
        $contractorId = DB::table('contractors')->where('email', $contractorEmail)->value('id');
        $this->assertNotNull($contractorId, 'no contractors-table record for the contractor persona');
        $this->assertGreaterThan(
            0,
            DB::table('maintenance_requests')->where('contractor_id', $contractorId)->count(),
            'contractor persona has no assigned work orders'
        );
    }
}
