<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tenant;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_tenant');
    }

    public function view(AuthUser $authUser, Tenant $tenant): bool
    {
        return $authUser->can('view_tenant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_tenant');
    }

    public function update(AuthUser $authUser, Tenant $tenant): bool
    {
        return $authUser->can('update_tenant');
    }

    public function delete(AuthUser $authUser, Tenant $tenant): bool
    {
        return $authUser->can('delete_tenant');
    }

    public function restore(AuthUser $authUser, Tenant $tenant): bool
    {
        return $authUser->can('restore_tenant');
    }

    public function forceDelete(AuthUser $authUser, Tenant $tenant): bool
    {
        return $authUser->can('force_delete_tenant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_tenant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_tenant');
    }

    public function replicate(AuthUser $authUser, Tenant $tenant): bool
    {
        return $authUser->can('replicate_tenant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_tenant');
    }

}