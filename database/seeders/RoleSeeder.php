<?php

namespace Database\Seeders;

use App\Enums\Auth\RoleType;
use Illuminate\Database\Seeder;
use App\Enums\Auth\PermissionType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Contracts\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Uruchomienie konkretnego seedera:
        // sail artisan db:seed --class=RoleSeeder

        // Reset cache'a ról i uprawnień:
        // sail artisan permission:cache-reset
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => RoleType::ADMIN]);
        Role::create(['name' => RoleType::TOURIST]);
        Role::create(['name' => RoleType::HOLDER]);

        // ADMINISTRATOR SYSTEMU
        $userRole = Role::findByName(RoleType::ADMIN->value);
        $userRole->givePermissionTo(PermissionType::USER_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::USER_MANAGE->value);
        
        $userRole->givePermissionTo(PermissionType::ATTRACTION_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::ATTRACTION_MANAGE->value);
        
        // Wlasciciel
        $userRole = Role::findByName(RoleType::HOLDER->value);
        $userRole->givePermissionTo(PermissionType::ATTRACTION_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::ATTRACTION_MANAGE->value);
        
        // Turysta
        $userRole = Role::findByName(RoleType::TOURIST->value);
        $userRole->givePermissionTo(PermissionType::ATTRACTION_ACCESS->value);
        
    }
}
