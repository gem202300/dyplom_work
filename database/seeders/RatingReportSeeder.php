<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rating;
use App\Models\RatingReport;
use Illuminate\Database\Seeder;

class RatingReportSeeder extends Seeder
{
    public function run(): void
    {
        $totalReportsToCreate = 30;   

        $badRatings = Rating::whereIn('rating', [1, 2])
            ->get();

        if ($badRatings->isEmpty()) {
            $this->command->info('Немає рейтингів з низькою оцінкою для створення скарг.');
            return;
        }

        $users = User::all();

        $reportsCreated = 0;

        foreach ($badRatings as $rating) {
            if ($reportsCreated >= $totalReportsToCreate) {
                break;
            }

            // Перевіряємо, чи вже є скарги від когось на цей рейтинг
            $existingReporters = RatingReport::where('rating_id', $rating->id)
                ->pluck('user_id')
                ->toArray();

            $availableUsers = $users->whereNotIn('id', $existingReporters)
                ->where('id', '!=', $rating->user_id) 
                ->shuffle();

            if ($availableUsers->isNotEmpty()) {
                $reporter = $availableUsers->first();

                RatingReport::factory()->create([
                    'rating_id' => $rating->id,
                    'user_id'   => $reporter->id,
                ]);

                $reportsCreated++;
            }
        }

        while ($reportsCreated < $totalReportsToCreate) {
            $rating = $badRatings->random();

            $existingReporters = RatingReport::where('rating_id', $rating->id)
                ->pluck('user_id')
                ->toArray();

            $availableUsers = $users->whereNotIn('id', $existingReporters)
                ->where('id', '!=', $rating->user_id)
                ->shuffle();

            if ($availableUsers->isEmpty()) {
                continue;
            }

            $reporter = $availableUsers->first();

            RatingReport::factory()->create([
                'rating_id' => $rating->id,
                'user_id'   => $reporter->id,
            ]);

            $reportsCreated++;

            if (rand(1, 100) <= 10) { 
                $badRatings = $badRatings->where('id', '!=', $rating->id);
            }
        }

        $badCount = $badRatings->count();
        $avg = $badCount > 0 ? round($reportsCreated / $badCount, 1) : 0;

        $this->command->info("Створено скарг: $reportsCreated");
        $this->command->info("Поганих рейтингів: $badCount");
        $this->command->info("Середня кількість скарг на поганий рейтинг: $avg");
    }
}