<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            Branch::create([
                'name' => $team->name . ' Branch',
                'address' => 'شارع الملكة أروى، حي فوه، المكلا، حضرموت، اليمن',
                'phone_number' => '+967 776 248 526',
                'team_id' => $team->id,
            ]);
        }
    }
}