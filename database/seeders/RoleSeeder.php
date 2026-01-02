<?php

namespace Database\Seeders;

use App\Enums\Auth\RoleType;
use Illuminate\Database\Seeder;
use App\Enums\Auth\PermissionType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => RoleType::ADMIN->value]);
        Role::create(['name' => RoleType::TOURIST->value]);
        Role::create(['name' => RoleType::OWNER->value]);

        $admin = Role::findByName(RoleType::ADMIN->value);
        $admin->givePermissionTo([
            PermissionType::USER_ACCESS,
            PermissionType::USER_MANAGE,
            PermissionType::ATTRACTION_ACCESS,
            PermissionType::ATTRACTION_MANAGE,
            PermissionType::CATEGORY_ACCESS,
            PermissionType::CATEGORY_MANAGE,
            PermissionType::NOCLEG_VIEW,
            PermissionType::NOCLEG_MANAGE,
            PermissionType::RATING_VIEW,
            PermissionType::RATING_CREATE,
            PermissionType::RATING_MANAGE,
            PermissionType::BANNED_WORDS_MANAGE,
        ]);

        $owner = Role::findByName(RoleType::OWNER->value);
        $owner->givePermissionTo([
            PermissionType::ATTRACTION_ACCESS,
            PermissionType::CATEGORY_ACCESS,
            PermissionType::NOCLEG_VIEW,
            PermissionType::NOCLEG_OWNER_MANAGE,
            PermissionType::RATING_VIEW,
            PermissionType::RATING_CREATE,
            PermissionType::MY_NOCLEGI_ACCESS,
        ]);

        $tourist = Role::findByName(RoleType::TOURIST->value);
        $tourist->givePermissionTo([
            PermissionType::ATTRACTION_ACCESS,
            PermissionType::CATEGORY_ACCESS,
            PermissionType::NOCLEG_VIEW,
            PermissionType::RATING_VIEW,
            PermissionType::RATING_CREATE,
        ]);
    }
}