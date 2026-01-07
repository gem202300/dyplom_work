<?php

namespace Database\Factories;

use App\Models\Nocleg;
use App\Models\MapIcon;
use App\Models\ObjectType;
use App\Enums\Auth\RoleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoclegFactory extends Factory
{
    protected $model = Nocleg::class;

    public function definition(): array
    {
        $city = $this->faker->city;
        $region = $this->faker->state ?: $this->faker->country;

        $availableTypes = ObjectType::pluck('name')->toArray();
        if (empty($availableTypes)) {
            $availableTypes = ['Domki', 'Hotel', 'Apartamenty', 'Pokoje prywatne', 'Hostel'];
        }

        $typeName = $this->faker->randomElement($availableTypes);
        $objectType = ObjectType::where('name', $typeName)->first();

        // Мапінг: назва типу → назва іконки в таблиці map_icons
        $iconMapping = [
            'Domki'            => 'Domek',       // Domki → іконка "Domek"
            'Hotel'            => 'Hotel',
            'Apartamenty'      => 'Apartament',  // Apartamenty → іконка "Apartament"
            'Pokoje prywatne'  => null,          // немає окремої іконки — fallback
            'Hostel'           => 'Hostel',
        ];

        $iconName = $iconMapping[$typeName] ?? null;

        // Шукаємо іконку за назвою (category_id = null — для ноцлегів)
        $mapIcon = $iconName
            ? MapIcon::where('name', $iconName)->whereNull('category_id')->first()
            : MapIcon::where('name', 'Domyślny marker')->first(); // fallback

        $titlePrefixes = [
            'Domki'            => ['Przytulne Domki', 'Domki Górskie', 'Drewniane Domki', 'Domki nad Jeziorem', 'Komfortowe Domki'],
            'Hotel'            => ['Hotel', 'Elegancki Hotel', 'Nowoczesny Hotel', 'Hotel Centrum', 'Boutique Hotel'],
            'Apartamenty'      => ['Apartamenty', 'Luksusowe Apartamenty', 'Komfortowe Apartamenty', 'Apartament z Widokiem'],
            'Pokoje prywatne'  => ['Pokoje Gościnne', 'Przytulne Pokoje', 'Pokoje u Gospodarzy', 'Kwatera Prywatna'],
            'Hostel'           => ['Hostel', 'Młodzieżowy Hostel', 'Tani Hostel', 'Hostel Miejski'],
        ];

        $prefix = $this->faker->randomElement($titlePrefixes[$typeName] ?? ['Komfortowy Nocleg']);

        return [
            'title' => $prefix . ' ' . $this->faker->words(rand(1, 3), true) . ' w ' . $city,
            'description' => $this->faker->paragraphs(rand(3, 7), true),
            'user_id' => function () {
                $owner = \App\Models\User::role(RoleType::OWNER->value)
                    ->inRandomOrder()
                    ->first();
                return $owner?->id;
            },
            'status' => $this->faker->randomElement(['approved', 'approved', 'approved', 'pending']),
            'capacity' => $this->faker->numberBetween(2, 50),
            'object_type_id' => $objectType?->id,
            'city' => $city,
            'street' => $this->faker->streetAddress,
            'location' => $city . ', ' . $region . ', Polska',
            'latitude' => $this->faker->latitude(49.0, 54.5),
            'longitude' => $this->faker->longitude(14.0, 24.0),
            'map_icon' => $mapIcon?->icon_url, // ← Ось і воно! Автоматично встановлюється
            'contact_phone' => $this->faker->phoneNumber,
            'link' => $this->faker->optional(0.7)->url,
            'has_kitchen' => $this->faker->boolean(70),
            'has_parking' => $this->faker->boolean(85),
            'has_bathroom' => $this->faker->boolean(95),
            'has_wifi' => $this->faker->boolean(80),
            'has_tv' => $this->faker->boolean(75),
            'has_balcony' => $this->faker->boolean(50),
            'amenities_other' => $this->faker->optional(0.4)->sentence,
            'reject_reason' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Nocleg $nocleg) {
            $photosCount = rand(3, 7);
            $photos = [];
            for ($i = 0; $i < $photosCount; $i++) {
                $photos[] = [
                    'path' => 'https://picsum.photos/id/' . rand(1, 1000) . '/800/600',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $nocleg->photos()->createMany($photos);
        });
    }
}