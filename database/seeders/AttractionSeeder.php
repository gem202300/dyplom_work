<?php

namespace Database\Seeders;

use App\Models\Attraction;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    public function run(): void
    {
        Attraction::factory()
            ->count(20) // можеш змінити кількість
            ->create();
    }
}