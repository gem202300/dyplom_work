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

        // Різні коментарі залежно від оцінки
        $comments = [
            1 => [
                'Niestety, bardzo się zawiodłem.',
                'Nie polecam – wszystko zaniedbane.',
                'Strata czasu i pieniędzy.',
                'Fatalna organizacja, nie wrócę.',
                'Dużo lepiej można spędzić czas gdzie indziej.',
            ],
            2 => [
                'Może być, ale bez szału.',
                'Średnio, spodziewałem się więcej.',
                'Niektóre rzeczy OK, ale ogólnie słabo.',
                'Trochę rozczarowania.',
                'Da się przeżyć, ale nie polecam specjalnie.',
            ],
            3 => [
                'Całkiem OK, ale nic specjalnego.',
                'Przyzwoicie, choć mogłoby być lepiej.',
                'Ani źle, ani super – średnio.',
                'Warto zobaczyć raz, ale niekoniecznie wracać.',
                'Normalne miejsce, bez większych emocji.',
            ],
            4 => [
                'Bardzo fajne miejsce, polecam!',
                'Super się bawiłem, miło spędzony czas.',
                'Pięknie, warto przyjechać.',
                'Dobrze zorganizowane, przyjemna atmosfera.',
                'Jedno z lepszych miejsc w okolicy!',
            ],
            5 => [
                'Absolutnie rewelacja! Przyjadę ponownie!',
                'Przepiękne miejsce, zachwycone!',
                'Najlepsza atrakcja w regionie!',
                'Wow, nie spodziewałem się tak dobrze!',
                '5 gwiazdek bez wahania – perfekt!',
                'Cudowne wspomnienia, dziękuję!',
                'Must-see! Nie można przegapić.',
            ],
        ];

        foreach ($attractions as $attraction) {
            // Перемішуємо користувачів, щоб оцінки були випадкові
            $shuffledUsers = $users->shuffle();

            // Кожен атракціон оцінює приблизно 30-50% користувачів
            $usersToRate = $shuffledUsers->take(rand(
                intval($users->count() * 0.3),
                intval($users->count() * 0.5)
            ));

            foreach ($usersToRate as $user) {
                // Перевіряємо, чи вже немає оцінки від цього юзера
                $exists = Rating::where('user_id', $user->id)
                    ->where('rateable_id', $attraction->id)
                    ->where('rateable_type', Attraction::class)
                    ->exists();

                if (!$exists) {
                    // Випадкова оцінка від 1 до 5
                    $ratingValue = rand(1, 5);

                    // 70% шанс залишити коментар
                    $comment = 10
                        ? $comments[$ratingValue][array_rand($comments[$ratingValue])]
                        : null;

                    Rating::create([
                        'rateable_id'   => $attraction->id,
                        'rateable_type' => Attraction::class,
                        'user_id'       => $user->id,
                        'rating'        => $ratingValue,
                        'comment'       => $comment,
                    ]);
                }
            }
        }

        // Опціонально: вивести статистику в консоль
        $totalRatings = Rating::count();
        $this->command->info("Створено оцінок: {$totalRatings}");
        $this->command->info("Середня кількість оцінок на атракцію: " . round($totalRatings / $attractions->count(), 1));
    }
}