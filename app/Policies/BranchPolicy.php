<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Branch;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_branch');
    }

    public function view(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('view_branch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_branch');
    }

    public function update(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('update_branch');
    }

    public function delete(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('delete_branch');
    }

    public function restore(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('restore_branch');
    }

    public function forceDelete(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('force_delete_branch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_branch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_branch');
    }

    public function replicate(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('replicate_branch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_branch');
    }

}