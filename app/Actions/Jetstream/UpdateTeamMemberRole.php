<?php

namespace App\Actions\Jetstream;

use App\Enums\AgencyRole;
use App\Models\Team;
use App\Models\User;
use App\Services\AgencyPermissionService;
use App\Services\AgencyRoleAuditService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Events\TeamMemberUpdated;

class UpdateTeamMemberRole extends \Laravel\Jetstream\Actions\UpdateTeamMemberRole
{
    public function __construct(
        private readonly AgencyPermissionService $permissions,
        private readonly AgencyRoleAuditService $roleAudits,
    ) {}

    /**
     * Parameters stay untyped to match Jetstream's parent signature: narrowing
     * them made the class fatal to load ("must be compatible"), so changing a
     * member's role crashed before any of this ran.
     *
     * @param  User  $user
     * @param  Team  $team
     * @param  int|string  $teamMemberId
     */
    public function update($user, $team, $teamMemberId, string $role): void
    {
        $teamMemberId = (int) $teamMemberId;

        Validator::make(['role' => $role], [
            'role' => ['required', Rule::in(AgencyRole::assignable())],
        ])->validate();

        abort_if($team->user_id === $teamMemberId, 422, 'The organisation owner role cannot be changed.');
        $member = $team->users()->whereKey($teamMemberId)->firstOrFail();
        $actorRole = $this->permissions->roleFor($user, $team);
        $oldRole = AgencyRole::tryFrom($member->membership->role ?? '') ?? AgencyRole::Member;
        $newRole = AgencyRole::from($role);

        if (! $actorRole->canManage($oldRole) || ! $actorRole->canManage($newRole)) {
            throw new AuthorizationException('You cannot manage or assign this organisation role.');
        }

        DB::transaction(function () use ($user, $team, $teamMemberId, $oldRole, $newRole): void {
            $team->users()->updateExistingPivot($teamMemberId, ['role' => $newRole->value]);
            $request = Request::createFrom(request());
            $request->setUserResolver(fn () => $user);
            $this->roleAudits->record(
                $request,
                $team,
                $teamMemberId,
                'role_changed',
                $oldRole->value,
                $newRole->value,
            );
        });

        TeamMemberUpdated::dispatch($team->fresh(), $member->fresh());
    }
}
