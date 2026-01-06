<?php

namespace App\Http\Controllers;

use App\Models\Attraction;

class AttractionController extends Controller
{
    public function index()
    {
        $attractions = Attraction::where('is_active', true)->get();
        return view('attractions.index', compact('attractions'));
    }

    public function show(Attraction $attraction)
    {
        if (!$attraction->is_active && !auth()->check()) {
            abort(404);
        }

        $attraction->load(['photos', 'categories']);
        $ratings = $attraction->ratings()->with('user')->latest()->paginate(5);

        return view('attractions.show', compact('attraction', 'ratings'));
    }


    public function create()
    {
        $this->authorize('create', Attraction::class); 
        return view('attractions.create');
    }

    public function edit(Attraction $attraction)
    {
        $this->authorize('update', $attraction);  
        return view('attractions.edit', compact('attraction'));
    }

    
}