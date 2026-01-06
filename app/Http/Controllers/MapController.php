<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;
use App\Models\Attraction;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $focusData = null;
        
        if ($request->has('focus')) {
            $focusId = $request->get('focus');
            $focusType = $request->get('type', 'attraction');
            
            if ($focusType === 'attraction') {
                $item = Attraction::find($focusId);
                if ($item && $item->latitude && $item->longitude) {
                    $focusData = [
                        'id' => $item->id,
                        'type' => 'attraction',
                        'title' => $item->name,
                        'lat' => $item->latitude,
                        'lng' => $item->longitude,
                        'description' => $item->description,
                        'rating' => $item->rating,
                    ];
                }
            } elseif ($focusType === 'nocleg') {
                $item = Nocleg::find($focusId);
                if ($item && $item->latitude && $item->longitude) {
                    $focusData = [
                        'id' => $item->id,
                        'type' => 'nocleg',
                        'title' => $item->title,
                        'lat' => $item->latitude,
                        'lng' => $item->longitude,
                        'description' => $item->description,
                        'capacity' => $item->capacity,
                    ];
                }
            }
        }
        
        return view('map.index', compact('focusData'));
    }
}