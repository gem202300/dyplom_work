<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AttractionPhoto;
use App\Enums\Auth\PermissionType;

class AttractionPhotoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ATTRACTION_ACCESS->value);
    }

    public function view(User $user, AttractionPhoto $photo): bool
    {
        return $user->can(PermissionType::ATTRACTION_ACCESS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ATTRACTION_MANAGE->value);
    }

    public function update(User $user, AttractionPhoto $photo): bool
    {
        return $user->can(PermissionType::ATTRACTION_MANAGE->value);
    }

    public function delete(User $user, AttractionPhoto $photo): bool
    {
        return $user->can(PermissionType::ATTRACTION_MANAGE->value);

    }

    public function restore(User $user, AttractionPhoto $photo): bool
    {
        return false;
    }

    public function forceDelete(User $user, AttractionPhoto $photo): bool
    {
        return false;
    }
}
