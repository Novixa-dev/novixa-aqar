<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EnergyConsumption;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnergyConsumptionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_energy_consumption');
    }

    public function view(AuthUser $authUser, EnergyConsumption $energyConsumption): bool
    {
        return $authUser->can('view_energy_consumption');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_energy_consumption');
    }

    public function update(AuthUser $authUser, EnergyConsumption $energyConsumption): bool
    {
        return $authUser->can('update_energy_consumption');
    }

    public function delete(AuthUser $authUser, EnergyConsumption $energyConsumption): bool
    {
        return $authUser->can('delete_energy_consumption');
    }

    public function restore(AuthUser $authUser, EnergyConsumption $energyConsumption): bool
    {
        return $authUser->can('restore_energy_consumption');
    }

    public function forceDelete(AuthUser $authUser, EnergyConsumption $energyConsumption): bool
    {
        return $authUser->can('force_delete_energy_consumption');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_energy_consumption');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_energy_consumption');
    }

    public function replicate(AuthUser $authUser, EnergyConsumption $energyConsumption): bool
    {
        return $authUser->can('replicate_energy_consumption');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_energy_consumption');
    }

}