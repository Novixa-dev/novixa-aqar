<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RentalApplication;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentalApplicationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_rental_application');
    }

    public function view(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('view_rental_application');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_rental_application');
    }

    public function update(AuthUser $authUser, RentalApplication $rentalApplication): bool
    {
        return $authUser->can('update_rental_application');
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

}