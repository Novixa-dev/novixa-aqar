<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Lead;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_lead');
    }

    public function view(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('view_lead');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_lead');
    }

    public function update(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('update_lead');
    }

    public function delete(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('delete_lead');
    }

    public function restore(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('restore_lead');
    }

    public function forceDelete(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('force_delete_lead');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_lead');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_lead');
    }

    public function replicate(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('replicate_lead');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_lead');
    }

}