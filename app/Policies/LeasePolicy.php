<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Lease;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_lease');
    }

    public function view(AuthUser $authUser, Lease $lease): bool
    {
        return $authUser->can('view_lease');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_lease');
    }

    public function update(AuthUser $authUser, Lease $lease): bool
    {
        return $authUser->can('update_lease');
    }

    public function delete(AuthUser $authUser, Lease $lease): bool
    {
        return $authUser->can('delete_lease');
    }

    public function restore(AuthUser $authUser, Lease $lease): bool
    {
        return $authUser->can('restore_lease');
    }

    public function forceDelete(AuthUser $authUser, Lease $lease): bool
    {
        return $authUser->can('force_delete_lease');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_lease');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_lease');
    }

    public function replicate(AuthUser $authUser, Lease $lease): bool
    {
        return $authUser->can('replicate_lease');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_lease');
    }

}