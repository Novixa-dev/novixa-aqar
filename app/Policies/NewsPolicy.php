<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\News;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_news');
    }

    public function view(AuthUser $authUser, News $news): bool
    {
        return $authUser->can('view_news');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_news');
    }

    public function update(AuthUser $authUser, News $news): bool
    {
        return $authUser->can('update_news');
    }

    public function delete(AuthUser $authUser, News $news): bool
    {
        return $authUser->can('delete_news');
    }

    public function restore(AuthUser $authUser, News $news): bool
    {
        return $authUser->can('restore_news');
    }

    public function forceDelete(AuthUser $authUser, News $news): bool
    {
        return $authUser->can('force_delete_news');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_news');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_news');
    }

    public function replicate(AuthUser $authUser, News $news): bool
    {
        return $authUser->can('replicate_news');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_news');
    }

}