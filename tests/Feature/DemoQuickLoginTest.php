<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\DemoAccounts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DemoQuickLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_lists_every_demo_persona_when_enabled(): void
    {
        config(['demo.quick_login' => true]);

        $response = $this->get('/login')->assertOk();

        foreach (DemoAccounts::all() as $account) {
            $response->assertSee($account['email']);
        }
        $response->assertSee('demo-accounts-toggle', false);
    }

    public function test_login_page_hides_demo_personas_by_default(): void
    {
        config(['demo.quick_login' => false]);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('demo-accounts-toggle', false)
            ->assertDontSee(DemoAccounts::email('super_admin'));
    }

    public function test_demo_account_password_and_email_are_locked_in_demo_mode(): void
    {
        config(['demo.quick_login' => true]);
        $user = User::factory()->create(['email' => DemoAccounts::email('agent')]);

        $this->expectException(ValidationException::class);
        $user->update(['password' => Hash::make('changed-by-a-visitor')]);
    }

    public function test_demo_account_can_still_update_its_name_in_demo_mode(): void
    {
        config(['demo.quick_login' => true]);
        $user = User::factory()->create(['email' => DemoAccounts::email('tenant')]);

        $user->update(['name' => 'Renamed']);

        $this->assertSame('Renamed', $user->fresh()->name);
    }

    public function test_other_accounts_are_unaffected_in_demo_mode(): void
    {
        config(['demo.quick_login' => true]);
        $user = User::factory()->create(['email' => 'someone@example.com']);

        $user->update(['password' => Hash::make('new-password')]);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
