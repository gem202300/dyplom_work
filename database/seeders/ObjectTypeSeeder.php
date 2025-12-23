<?php

namespace Database\Seeders;

use App\Models\ObjectType;
use Illuminate\Database\Seeder;

class ObjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Domki',
            'Hotel',
            'Pokoje prywatne',
            'Apartamenty',
            'Hostel',
        ];

        foreach ($types as $type) {
            ObjectType::firstOrCreate([
                'name' => $type
            ]);
        }
    }
}
