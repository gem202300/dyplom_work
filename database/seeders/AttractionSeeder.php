<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Attraction;
use App\Models\AttractionPhoto;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    public function run()
    {
        $zamek = Attraction::create([
            'name' => 'Zamek we Lwowie',
            'location' => 'Lwów, Ukraina',
            'description' => 'Starożytny zamek z widokiem na miasto.',
            'opening_time' => '10:00',
            'closing_time' => '18:00',
            'rating' => 4.5,
            'latitude' => 49.8397,
            'longitude' => 24.0297,
        ]);

        AttractionPhoto::insert([
            [
                'attraction_id' => $zamek->id,
                'path' => 'images/attractions/lviv-castle.jpg',
            ],
            [
                'attraction_id' => $zamek->id,
                'path' => 'images/attractions/lviv-castle-2.jpg',
            ],
        ]);

        $zamek->categories()->attach(
            Category::where('name', 'Zamki')->first()->id
        );

        
        $jezioro = Attraction::create([
            'name' => 'Jezioro Synewyr',
            'location' => 'Zakarpacie, Ukraina',
            'description' => 'Największe naturalne jezioro w ukraińskich Karpatach.',
            'opening_time' => '09:00',
            'closing_time' => '19:00',
            'rating' => 4.8,
            'latitude' => 48.6222,
            'longitude' => 23.6908,
        ]);

        AttractionPhoto::insert([
            [
                'attraction_id' => $jezioro->id,
                'path' => 'images/attractions/synevyr-1.jpg',
            ],
            [
                'attraction_id' => $jezioro->id,
                'path' => 'images/attractions/synevyr-2.jpg',
            ],
        ]);

        $jezioro->categories()->attach([
            Category::where('name', 'Jeziora')->first()->id,
            Category::where('name', 'Przyroda')->first()->id,
        ]);
    }
}
