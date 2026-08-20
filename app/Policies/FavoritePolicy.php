<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Favorite;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavoritePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_favorite');
    }

    public function view(AuthUser $authUser, Favorite $favorite): bool
    {
        return $authUser->can('view_favorite');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_favorite');
    }

    public function update(AuthUser $authUser, Favorite $favorite): bool
    {
        return $authUser->can('update_favorite');
    }

    public function delete(AuthUser $authUser, Favorite $favorite): bool
    {
        return $authUser->can('delete_favorite');
    }

    public function restore(AuthUser $authUser, Favorite $favorite): bool
    {
        return $authUser->can('restore_favorite');
    }

    public function forceDelete(AuthUser $authUser, Favorite $favorite): bool
    {
        return $authUser->can('force_delete_favorite');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_favorite');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_favorite');
    }

    public function replicate(AuthUser $authUser, Favorite $favorite): bool
    {
        return $authUser->can('replicate_favorite');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_favorite');
    }

}