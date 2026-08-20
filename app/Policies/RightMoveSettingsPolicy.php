<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RightMoveSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class RightMoveSettingsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_right_move_settings');
    }

    public function view(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('view_right_move_settings');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_right_move_settings');
    }

    public function update(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('update_right_move_settings');
    }

    public function delete(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('delete_right_move_settings');
    }

    public function restore(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('restore_right_move_settings');
    }

    public function forceDelete(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('force_delete_right_move_settings');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_right_move_settings');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_right_move_settings');
    }

    public function replicate(AuthUser $authUser, RightMoveSettings $rightMoveSettings): bool
    {
        return $authUser->can('replicate_right_move_settings');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_right_move_settings');
    }

}