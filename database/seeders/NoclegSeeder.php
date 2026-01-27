<?php

namespace Database\Seeders;

use App\Models\Nocleg;
use Illuminate\Database\Seeder;

class NoclegSeeder extends Seeder
{
    public function run(): void
    {
        Nocleg::factory()
            ->count(40)
            ->create();

         Nocleg::factory()->count(10)->create(['status' => 'pending']);
    }
}