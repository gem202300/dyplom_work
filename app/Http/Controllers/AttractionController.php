<?php

namespace App\Http\Controllers;

use App\Models\Attraction;

class AttractionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Attraction::class); 
        return view('attractions.index');
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

    public function show(Attraction $attraction)
    {
        $this->authorize('view', $attraction); 
        if (!$attraction->is_active && !auth()->check()) {
            abort(404);
        }

        $attraction->load(['photos', 'categories']);

        $ratings = $attraction->ratings()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('attractions.show', compact('attraction', 'ratings'));
    }
}