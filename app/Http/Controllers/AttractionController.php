<?php
namespace App\Http\Controllers;

use App\Models\Attraction;

class AttractionController extends Controller
{
    public function index()
    {
        return view('attractions.index');
    }

    public function create()
    {
        return view('attractions.create');
    }

    public function edit(Attraction $attraction)
    {
        return view('attractions.edit', compact('attraction'));
    }
    
    public function show(Attraction $attraction)
    {
        $attraction->load(['photos', 'categories']);

        $ratings = $attraction->ratings()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('attractions.show', compact('attraction', 'ratings'));
    }



}
