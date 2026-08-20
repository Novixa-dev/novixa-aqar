<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UtilityPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class UtilityPaymentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_utility_payment');
    }

    public function view(AuthUser $authUser, UtilityPayment $utilityPayment): bool
    {
        return $authUser->can('view_utility_payment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_utility_payment');
    }

    public function update(AuthUser $authUser, UtilityPayment $utilityPayment): bool
    {
        return $authUser->can('update_utility_payment');
    }

    public function delete(AuthUser $authUser, UtilityPayment $utilityPayment): bool
    {
        return $authUser->can('delete_utility_payment');
    }

    public function restore(AuthUser $authUser, UtilityPayment $utilityPayment): bool
    {
        return $authUser->can('restore_utility_payment');
    }

    public function forceDelete(AuthUser $authUser, UtilityPayment $utilityPayment): bool
    {
        return $authUser->can('force_delete_utility_payment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_utility_payment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_utility_payment');
    }

    public function replicate(AuthUser $authUser, UtilityPayment $utilityPayment): bool
    {
        return $authUser->can('replicate_utility_payment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_utility_payment');
    }

}