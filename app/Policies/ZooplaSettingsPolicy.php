<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ZooplaSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class ZooplaSettingsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_zoopla_settings');
    }

    public function view(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('view_zoopla_settings');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_zoopla_settings');
    }

    public function update(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('update_zoopla_settings');
    }

    public function delete(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('delete_zoopla_settings');
    }

    public function restore(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('restore_zoopla_settings');
    }

    public function forceDelete(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('force_delete_zoopla_settings');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_zoopla_settings');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_zoopla_settings');
    }

    public function replicate(AuthUser $authUser, ZooplaSettings $zooplaSettings): bool
    {
        return $authUser->can('replicate_zoopla_settings');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_zoopla_settings');
    }

}