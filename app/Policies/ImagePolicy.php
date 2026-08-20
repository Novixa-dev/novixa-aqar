<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;

class ImagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_image');
    }

    public function create(User $user): bool
    {
        return $user->can('create_image');
    }

    public function update(User $user, Image $image): bool
    {
        return $user->can('update', $image->property);
    }

    public function delete(User $user, Image $image): bool
    {
        return $user->can('update', $image->property);
    }
}
