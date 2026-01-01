<?php

namespace Database\Seeders;

use App\Models\MapIcon;
use Illuminate\Database\Seeder;

class MapIconSeeder extends Seeder
{
    public function run()
    {
        // Очищаємо старі записи (можна закоментувати після першого запуску)
        MapIcon::truncate();

        // Додаємо твої 4 локальні іконки з правильними шляхами
       $icons = [
            [
                'name' => 'Zamek',
                'icon_url' => '/images/map-icons/icons8-castle-50.png',
                'category_id' => 1  // Замки
            ],
            [
                'name' => 'Jezioro',
                'icon_url' => '/images/map-icons/icons8-lake-50.png',
                'category_id' => 2  // Jeziorka / Wody
            ],
            [
                'name' => 'Góry',
                'icon_url' => '/images/map-icons/icons8-mountain-50.png',
                'category_id' => 4  // Przyroda / Góry
            ],
            [
                'name' => 'Muzeum',
                'icon_url' => '/images/map-icons/icons8-museum-50.png',
                'category_id' => 3  // Miejsca historyczne
            ],
        ];

        // Ночлеги (нові іконки)
        $noclegIcons = [
            [
                'name' => 'Hotel',
                'icon_url' => '/images/map-icons/icons8-hotel-50.png',
                'category_id' => null  // Ночлеги будуть в null
            ],
            [
                'name' => 'Apartament',
                'icon_url' => '/images/map-icons/icons8-apartment-100.png',
                'category_id' => null
            ],
            [
                'name' => 'Domek',
                'icon_url' => '/images/map-icons/icons8-cottage-80.png',
                'category_id' => null
            ],
            [
                'name' => 'Hostel',
                'icon_url' => '/images/map-icons/icons8-hostel-50.png',
                'category_id' => null
            ],
        ];

        foreach (array_merge($icons, $noclegIcons) as $iconData) {
            MapIcon::create($iconData);
        }

        // Опціонально: fallback іконка (якщо жодна категорія не підійде)
        // Якщо не хочеш — просто видали цей блок
        MapIcon::create([
            'name' => 'Domyślny marker',
            'icon_url' => 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
            'category_id' => null
        ]);
    }
}