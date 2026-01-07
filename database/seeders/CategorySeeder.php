<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Zamki i pałace',
            'Jeziora i rzeki',
            'Miejsca historyczne',
            'Przyroda i parki narodowe',
            'Muzea',
            'Kościoły i sanktuaria',
            'Góry i szlaki turystyczne',
            'Plaże i wybrzeże',
            'Parki rozrywki',
            'Rejsy i sporty wodne',
            'Zabytki UNESCO',
            'Stare miasta i rynki',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}