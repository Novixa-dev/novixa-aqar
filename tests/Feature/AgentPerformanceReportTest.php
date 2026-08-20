<?php

namespace Tests\Feature;

use App\Filament\Staff\Resources\AgentPerformances\Pages\ListAgentPerformance;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Filament\Facades\Filament;
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

        // Real permissions (and the staff role's grants) come from the same
        // seeders production uses - a bare Role::firstOrCreate() leaves the role
        // with zero permissions now that authorization is actually enforced
        // (see AQAR_PRE_RELEASE_AUDIT_REPORT.md P0-1).
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(RolesSeeder::class);

        $staff = User::factory()->create();
        $staff->teams()->attach($team);
        $staff->forceFill(['current_team_id' => $team->id])->save();
        $staffRole = Role::where('name', 'staff')->where('guard_name', 'web')->first();
        $staff->assignRole($staffRole);

        $agentRole = Role::where('name', 'agent')->where('guard_name', 'web')->first();
        $agent = User::factory()->create();
        $agent->assignRole($agentRole);

        // Without an actual HTTP request to /staff/..., Filament has no URL to
        // infer the active panel from and defaults to the first-registered one
        // (App) - the table then builds row-view links using App's route names,
        // which don't exist for this Staff-only resource. Set it explicitly, the
        // documented way to test a specific panel's resources outside a request.
        Filament::setCurrentPanel(Filament::getPanel('staff'));

        Livewire::actingAs($staff)
            ->test(ListAgentPerformance::class)
            ->callAction('generateReport')
            ->assertFileDownloaded();
    }
}
