<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PropertyCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_property_category');
    }

    public function view(AuthUser $authUser, PropertyCategory $propertyCategory): bool
    {
        return $authUser->can('view_property_category');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_property_category');
    }

    public function update(AuthUser $authUser, PropertyCategory $propertyCategory): bool
    {
        return $authUser->can('update_property_category');
    }

    public function delete(AuthUser $authUser, PropertyCategory $propertyCategory): bool
    {
        return $authUser->can('delete_property_category');
    }

    public function restore(AuthUser $authUser, PropertyCategory $propertyCategory): bool
    {
        return $authUser->can('restore_property_category');
    }

    public function forceDelete(AuthUser $authUser, PropertyCategory $propertyCategory): bool
    {
        return $authUser->can('force_delete_property_category');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_property_category');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_property_category');
    }

    public function replicate(AuthUser $authUser, PropertyCategory $propertyCategory): bool
    {
        return $authUser->can('replicate_property_category');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_property_category');
    }

}