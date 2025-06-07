<?php

namespace Database\Seeders;

use App\Enums\Auth\RoleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory(100)->create();

        \App\Models\User::factory()->create([
            'name' => 'Użytkownik Testowy',
            'email' => 'user.test@localhost',
            'password' => Hash::make('12345678'),
            'phone' => '1234567890', 
            'address' => 'Test Address, 123', 
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Pracownik Testowy',
            'email' => 'worker.test@localhost',
            'password' => Hash::make('12345678'),
            'phone' => '0987654321', 
            'address' => 'Worker Address, 456', 
        ])->assignRole(RoleType::WORKER->value);

        \App\Models\User::factory()->create([
            'name' => 'Administrator Testowy',
            'email' => 'admin.test@localhost',
            'password' => Hash::make('12345678'),
            'phone' => '1122334455', 
            'address' => 'Admin Address, 789', 
        ])->assignRole(RoleType::ADMIN->value);
    }
}