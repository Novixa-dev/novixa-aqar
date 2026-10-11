<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PublicPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_page_offers_password_recovery(): void
    {
        $this->get('/login')->assertOk()->assertSee(route('password.request'), false);
        $this->get('/forgot-password')->assertOk();
    }

    public function test_a_user_can_reset_a_forgotten_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'tenant@novixa.dev']);

        $this->post('/forgot-password', ['email' => 'tenant@novixa.dev'])->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->get('/reset-password/'.$notification->token)->assertOk();

            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'New-Passw0rd!2026',
                'password_confirmation' => 'New-Passw0rd!2026',
            ])->assertSessionHasNoErrors();

            return Hash::check('New-Passw0rd!2026', $user->fresh()->password);
        });
    }
}
