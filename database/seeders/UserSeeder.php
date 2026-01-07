<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\Auth\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(100)->create();

        User::factory()->create([
            'name' => 'Użytkownik Testowy',
            'email' => 'user.test@localhost',
            'password' => Hash::make('12345678'),
            'phone' => '1234567890',
            'address' => 'Test Address, 123',
        ])->assignRole(RoleType::TOURIST->value);

        User::factory()->create([
            'name' => 'Właściciel Testowy',
            'email' => 'owner.test@localhost',
            'password' => Hash::make('12345678'),
            'phone' => '0987654321',
            'address' => 'Holder Address, 456',
        ])->assignRole(RoleType::OWNER->value);

        User::factory()->create([
            'name' => 'Administrator Testowy',
            'email' => 'admin.test@localhost',
            'password' => Hash::make('12345678'),
            'phone' => '1122334455',
            'address' => 'Admin Address, 789',
        ])->assignRole(RoleType::ADMIN->value);

        // Призначаємо роль OWNER випадковим 12 користувачам (окрім тестових)
        User::where('email', 'not like', '%@localhost')
            ->inRandomOrder()
            ->limit(12)
            ->get()
            ->each(function ($user) {
                $user->assignRole(RoleType::OWNER->value);
            });
    }
}