<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Auth\PermissionType;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(\App\Enums\Auth\RoleType::ADMIN->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can(PermissionType::USER_ACCESS->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can(PermissionType::USER_MANAGE->value);
    }
}
