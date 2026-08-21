<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RentalApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentalApplicationPolicy
{
    use HandlesAuthorization;

    public function before(AuthUser $authUser): ?bool
    {
        return $authUser->hasAnyRole(['admin', 'super_admin']) ? true : null;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_rental_application');
    }

    public function view(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('view_rental_application') && $this->canAccessApplication($authUser, $rentalApplication);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_rental_application');
    }

    public function update(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('update_rental_application') && $this->canAccessApplication($authUser, $rentalApplication);
    }

    public function delete(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('delete_rental_application');
    }

    public function restore(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('restore_rental_application');
    }

    public function forceDelete(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('force_delete_rental_application');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_rental_application');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_rental_application');
    }

    public function replicate(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('replicate_rental_application');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_rental_application');
    }

    /**
     * Agency roles (staff/agent) operate across all properties; everyone else
     * (landlords) may only access applications on properties they own or that
     * belong to one of their teams.
     */
    protected function canAccessApplication(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        if ($authUser->hasRole('staff') || $authUser->hasRole('agent')) {
            return true;
        }

        $property = $rentalApplication->property;

        if ($property === null) {
            return false;
        }

        return $property->user_id === $authUser->id
            || ($property->team_id !== null && $authUser->teams()->whereKey($property->team_id)->exists());
    }

}