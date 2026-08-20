<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PropertyFeature;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyFeaturePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_property_feature');
    }

    public function view(AuthUser $authUser, PropertyFeature $propertyFeature): bool
    {
        return $authUser->can('view_property_feature');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_property_feature');
    }

    public function update(AuthUser $authUser, PropertyFeature $propertyFeature): bool
    {
        return $authUser->can('update_property_feature');
    }

    public function delete(AuthUser $authUser, PropertyFeature $propertyFeature): bool
    {
        return $authUser->can('delete_property_feature');
    }

    public function restore(AuthUser $authUser, PropertyFeature $propertyFeature): bool
    {
        return $authUser->can('restore_property_feature');
    }

    public function forceDelete(AuthUser $authUser, PropertyFeature $propertyFeature): bool
    {
        return $authUser->can('force_delete_property_feature');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_property_feature');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_property_feature');
    }

    public function replicate(AuthUser $authUser, PropertyFeature $propertyFeature): bool
    {
        return $authUser->can('replicate_property_feature');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_property_feature');
    }

}