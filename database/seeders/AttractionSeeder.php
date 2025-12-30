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
            'latitude' => 49.8397 + (rand(-100, 100) / 10000),  
            'longitude' => 24.0297 + (rand(-100, 100) / 10000), 
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

        $polandLakes = [
            ['lat' => 53.7167, 'lng' => 21.8500],  
            ['lat' => 53.7833, 'lng' => 21.5500],  
            ['lat' => 53.5833, 'lng' => 21.9333],  
            ['lat' => 49.6222, 'lng' => 20.6908],  
            ['lat' => 49.4978, 'lng' => 20.1386],  
        ];
        
        $lakeCoords = $polandLakes[array_rand($polandLakes)];
        $lakeLat = $lakeCoords['lat'] + (rand(-50, 50) / 10000);
        $lakeLng = $lakeCoords['lng'] + (rand(-50, 50) / 10000);
        
        $jezioro = Attraction::create([
            'name' => 'Jezioro Synewyr',
            'location' => 'Zakarpacie, Ukraina',
            'description' => 'Największe naturalne jezioro w ukraińskich Karpatach.',
            'opening_time' => '09:00',
            'closing_time' => '19:00',
            'rating' => 4.8,
            'latitude' => $lakeLat,   
            'longitude' => $lakeLng,  
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