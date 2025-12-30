<?php

namespace Database\Seeders;

use App\Models\Nocleg;
use App\Models\ObjectType;
use App\Models\NoclegPhoto;
use Illuminate\Database\Seeder;

class NoclegSeeder extends Seeder
{
    public function run(): void
    {
        $domkiType = ObjectType::where('name', 'Domki')->first();
        $hotelType = ObjectType::where('name', 'Hotel')->first();

        // Випадкові координати Польщі
        $polandCoords = [
            ['lat' => 52.2297, 'lng' => 21.0122],  // Warszawa
            ['lat' => 50.0614, 'lng' => 19.9372],  // Kraków
            ['lat' => 51.1079, 'lng' => 17.0385],  // Wrocław
            ['lat' => 54.3520, 'lng' => 18.6466],  // Gdańsk
            ['lat' => 53.1325, 'lng' => 23.1688],  // Białystok
            ['lat' => 51.2465, 'lng' => 22.5684],  // Lublin
            ['lat' => 52.4064, 'lng' => 16.9252],  // Poznań
            ['lat' => 53.4285, 'lng' => 14.5528],  // Szczecin
            ['lat' => 50.2945, 'lng' => 18.6714],  // Katowice
            ['lat' => 53.0138, 'lng' => 18.5984],  // Toruń
        ];

        // Випадкові координати для домків (Татры/Карконоше)
        $domkiCoords = $polandCoords[array_rand($polandCoords)];
        $domkiLat = $domkiCoords['lat'] + (rand(-500, 500) / 10000);
        $domkiLng = $domkiCoords['lng'] + (rand(-500, 500) / 10000);

        $domki = Nocleg::create([
            'title' => 'Domki w Zakarpaciu',
            'description' => 'Przytulne domki w górach.',
            'user_id' => 103,
            'capacity' => 6,
            'object_type_id' => $domkiType->id,
            'city' => 'Użhorod',
            'street' => 'Hórnia 22',
            'latitude' => $domkiLat,  
            'longitude' => $domkiLng, 
            'contact_phone' => '+380991112233',
            'link' => 'https://example.com/domki',
            'has_kitchen' => true,
            'has_parking' => true,
            'has_bathroom' => true,
            'has_wifi' => false,
            'amenities_other' => 'Widok na góry',
        ]);

        NoclegPhoto::insert([
            ['nocleg_id' => $domki->id, 'path' => 'images/noclegi/domki1.png'],
            ['nocleg_id' => $domki->id, 'path' => 'images/noclegi/domki2.png'],
        ]);

        $hotelCoords = $polandCoords[array_rand($polandCoords)];
        $hotelLat = $hotelCoords['lat'] + (rand(-200, 200) / 10000);
        $hotelLng = $hotelCoords['lng'] + (rand(-200, 200) / 10000);

        $hotel = Nocleg::create([
            'title' => 'Hotel we Lwowie',
            'description' => 'Nowoczesny hotel.',
            'status' => 'approved',
            'capacity' => 20,
            'object_type_id' => $hotelType->id,
            'city' => 'Lwów',
            'street' => 'Prospekt Svobody 14',
            'latitude' => $hotelLat,   
            'longitude' => $hotelLng,  
            'contact_phone' => '+380501234567',
            'link' => 'https://example.com/hotel',
            'has_kitchen' => false,
            'has_parking' => true,
            'has_wifi' => true,
            'has_tv' => true,
        ]);

        NoclegPhoto::insert([
            ['nocleg_id' => $hotel->id, 'path' => 'images/noclegi/hotel1.png'],
            ['nocleg_id' => $hotel->id, 'path' => 'images/noclegi/hotel2.png'],
        ]);
    }
}