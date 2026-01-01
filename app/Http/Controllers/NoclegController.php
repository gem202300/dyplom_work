<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;

class NoclegController extends Controller
{
    
    public function show(Nocleg $nocleg)
    {
        $ratings = $nocleg->ratings()->latest()->paginate(5);
        return view('noclegi.show', compact('nocleg', 'ratings'));
    }
    public function details(Nocleg $nocleg)
    {
        $nocleg->load('photos', 'user');

        return view('noclegi.details', compact('nocleg'));
    }


    public function edit(Nocleg $nocleg)
    {
        return view('noclegi.edit', compact('nocleg'));
    }
    public function create(Nocleg $nocleg)
    {
        return view('noclegi.create', compact('nocleg'));
    }
}
