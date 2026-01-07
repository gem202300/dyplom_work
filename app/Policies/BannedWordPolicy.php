<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BannedWord;
use App\Enums\Auth\PermissionType;

class BannedWordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::BANNED_WORDS_MANAGE->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BannedWord $bannedWord): bool
    {
        return $user->can(PermissionType::BANNED_WORDS_MANAGE->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionType::BANNED_WORDS_MANAGE->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BannedWord $bannedWord): bool
    {
        return $user->can(PermissionType::BANNED_WORDS_MANAGE->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BannedWord $bannedWord): bool
    {
        return $user->can(PermissionType::BANNED_WORDS_MANAGE->value);
    }
}