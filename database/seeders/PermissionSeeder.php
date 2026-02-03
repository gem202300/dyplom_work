<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Auth\PermissionType;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => PermissionType::USER_ACCESS->value]);
        Permission::create(['name' => PermissionType::USER_MANAGE->value]);

        Permission::create(['name' => PermissionType::ATTRACTION_ACCESS->value]);
        Permission::create(['name' => PermissionType::ATTRACTION_MANAGE->value]);

        Permission::create(['name' => PermissionType::CATEGORY_ACCESS->value]);
        Permission::create(['name' => PermissionType::CATEGORY_MANAGE->value]);

        Permission::create(['name' => PermissionType::NOCLEG_VIEW->value]);
        Permission::create(['name' => PermissionType::NOCLEG_MANAGE->value]);
        Permission::create(['name' => PermissionType::NOCLEG_OWNER_MANAGE->value]); 

        Permission::create(['name' => PermissionType::RATING_VIEW->value]);
        Permission::create(['name' => PermissionType::RATING_CREATE->value]);
        Permission::create(['name' => PermissionType::RATING_MANAGE->value]);

        Permission::create(['name' => PermissionType::BANNED_WORDS_MANAGE->value]);
        Permission::create(['name' => PermissionType::MY_NOCLEGI_ACCESS->value]);
    }
}