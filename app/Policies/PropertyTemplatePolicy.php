<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PropertyTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_property_template');
    }

    public function view(AuthUser $authUser, PropertyTemplate $propertyTemplate): bool
    {
        return $authUser->can('view_property_template');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_property_template');
    }

    public function update(AuthUser $authUser, PropertyTemplate $propertyTemplate): bool
    {
        return $authUser->can('update_property_template');
    }

    public function delete(AuthUser $authUser, PropertyTemplate $propertyTemplate): bool
    {
        return $authUser->can('delete_property_template');
    }

    public function restore(AuthUser $authUser, PropertyTemplate $propertyTemplate): bool
    {
        return $authUser->can('restore_property_template');
    }

    public function forceDelete(AuthUser $authUser, PropertyTemplate $propertyTemplate): bool
    {
        return $authUser->can('force_delete_property_template');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_property_template');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_property_template');
    }

    public function replicate(AuthUser $authUser, PropertyTemplate $propertyTemplate): bool
    {
        return $authUser->can('replicate_property_template');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_property_template');
    }

}