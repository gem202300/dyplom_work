<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\RatingReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingReportFactory extends Factory
{
    protected $model = RatingReport::class;

    public function definition(): array
    {
        return [
            'rating_id' => null,   
            'user_id'   => null,   
            'reason'    => $this->faker->randomElement([
                'Wulgaryzmy',
                'Nieobiektywna ocena',
                'Treści obraźliwe',
                'Spam',
                'Fałszywa informacja',
                'Reklama / spam',
                'Inne',
            ]),
        ];
    }
}