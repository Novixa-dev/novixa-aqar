<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KeyLocation;
use Illuminate\Auth\Access\HandlesAuthorization;

class KeyLocationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_key_location');
    }

    public function view(AuthUser $authUser, KeyLocation $keyLocation): bool
    {
        return $authUser->can('view_key_location');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_key_location');
    }

    public function update(AuthUser $authUser, KeyLocation $keyLocation): bool
    {
        return $authUser->can('update_key_location');
    }

    public function delete(AuthUser $authUser, KeyLocation $keyLocation): bool
    {
        return $authUser->can('delete_key_location');
    }

    public function restore(AuthUser $authUser, KeyLocation $keyLocation): bool
    {
        return $authUser->can('restore_key_location');
    }

    public function forceDelete(AuthUser $authUser, KeyLocation $keyLocation): bool
    {
        return $authUser->can('force_delete_key_location');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_key_location');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_key_location');
    }

    public function replicate(AuthUser $authUser, KeyLocation $keyLocation): bool
    {
        return $authUser->can('replicate_key_location');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_key_location');
    }

}