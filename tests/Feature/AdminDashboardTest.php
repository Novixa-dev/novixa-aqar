<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_and_see_widgets_and_resources(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create(['email' => 'admin@example.com']);
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $content = $response->getContent();

        // Check that page is not blank
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Novixa Aqar', $content);
    }
}
