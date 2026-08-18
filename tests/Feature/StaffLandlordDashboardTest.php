<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffLandlordDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_loads_without_fatal_error()
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $user->assignRole(Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']));
        Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);

        $response = $this->actingAs($user)->followingRedirects()->get('/staff');

        $response->assertStatus(200);
    }

    public function test_landlord_dashboard_loads_without_fatal_error()
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $user->teams()->attach($team);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $user->assignRole(Role::firstOrCreate(['name' => 'landlord', 'guard_name' => 'web']));

        $response = $this->actingAs($user)->followingRedirects()->get('/landlord');

        $response->assertStatus(200);
    }
}
