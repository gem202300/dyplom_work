<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Zamki',
            'Jeziora',
            'Miejsca historyczne',
            'Przyroda',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
