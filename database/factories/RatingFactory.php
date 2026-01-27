<?php

namespace Database\Factories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        $ratingValue = $this->faker->numberBetween(1, 5);

        $comments = [
            1 => [
                'Niestety, bardzo się zawiodłem.',
                'Nie polecam – wszystko zaniedbane.',
                'Strata czasu i pieniędzy.',
                'Fatalna organizacja, nie wrócę.',
                'Dużo lepiej można spędzić czas gdzie indziej.',
                'Kompletne rozczarowanie, szkoda każdego złotego.',
            ],
            2 => [
                'Może być, ale bez szału.',
                'Średnio, spodziewałem się więcej.',
                'Niektóre rzeczy OK, ale ogólnie słabo.',
                'Trochę rozczarowania.',
                'Da się przeżyć, ale nie polecam specjalnie.',
                'Nie najgorsze, ale daleko do ideału.',
            ],
            3 => [
                'Całkiem OK, ale nic specjalnego.',
                'Przyzwoicie, choć mogłoby być lepiej.',
                'Ani źle, ani super – średnio.',
                'Warto zobaczyć raz, ale niekoniecznie wracać.',
                'Normalne miejsce, bez większych emocji.',
                'Znośnie, nic więcej.',
            ],
            4 => [
                'Bardzo fajne miejsce, polecam!',
                'Super się bawiłem, miło spędzony czas.',
                'Pięknie, warto przyjechać.',
                'Dobrze zorganizowane, przyjemna atmosfera.',
                'Jedno z lepszych miejsc w okolicy!',
                'Bardzo pozytywnie zaskoczony!',
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

        $comment = $comments[$ratingValue][array_rand($comments[$ratingValue])];

        return [
            'rateable_id'   => null,   
            'rateable_type' => null,
            'user_id'       => null,
            'rating'        => $ratingValue,
            'comment'       => $comment,
        ];
    }
}