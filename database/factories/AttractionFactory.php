<?php

namespace Database\Factories;

use App\Models\MapIcon;
use App\Models\Category;
use App\Models\Attraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttractionFactory extends Factory
{
    protected $model = Attraction::class;

    public function definition(): array
    {
        $city = $this->faker->city;
        $region = $this->faker->state ?: $this->faker->country;

        return [
            'name' => $this->faker->unique()->words(rand(2, 4), true) . ' w ' . $city,
            'location' => $city . ', ' . $region . ', Polska',
            'description' => $this->faker->paragraphs(rand(3, 6), true),
            'opening_time' => $this->faker->optional(0.8)->time('H:i'),
            'closing_time' => $this->faker->optional(0.8)->time('H:i'),
            'is_active' => $this->faker->boolean(90),
            'rating' => $this->faker->randomFloat(2, 3.5, 5.0),
            'latitude' => $this->faker->latitude(49.0, 54.5),
            'longitude' => $this->faker->longitude(14.0, 24.0),
            // map_icon встановлюється нижче в configure()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Attraction $attraction) {
            // 1. Додаємо фото (як було раніше)
            $photosCount = rand(3, 8);
            $photos = [];
            for ($i = 0; $i < $photosCount; $i++) {
                $photos[] = [
                    'path' => 'https://picsum.photos/id/' . rand(1, 1000) . '/800/600',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $attraction->photos()->createMany($photos);

            // 2. Додаємо від 1 до 3 випадкових категорій
            $categories = Category::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $attraction->categories()->attach($categories);

            // 3. Встановлюємо map_icon на основі ПЕРШОЇ категорії (як у тебе на карті)
            $firstCategoryId = $categories->first();

            $mapIcon = MapIcon::where('category_id', $firstCategoryId)->first();

            // Якщо чомусь іконки немає — ставимо domyślny marker
            if (!$mapIcon) {
                $mapIcon = MapIcon::where('name', 'Domyślny marker')->first();
            }

            // Оновлюємо запис
            $attraction->update([
                'map_icon' => $mapIcon?->icon_url
            ]);
        });
    }
}