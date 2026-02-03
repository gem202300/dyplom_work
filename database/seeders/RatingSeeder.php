<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Nocleg;
use App\Models\Rating;
use App\Models\Attraction;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('błąd');
            return;
        }

        $objects = collect()
            ->merge(Attraction::all()->map(fn($item) => ['type' => Attraction::class, 'model' => $item]))
            ->merge(Nocleg::all()->map(fn($item) => ['type' => Nocleg::class, 'model' => $item]));

        if ($objects->isEmpty()) {
            $this->command->warn('Немає атракцій або ноцлегів для оцінки.');
            return;
        }

        $totalRatings = 0;
        $maxRatingsPerObject = 20;   
        $chanceToRate = 45;          

        foreach ($objects as $item) {
            $rateable = $item['model'];
            $type     = $item['type'];

            $alreadyRated = Rating::where('rateable_id', $rateable->id)
                ->where('rateable_type', $type)
                ->pluck('user_id')
                ->toArray();

            $availableUsers = $users->whereNotIn('id', $alreadyRated);

            if ($availableUsers->count() < 3) {
                continue;
            }

            $potentialCount = min($maxRatingsPerObject, $availableUsers->count());

            $usersWhoRate = $availableUsers
                ->shuffle()
                ->take($potentialCount)
                ->filter(fn() => rand(1, 100) <= $chanceToRate);

            foreach ($usersWhoRate as $user) {
                Rating::factory()->create([
                    'user_id'       => $user->id,
                    'rateable_id'   => $rateable->id,
                    'rateable_type' => $type,
                ]);

                $totalRatings++;
            }
        }

        $totalObjects = $objects->count();
        $avg = $totalObjects > 0 ? round($totalRatings / $totalObjects, 1) : 0;
    }
}