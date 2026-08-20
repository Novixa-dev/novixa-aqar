<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MaintenanceRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_maintenance_request');
    }

    public function view(AuthUser $authUser, MaintenanceRequest $maintenanceRequest): bool
    {
        return $authUser->can('view_maintenance_request');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_maintenance_request');
    }

    public function update(AuthUser $authUser, MaintenanceRequest $maintenanceRequest): bool
    {
        return $authUser->can('update_maintenance_request');
    }

    public function delete(AuthUser $authUser, MaintenanceRequest $maintenanceRequest): bool
    {
        return $authUser->can('delete_maintenance_request');
    }

    public function restore(AuthUser $authUser, MaintenanceRequest $maintenanceRequest): bool
    {
        return $authUser->can('restore_maintenance_request');
    }

    public function forceDelete(AuthUser $authUser, MaintenanceRequest $maintenanceRequest): bool
    {
        return $authUser->can('force_delete_maintenance_request');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_maintenance_request');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_maintenance_request');
    }

    public function replicate(AuthUser $authUser, MaintenanceRequest $maintenanceRequest): bool
    {
        return $authUser->can('replicate_maintenance_request');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_maintenance_request');
    }

}