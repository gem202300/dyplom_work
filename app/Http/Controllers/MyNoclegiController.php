<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyNoclegiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $noclegi = $user->noclegi()->latest()->paginate(9);

        return view('noclegi.my-noclegi', compact('noclegi'));
    }
}
