<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Support\DemoAccounts;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException(
                'UserSeeder creates demo accounts with default passwords and must never run in production. '
                . 'Create the first admin via `php artisan make:filament-user` instead.'
            );
        }

        $team = Team::first();

        // One persona per role, all defined in App\Support\DemoAccounts
        // (emails @novixa.dev, shared demo password).
        foreach (DemoAccounts::all() as $roleName => $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make(DemoAccounts::PASSWORD),
                    'email_verified_at' => now(),
                ]
            );

            if ($team) {
                $user->teams()->syncWithoutDetaching([$team->id]);
                // switchTeam() (not a raw current_team_id assignment) also
                // verifies membership - without this, current_team_id stays
                // null and every team-scoped feature (V1 API's
                // TenantCrudController, any ->current_team_id lookup) throws
                // "select an organisation" for every seeded demo account.
                if (is_null($user->current_team_id)) {
                    $user->switchTeam($team);
                }
            }

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        echo "Seeded demo users (@".DemoAccounts::DOMAIN."). Default password for all users: ".DemoAccounts::PASSWORD."\n";
    }
}
