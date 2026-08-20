<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LeaseAgreement;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaseAgreementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_lease_agreement');
    }

    public function view(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('view_lease_agreement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_lease_agreement');
    }

    public function update(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('update_lease_agreement');
    }

    public function delete(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('delete_lease_agreement');
    }

    public function restore(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('restore_lease_agreement');
    }

    public function forceDelete(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('force_delete_lease_agreement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_lease_agreement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_lease_agreement');
    }

    public function replicate(AuthUser $authUser, LeaseAgreement $leaseAgreement): bool
    {
        return $authUser->can('replicate_lease_agreement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_lease_agreement');
    }

}