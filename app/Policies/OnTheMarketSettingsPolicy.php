<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OnTheMarketSettings;
use Illuminate\Auth\Access\HandlesAuthorization;

class OnTheMarketSettingsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_on_the_market_settings');
    }

    public function view(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('view_on_the_market_settings');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_on_the_market_settings');
    }

    public function update(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('update_on_the_market_settings');
    }

    public function delete(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('delete_on_the_market_settings');
    }

    public function restore(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('restore_on_the_market_settings');
    }

    public function forceDelete(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('force_delete_on_the_market_settings');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_on_the_market_settings');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_on_the_market_settings');
    }

    public function replicate(AuthUser $authUser, OnTheMarketSettings $onTheMarketSettings): bool
    {
        return $authUser->can('replicate_on_the_market_settings');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_on_the_market_settings');
    }

}