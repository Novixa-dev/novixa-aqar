<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Review;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_review');
    }

    public function view(AuthUser $authUser, Review $review): bool
    {
        return $authUser->can('view_review');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_review');
    }

    public function update(AuthUser $authUser, Review $review): bool
    {
        return $authUser->can('update_review');
    }

    public function delete(AuthUser $authUser, Review $review): bool
    {
        return $authUser->can('delete_review');
    }

    public function restore(AuthUser $authUser, Review $review): bool
    {
        return $authUser->can('restore_review');
    }

    public function forceDelete(AuthUser $authUser, Review $review): bool
    {
        return $authUser->can('force_delete_review');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_review');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_review');
    }

    public function replicate(AuthUser $authUser, Review $review): bool
    {
        return $authUser->can('replicate_review');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_review');
    }

}