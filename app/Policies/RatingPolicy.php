<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rating;
use App\Enums\Auth\PermissionType;

class RatingPolicy
{
    /**
     * Determine whether the user can view any ratings.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::RATING_VIEW->value);
    }

    /**
     * Determine whether the user can view a specific rating.
     */
    public function view(User $user, Rating $rating): bool
    {
        return $user->can(PermissionType::RATING_VIEW->value);
    }

    /**
     * Determine whether the user can create a rating (leave a review).
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionType::RATING_CREATE->value);
    }

    public function update(User $user, Rating $rating): bool
    {
       
        return false;
    }

    /**
     * Determine whether the user can delete a rating.
     */
    public function delete(User $user, Rating $rating): bool
    {
        return $user->can(PermissionType::RATING_MANAGE->value);
    }
}