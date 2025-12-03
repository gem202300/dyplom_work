<?php

namespace Database\Seeders;

use App\Models\Nocleg;
use App\Models\NoclegPhoto;
use Illuminate\Database\Seeder;

class NoclegSeeder extends Seeder
{
    public function run()
    {
        $domki = Nocleg::create([
            'title' => 'Domki w Zakarpaciu',
            'description' => 'Przytulne domki w górach.',
            'user_id' => 103,
            'capacity' => 6,
            'object_type' => 'domki',
            'city' => 'Użhorod',
            'street' => 'Hórnia 22',
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

        $hotel = Nocleg::create([
            'title' => 'Hotel we Lwowie',
            'description' => 'Nowoczesny hotel.',
            'status' => 'approved',            
            'capacity' => 20,
            'object_type' => 'hotel',
            'city' => 'Lwów',
            'street' => 'Prospekt Svobody 14',
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
