<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Actions\UpdateTeamMemberRole;
use Tests\TestCase;

class UpdateTeamMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_owner_can_change_a_members_role(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $member = User::factory()->create();
        $team->users()->attach($member, ['role' => 'member']);

        // Resolved the way Jetstream resolves it: through the container
        // binding to App\Actions\Jetstream\UpdateTeamMemberRole.
        app(UpdateTeamMemberRole::class)->update($owner, $team, (string) $member->id, 'editor');

        $this->assertSame('editor', $team->users()->whereKey($member->id)->first()->membership->role);
    }

    public function test_the_owner_role_itself_cannot_be_changed(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        app(UpdateTeamMemberRole::class)->update($owner, $team, $owner->id, 'admin');
    }
}
