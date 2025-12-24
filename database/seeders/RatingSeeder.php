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

        
        $users = User::all();
        $attractions = Attraction::all();

        foreach ($users as $user) {
            foreach ($attractions as $attraction) {
                if (!Rating::where('user_id', $user->id)
                        ->where('rateable_id', $attraction->id)
                        ->where('rateable_type', Attraction::class)
                        ->exists()) {
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

    }
}
