<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Auth\PermissionType;

class MyNoclegiPolicy
{
    public function view(User $user): bool
    {
        return $user->can(PermissionType::MY_NOCLEGI_ACCESS->value);
    }
}