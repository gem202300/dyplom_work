<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Auth\RoleType;

class AdminPolicy
{
   
    public function adminAccess(User $user): bool
    {
        return $user->hasRole(RoleType::ADMIN->value);
    }
}