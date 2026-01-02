<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;

class NoclegController extends Controller
{
    
    public function show(Nocleg $nocleg)
    {
        $this->authorize('view', $nocleg);  
        $ratings = $nocleg->ratings()->latest()->paginate(5);
        return view('noclegi.show', compact('nocleg', 'ratings'));
    }
    public function details(Nocleg $nocleg)
    {
        $this->authorize('view', $nocleg);  
        $nocleg->load('photos', 'user');

        return view('noclegi.details', compact('nocleg'));
    }


    public function create()
    {
        $this->authorize('create', Nocleg::class);
        return view('noclegi.create');
    }

    public function edit(Nocleg $nocleg)
    {
        $this->authorize('update', $nocleg);
        return view('noclegi.edit', compact('nocleg'));
    }
}