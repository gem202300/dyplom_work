<?php

namespace Database\Factories;

use App\Models\MapIcon;
use App\Models\Category;
use App\Models\Attraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttractionFactory extends Factory
{
    protected $model = Attraction::class;

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

        return ['name' => 'Kalisz', 'lat' => 51.761, 'lon' => 18.091];
    }

    public function definition(): array
    {
        $cityData = $this->getRandomCity();
        $cityName = $cityData['name'];

        $offsetMultiplier = ($cityName === 'Kalisz') ? 0.025 : 0.065;
        $lat = $cityData['lat'] + $this->faker->randomFloat(4, -$offsetMultiplier, $offsetMultiplier);
        $lon = $cityData['lon'] + $this->faker->randomFloat(4, -$offsetMultiplier, $offsetMultiplier);

        return [
            'name'         => $this->faker->unique()->words(rand(2, 4), true) . ' w ' . $cityName,
            'location'     => $cityName . ', Wielkopolskie, Polska',
            'description'  => $this->faker->paragraphs(rand(3, 6), true),
            'opening_time' => $this->faker->optional(0.8)->time('H:i'),
            'closing_time' => $this->faker->optional(0.8)->time('H:i'),
            'is_active'    => $this->faker->boolean(90),
            'rating'       => $this->faker->randomFloat(2, 3.5, 5.0),
            'latitude'     => $lat,
            'longitude'    => $lon,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Attraction $attraction) {
            // Фото
            $photosCount = rand(3, 8);
            $photos = [];
            for ($i = 0; $i < $photosCount; $i++) {
                $photos[] = [
                    'path'       => 'https://picsum.photos/id/' . rand(1, 1000) . '/800/600',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $attraction->photos()->createMany($photos);

            // Категорії
            $categories = Category::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $attraction->categories()->attach($categories);

            // Іконка на основі першої категорії
            $firstCategoryId = $categories->first();
            $mapIcon = MapIcon::where('category_id', $firstCategoryId)->first()
                ?? MapIcon::where('name', 'Domyślny marker')->first();

            $attraction->update([
                'map_icon' => $mapIcon?->icon_url
            ]);
        });
    }
}