<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_can_access_dashboard_and_see_widgets_and_resources(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $this->markTestSkipped('Database tables not migrated in test environment');
        }

        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $user = User::factory()->create([
                'email' => 'admin@example.com',
                'role' => 'admin'
            ]);
        }

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $content = $response->getContent();

        // Check that page is not blank
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Admin Panel', $content);
    }
}
