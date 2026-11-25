<?php

namespace Tests\Feature;

use App\Filament\Auth\RequestPasswordReset;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
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

    public function test_a_mail_failure_shows_an_error_instead_of_a_500(): void
    {
        $this->useAFailingMailer();
        User::factory()->create(['email' => 'tenant@novixa.dev']);

        $this->from('/forgot-password')
            ->post('/forgot-password', ['email' => 'tenant@novixa.dev'])
            ->assertRedirect('/forgot-password')
            ->assertSessionHasErrors(['email' => __('We could not send the reset email right now. Please try again later or contact us.')])
            ->assertSessionHasInput('email', 'tenant@novixa.dev');
    }

    public function test_a_panel_reset_mail_failure_shows_a_notification_instead_of_an_error(): void
    {
        $this->useAFailingMailer();
        // A roleless user can access the "app" panel, so Filament really
        // tries to mail them (it silently skips users who cannot).
        User::factory()->create(['email' => 'tenant@novixa.dev']);
        Filament::setCurrentPanel(Filament::getPanel('app'));

        Livewire::test(RequestPasswordReset::class)
            ->set('data.email', 'tenant@novixa.dev')
            ->call('request')
            ->assertNotified(__('We could not send the reset email right now. Please try again later or contact us.'));
    }

    private function useAFailingMailer(): void
    {
        Mail::extend('broken', fn () => new class extends AbstractTransport
        {
            protected function doSend(SentMessage $message): void
            {
                throw new TransportException('SMTP is down');
            }

            public function __toString(): string
            {
                return 'broken://';
            }
        });
        config(['mail.mailers.broken' => ['transport' => 'broken'], 'mail.default' => 'broken']);
    }
}
