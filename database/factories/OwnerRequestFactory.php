<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OwnerRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'phone'          => fake()->phoneNumber(),
            'reason'         => fake()->paragraphs(2, true),
            'accepted_terms' => true,
            'status'         => 'pending',
        ];
    }
}