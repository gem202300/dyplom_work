<?php

namespace App\Http\Controllers;

use App\Models\BannedWord;
use Illuminate\Http\Request;

class BannedWordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:255',
            'partial' => 'required|boolean'
        ]);

        BannedWord::create([
            'word' => mb_strtolower($request->word),
            'partial' => $request->partial,
        ]);

        return back()->with('success', 'Słowo zostało dodane.');
    }
}
