<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(string $role = 'buyer'): User
    {
        $user = User::factory()->create([
            'email' => "{$role}.flow@example.test",
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $team = Team::factory()->create();
        $user->teams()->attach($team);
        $user->switchTeam($team);

        return $user;
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->verifiedUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $user = $this->verifiedUser();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_logout_terminates_the_session(): void
    {
        $user = $this->verifiedUser();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }

    public function test_registration_route_loads(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_password_reset_request_page_loads_on_role_panels(): void
    {
        // App-level Fortify reset views were never registered (so /forgot-password
        // is intentionally absent); recovery lives on the Filament panels.
        foreach (['admin', 'staff', 'agent', 'buyer', 'seller', 'landlord', 'tenant', 'contractor'] as $panel) {
            $this->get("/{$panel}/password-reset/request")->assertOk();
        }
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        config(['fortify.limiters.login' => 'login']);
        $user = $this->verifiedUser();

        foreach (range(1, 6) as $attempt) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // The throttle:login limiter is defined in FortifyServiceProvider and
        // applied in routes/web.php; attempt #6 must be rejected with 429.
        $response->assertStatus(429);
    }
}
