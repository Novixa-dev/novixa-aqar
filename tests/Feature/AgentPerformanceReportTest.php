<?php

namespace Tests\Feature;

use App\Filament\Staff\Resources\AgentPerformances\Pages\ListAgentPerformance;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgentPerformanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_report_action_downloads_a_csv_file()
    {
        $team = Team::factory()->create();
        $staff = User::factory()->create();
        $staff->teams()->attach($team);
        $staff->forceFill(['current_team_id' => $team->id])->save();
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->assignRole($staffRole);

        $agentRole = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);
        $agent = User::factory()->create();
        $agent->assignRole($agentRole);

        Livewire::actingAs($staff)
            ->test(ListAgentPerformance::class)
            ->callAction('generateReport')
            ->assertFileDownloaded();
    }
}
