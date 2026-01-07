<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use App\Enums\Auth\PermissionType;

class CategoryPolicy
{
    /**
     * Дозволяємо всім (включаючи гостей) бачити список категорій
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Дозволяємо всім бачити окрему категорію
     */
    public function view(?User $user, Category $category): bool
    {
        return true;
    }

    /**
     * Тільки з правами — створення, редагування, видалення
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionType::CATEGORY_MANAGE->value);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can(PermissionType::CATEGORY_MANAGE->value);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can(PermissionType::CATEGORY_MANAGE->value);
    }
}