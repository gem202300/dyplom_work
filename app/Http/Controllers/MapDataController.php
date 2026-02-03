<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;
use App\Models\Attraction;
use App\Models\ObjectType;
use Illuminate\Http\Request;

class MapDataController extends Controller
{
    public function index()
    {
        try {
            $noclegs = Nocleg::where('status', 'approved')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0)
                ->with(['ratings', 'objectType'])
                ->select('id', 'title', 'city', 'street', 'latitude', 'longitude', 
                         'capacity', 'description', 'map_icon', 'object_type_id')
                ->get();

            // Активні атракції з рейтингом та категоріями
            $attractions = Attraction::where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0)
                ->with(['ratings', 'categories'])
                ->select('id', 'name as title', 'location as city', 'description', 
                         'latitude', 'longitude', 'map_icon')
                ->get();

            $features = [];

            foreach ($noclegs as $n) {
                if (!$this->isValidCoordinates($n->latitude, $n->longitude)) {
                    continue;
                }

                // Обчислюємо середній рейтинг
                $avgRating = $n->ratings->avg('rating');
                
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$n->longitude, (float)$n->latitude]
                    ],
                    'properties' => [
                        'id' => $n->id,
                        'title' => $n->title ?? 'Brak nazwy',
                        'address' => ($n->city ?? '') . ($n->street ? ', ' . $n->street : ''),
                        'description' => $n->description ?? '',
                        'type' => 'nocleg',
                        'capacity' => (int)($n->capacity ?? 0),
                        'rating' => round($avgRating, 1) ?? 0,
                        'object_type_id' => $n->object_type_id,
                        'object_type_name' => $n->objectType->name ?? null,
                        'icon_url' => $n->map_icon ?: '/images/map-icons/icons8-hotel-50.png',
                        'categories' => [] // Для ночлегів категорій немає
                    ]
                ];
            }

            foreach ($attractions as $a) {
                if (!$this->isValidCoordinates($a->latitude, $a->longitude)) {
                    continue;
                }

                // Обчислюємо середній рейтинг
                $avgRating = $a->ratings->avg('rating');
                
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$a->longitude, (float)$a->latitude]
                    ],
                    'properties' => [
                        'id' => $a->id,
                        'title' => $a->title ?? 'Brak nazwy',
                        'address' => $a->city ?? '',
                        'description' => $a->description ?? '',
                        'rating' => round($avgRating, 1) ?? 0,
                        'type' => 'attraction',
                        'capacity' => null, // Для атракцій вмістимість немає
                        'object_type_id' => null,
                        'icon_url' => $a->map_icon ?: '/images/map-icons/icons8-museum-50.png',
                        'categories' => $a->categories->pluck('id')->toArray(),
                    ]
                ];
            }

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $features
            ]);

        } catch (\Exception $e) {
            error_log('MapDataController error: ' . $e->getMessage());
            
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }
    }

    private function isValidCoordinates($lat, $lng): bool
    {
        return is_numeric($lat) && is_numeric($lng) 
            && $lat >= -90 && $lat <= 90 
            && $lng >= -180 && $lng <= 180
            && $lat != 0 
            && $lng != 0;
    }
}