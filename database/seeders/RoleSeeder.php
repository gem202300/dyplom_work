<?php

namespace Database\Seeders;

use App\Enums\Auth\RoleType;
use Illuminate\Database\Seeder;
use App\Enums\Auth\PermissionType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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
        Role::create(['name' => RoleType::WORKER]);
        Role::create(['name' => RoleType::USER]);

        // ADMINISTRATOR SYSTEMU
        $userRole = Role::findByName(RoleType::ADMIN->value);
        $userRole->givePermissionTo(PermissionType::USER_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::USER_MANAGE->value);
        $userRole->givePermissionTo(PermissionType::EVENT_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::EVENT_MANAGE->value);
        $userRole->givePermissionTo(PermissionType::RESERBATION_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::RESERBATION_MANAGE->value);
        // PRACOWNIK
        $userRole = Role::findByName(RoleType::WORKER->value);
        $userRole->givePermissionTo(PermissionType::EVENT_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::EVENT_MANAGE->value);
        $userRole->givePermissionTo(PermissionType::RESERBATION_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::RESERBATION_MANAGE->value);

        // UŻYTKOWNIK
        $userRole = Role::findByName(RoleType::USER->value);
        //$userRole->givePermissionTo(PermissionType::USER_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::EVENT_ACCESS->value);
        $userRole->givePermissionTo(PermissionType::RESERBATION_ACCESS->value);
    }
}
