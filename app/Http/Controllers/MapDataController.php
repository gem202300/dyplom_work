<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;
use App\Models\Attraction;
use Illuminate\Http\Request;

class MapDataController extends Controller
{
    public function index()
    {
        try {
            // Затверджені ночлеги
            $noclegs = Nocleg::where('status', 'approved')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0)
                ->select('id', 'title', 'city', 'street', 'latitude', 'longitude', 'capacity', 'description')
                ->get();

            // Активні атракції
            $attractions = Attraction::where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0)
                ->select('id', 'name as title', 'location as city', 'description', 'latitude', 'longitude', 'rating')
                ->get();

            $features = [];

            foreach ($noclegs as $n) {
                // Перевірка координат
                if (!$this->isValidCoordinates($n->latitude, $n->longitude)) {
                    continue;
                }

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
                        'capacity' => $n->capacity ?? 0,
                        'icon' => 'lodging-15'
                    ]
                ];
            }

            foreach ($attractions as $a) {
                // Перевірка координат
                if (!$this->isValidCoordinates($a->latitude, $a->longitude)) {
                    continue;
                }

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
                        'rating' => $a->rating ? number_format($a->rating, 1) : '0.0',
                        'type' => 'attraction',
                        'icon' => 'attraction-15'
                    ]
                ];
            }

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $features
            ]);

        } catch (\Exception $e) {
            // Простий варіант без імпорту
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