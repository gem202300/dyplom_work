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

    private array $locations = [
        'Kalisz'               => ['weight' => 55, 'lat' => 51.761, 'lon' => 18.091],
        'Ostrów Wielkopolski'  => ['weight' => 10, 'lat' => 51.655, 'lon' => 17.806],
        'Pleszew'              => ['weight' => 8,  'lat' => 51.896, 'lon' => 17.785],
        'Nowe Skalmierzyce'    => ['weight' => 7,  'lat' => 51.710, 'lon' => 18.000],
        'Jarocin'              => ['weight' => 6,  'lat' => 51.972, 'lon' => 17.502],
        'Koźminek'             => ['weight' => 4,  'lat' => 51.786, 'lon' => 18.319],
        'Stawiszyn'            => ['weight' => 3,  'lat' => 51.918, 'lon' => 18.111],
        'Opatówek'             => ['weight' => 3,  'lat' => 51.739, 'lon' => 18.250],
        'Godziesze Wielkie'    => ['weight' => 2,  'lat' => 51.643, 'lon' => 18.078],
        'Sieroszewice'         => ['weight' => 2,  'lat' => 51.633, 'lon' => 17.983],
    ];

    private function getRandomCity(): array
    {
        $totalWeight = array_sum(array_column($this->locations, 'weight'));
        $rand = mt_rand(1, $totalWeight);

        $current = 0;
        foreach ($this->locations as $city => $data) {
            $current += $data['weight'];
            if ($rand <= $current) {
                return [
                    'name' => $city,
                    'lat'  => $data['lat'],
                    'lon'  => $data['lon'],
                ];
            }
        }

        // fallback
        return ['name' => 'Kalisz', 'lat' => 51.761, 'lon' => 18.091];
    }

    public function definition(): array
    {
        $cityData = $this->getRandomCity();
        $cityName = $cityData['name'];

        // Більший розкид для маленьких міст, менший для Каліша
        $offsetMultiplier = ($cityName === 'Kalisz') ? 0.025 : 0.065;
        $lat = $cityData['lat'] + $this->faker->randomFloat(4, -$offsetMultiplier, $offsetMultiplier);
        $lon = $cityData['lon'] + $this->faker->randomFloat(4, -$offsetMultiplier, $offsetMultiplier);

        $region = 'Wielkopolskie';

        $availableTypes = ObjectType::pluck('name')->toArray() ?: ['Domki', 'Hotel', 'Apartamenty', 'Pokoje prywatne', 'Hostel'];
        $typeName = $this->faker->randomElement($availableTypes);
        $objectType = ObjectType::where('name', $typeName)->first();

        $iconMapping = [
            'Domki'            => 'Domek',
            'Hotel'            => 'Hotel',
            'Apartamenty'      => 'Apartament',
            'Pokoje prywatne'  => null,
            'Hostel'           => 'Hostel',
        ];

        $iconName = $iconMapping[$typeName] ?? null;
        $mapIcon = $iconName
            ? MapIcon::where('name', $iconName)->whereNull('category_id')->first()
            : MapIcon::where('name', 'Domyślny marker')->first();

        $titlePrefixes = [
            'Domki'            => ['Przytulne Domki', 'Domki nad Prosną', 'Drewniane Domki', 'Komfortowe Domki'],
            'Hotel'            => ['Hotel', 'Elegancki Hotel', 'Nowoczesny Hotel', 'Hotel w Centrum'],
            'Apartamenty'      => ['Apartamenty', 'Luksusowe Apartamenty', 'Komfortowe Apartamenty'],
            'Pokoje prywatne'  => ['Pokoje Gościnne', 'Przytulne Pokoje', 'Kwatera Prywatna'],
            'Hostel'           => ['Hostel', 'Tani Hostel', 'Hostel Miejski'],
        ];

        $prefix = $this->faker->randomElement($titlePrefixes[$typeName] ?? ['Komfortowy Nocleg']);

        return [
            'title'           => $prefix . ' ' . $this->faker->words(rand(1, 3), true) . ' w ' . $cityName,
            'description'     => $this->faker->paragraphs(rand(3, 7), true),
            'user_id'         => fn() => \App\Models\User::role(RoleType::OWNER->value)->inRandomOrder()->first()?->id,
            'status'          => $this->faker->randomElement(['approved', 'approved', 'approved', 'pending']),
            'capacity'        => $this->faker->numberBetween(2, 50),
            'object_type_id'  => $objectType?->id,
            'city'            => $cityName,
            'street'          => $this->faker->streetAddress,
            'location'        => $cityName . ', Wielkopolskie, Polska',
            'latitude'        => $lat,
            'longitude'       => $lon,
            'map_icon'        => $mapIcon?->icon_url,
            'contact_phone'   => $this->faker->phoneNumber,
            'link'            => $this->faker->optional(0.7)->url,
            'has_kitchen'     => $this->faker->boolean(70),
            'has_parking'     => $this->faker->boolean(85),
            'has_bathroom'    => $this->faker->boolean(95),
            'has_wifi'        => $this->faker->boolean(80),
            'has_tv'          => $this->faker->boolean(75),
            'has_balcony'     => $this->faker->boolean(50),
            'amenities_other' => $this->faker->optional(0.4)->sentence,
            'reject_reason'   => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Nocleg $nocleg) {
            $photosCount = rand(3, 7);
            $photos = [];
            for ($i = 0; $i < $photosCount; $i++) {
                $photos[] = [
                    'path'       => 'https://picsum.photos/id/' . rand(1, 1000) . '/800/600',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $nocleg->photos()->createMany($photos);
        });
    }
}