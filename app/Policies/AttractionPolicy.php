<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attraction;
use App\Enums\Auth\PermissionType;

class AttractionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ATTRACTION_ACCESS->value);
    }

    public function view(User $user, Attraction $attraction): bool
    {
        return $user->can(PermissionType::ATTRACTION_ACCESS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ATTRACTION_MANAGE->value);
    }

    public function update(User $user, Attraction $attraction): bool
    {
        return $user->can(PermissionType::ATTRACTION_MANAGE->value);
    }

    public function delete(User $user, Attraction $attraction): bool
    {
        return $user->can(PermissionType::ATTRACTION_MANAGE->value);
    }
}
