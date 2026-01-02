<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Nocleg;
use App\Enums\Auth\PermissionType;

class NoclegPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::NOCLEG_VIEW->value);
    }

    public function view(User $user, Nocleg $nocleg): bool
    {
        return $user->can(PermissionType::NOCLEG_VIEW->value);
    }

    // Створення — адмін або owner
    public function create(User $user): bool
    {
        return $user->can(PermissionType::NOCLEG_MANAGE->value) ||
               $user->can(PermissionType::NOCLEG_OWNER_MANAGE->value);
    }

    // Редагування — адмін або власник свого nocleg
    public function update(User $user, Nocleg $nocleg): bool
    {
        return $user->can(PermissionType::NOCLEG_MANAGE->value) ||
               ($user->can(PermissionType::NOCLEG_OWNER_MANAGE->value) && $nocleg->user_id === $user->id);
    }

    // Видалення — адмін або власник свого nocleg
    public function delete(User $user, Nocleg $nocleg): bool
    {
        return $user->can(PermissionType::NOCLEG_MANAGE->value) ||
               ($user->can(PermissionType::NOCLEG_OWNER_MANAGE->value) && $nocleg->user_id === $user->id);
    }
}