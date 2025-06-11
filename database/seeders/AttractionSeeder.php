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
        // Atrakcja 1 – Zamek we Lwowie
        $zamek = Attraction::create([
            'name' => 'Zamek we Lwowie',
            'location' => 'Lwów, Ukraina',
            'description' => 'Starożytny zamek z widokiem na miasto.',
            'opening_time' => '10:00',
            'closing_time' => '18:00',
            'rating' => 4.5,
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

        // Przypisanie kategorii "Zamki"
        $zamek->categories()->attach(
            Category::where('name', 'Zamki')->first()->id
        );

        // Atrakcja 2 – Jezioro Synewyr
        $jezioro = Attraction::create([
            'name' => 'Jezioro Synewyr',
            'location' => 'Zakarpacie, Ukraina',
            'description' => 'Największe naturalne jezioro w ukraińskich Karpatach.',
            'opening_time' => '09:00',
            'closing_time' => '19:00',
            'rating' => 4.8,
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

        // Przypisanie kategorii "Jeziora" i "Przyroda"
        $jezioro->categories()->attach([
            Category::where('name', 'Jeziora')->first()->id,
            Category::where('name', 'Przyroda')->first()->id,
        ]);
    }
}
