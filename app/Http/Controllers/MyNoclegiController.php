<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;

class MyNoclegiController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['owner'])) {
            abort(403);
        }

        $noclegi = auth()->user()->noclegi()->latest()->paginate(9);

        return view('noclegi.my-noclegi', compact('noclegi'));
    }
}