<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $adminPassword = 'password';
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@darhadhramaut.ye'],
            [
                'name' => 'Admin User',
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]
        );

        $team = Team::first();
        if ($team) {
            $adminUser->teams()->syncWithoutDetaching([$team->id]);
            // switchTeam() (not a raw current_team_id assignment) also
            // verifies membership - without this, current_team_id stays null
            // and every team-scoped feature (V1 API's TenantCrudController,
            // any ->current_team_id lookup) throws "select an organisation"
            // for every seeded demo account. Previously unset for all 8 roles.
            if (is_null($adminUser->current_team_id)) {
                $adminUser->switchTeam($team);
            }
        }

        $role = Role::where('name', 'super_admin')->first();
        if ($role) {
            $adminUser->assignRole($role);
        }

        // Create standard test users for other roles
        $roles = ['staff', 'agent', 'buyer', 'seller', 'landlord', 'tenant', 'contractor'];
        foreach ($roles as $roleName) {
            $user = User::firstOrCreate(
                ['email' => "{$roleName}@darhadhramaut.ye"],
                [
                    'name' => ucfirst($roleName) . ' User',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            if ($team) {
                $user->teams()->syncWithoutDetaching([$team->id]);
                if (is_null($user->current_team_id)) {
                    $user->switchTeam($team);
                }
            }
            $r = Role::where('name', $roleName)->first();
            if ($r) {
                $user->assignRole($r);
            }
        }

        echo "Seeded users successfully. Default password for all users: password\n";
    }
}
