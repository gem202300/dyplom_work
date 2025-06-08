<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\AttractionPhoto;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    public function run()
    {
        // Атракція 1
        $lvivCastle = Attraction::create([
            'name' => 'Замок у Львові',
            'location' => 'Львів, Україна',
            'description' => 'Стародавній замок із видом на місто.',
            'opening_hours' => '10:00 - 18:00',
            'rating' => 4.5,
        ]);

        AttractionPhoto::insert([
            [
                'attraction_id' => $lvivCastle->id,
                'path' => 'images/attractions/lviv-castle.jpg',
            ],
            [
                'attraction_id' => $lvivCastle->id,
                'path' => 'images/attractions/lviv-castle-2.jpg',
            ],
        ]);

        // Атракція 2
        $lake = Attraction::create([
            'name' => 'Озеро Синевир',
            'location' => 'Закарпаття, Україна',
            'description' => 'Найбільше природне озеро в Українських Карпатах.',
            'opening_hours' => '09:00 - 19:00',
            'rating' => 4.8,
        ]);

        AttractionPhoto::insert([
            [
                'attraction_id' => $lake->id,
                'path' => 'images/attractions/synevyr-1.jpg',
            ],
            [
                'attraction_id' => $lake->id,
                'path' => 'images/attractions/synevyr-2.jpg',
            ],
        ]);
    }
}
