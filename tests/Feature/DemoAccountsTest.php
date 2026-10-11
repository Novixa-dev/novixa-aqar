<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\DemoAccounts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_demo_account_is_on_the_novixa_domain(): void
    {
        $accounts = DemoAccounts::all();

        $this->assertSame(
            ['super_admin', 'staff', 'agent', 'landlord', 'tenant', 'buyer', 'seller', 'contractor'],
            array_keys($accounts),
        );

        foreach ($accounts as $role => $account) {
            $this->assertStringEndsWith('@novixa.dev', $account['email'], "{$role} is not on @novixa.dev");
        }

        $this->assertSame('admin@novixa.dev', DemoAccounts::email('super_admin'));
        $this->assertSame('seller@novixa.dev', DemoAccounts::email('seller'));
    }

    public function test_seeding_creates_one_working_login_per_role(): void
    {
        $this->seed(\Database\Seeders\DefaultTeamSeeder::class);
        $this->seed(\Database\Seeders\RolesSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        foreach (DemoAccounts::all() as $role => $account) {
            $user = User::where('email', $account['email'])->first();

            $this->assertNotNull($user, "no seeded user for {$account['email']}");
            $this->assertTrue($user->hasRole($role), "{$account['email']} lacks role {$role}");
            $this->assertTrue(Hash::check(DemoAccounts::PASSWORD, $user->password));
            $this->assertNotNull($user->current_team_id, "{$account['email']} has no current team");
        }

        // No demo login may be left on a domain this product does not own.
        $this->assertSame(0, User::where('email', 'like', '%@darhadhramaut.ye')->count());
        $this->assertSame(0, User::where('email', 'like', '%@aqar-demo.dexal.net')->count());
    }

    public function test_the_public_contact_email_is_on_the_novixa_domain(): void
    {
        // Fresh schema: the 2024 settings migration seeds the old default and
        // the 2026-10-11 migration must have moved it.
        $this->assertSame('info@novixa.dev', app(\App\Settings\GeneralSettings::class)->site_email);
        $this->assertSame(
            1,
            \Illuminate\Support\Facades\DB::table('settings')->where('group', 'general')->where('name', 'site_email')->count(),
            'site_email must be updated in place, not duplicated',
        );
    }
}
