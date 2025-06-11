<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Зберігає нову оцінку та коментар.
     */
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rateable_type' => 'required|string',
            'rateable_id' => 'required|integer',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $rating = new Rating([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        $rating->user_id = auth()->id();
        $rating->rateable_type = $validated['rateable_type'];
        $rating->rateable_id = $validated['rateable_id'];
        $rating->save();

        return back()->with('success', 'Dziękujemy za opinię!');
    }
    
    public function __construct()
    {
        $this->middleware('auth');
    }

}
