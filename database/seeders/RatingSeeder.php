<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rating;
use App\Models\Attraction;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $attractions = Attraction::all();

        foreach ($attractions as $attraction) {
            Rating::create([
                'rateable_id' => $attraction->id,
                'rateable_type' => Attraction::class,
                'user_id' => $user->id,
                'rating' => rand(3, 5),
                'comment' => 'To miłe miejsce!',
            ]);

            Rating::create([
                'rateable_id' => $attraction->id,
                'rateable_type' => Attraction::class,
                'user_id' => $user->id,
                'rating' => rand(2, 5),
                'comment' => 'Przyjadę ponownie!',
            ]);
            Rating::create([
                'rateable_id' => $attraction->id,
                'rateable_type' => Attraction::class,
                'user_id' => $user->id,
                'rating' => rand(2, 5),
                'comment' => 'Przyjadę ponownie!',
            ]);
            Rating::create([
                'rateable_id' => $attraction->id,
                'rateable_type' => Attraction::class,
                'user_id' => $user->id,
                'rating' => rand(2, 5),
                'comment' => 'Przyjadę ponownie!',
            ]);
            Rating::create([
                'rateable_id' => $attraction->id,
                'rateable_type' => Attraction::class,
                'user_id' => $user->id,
                'rating' => rand(2, 5),
                'comment' => 'Przyjadę ponownie!',
            ]);
            Rating::create([
                'rateable_id' => $attraction->id,
                'rateable_type' => Attraction::class,
                'user_id' => $user->id,
                'rating' => rand(2, 5),
                'comment' => 'Przyjadę ponownie!',
            ]);
        }
    }
}
