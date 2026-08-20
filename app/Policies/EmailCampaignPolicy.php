<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EmailCampaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailCampaignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_email_campaign');
    }

    public function view(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('view_email_campaign');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_email_campaign');
    }

    public function update(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('update_email_campaign');
    }

    public function delete(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('delete_email_campaign');
    }

    public function restore(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('restore_email_campaign');
    }

    public function forceDelete(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('force_delete_email_campaign');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_email_campaign');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_email_campaign');
    }

    public function replicate(AuthUser $authUser, EmailCampaign $emailCampaign): bool
    {
        return $authUser->can('replicate_email_campaign');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_email_campaign');
    }

}