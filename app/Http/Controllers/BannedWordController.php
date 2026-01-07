<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\BannedWord;
use Illuminate\Http\Request;

class BannedWordController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', BannedWord::class);  

        $request->validate([
            'word' => 'required|string|max:255',
            'partial' => 'required|boolean',
        ]);

        $word = mb_strtolower(trim($request->word));
        $partial = (bool) $request->partial;

        BannedWord::create([
            'word' => $word,
            'partial' => $partial,
        ]);

        $ratings = Rating::whereRaw(
            'LOWER(comment) LIKE ?',
            ['%' . $word . '%']
        )->get();

        foreach ($ratings as $rating) {

            $rating->reports()->delete();

            if ($partial) {
                $rating->comment = preg_replace(
                    '/' . preg_quote($word, '/') . '/iu',
                    str_repeat('*', mb_strlen($word)),
                    $rating->comment
                );

                $rating->is_flagged = false;
                $rating->save();
            } else {
                $rating->delete();
            }
        }

        return redirect()
            ->route('admin.ratings.reports')
            ->with('success', 'Słowo zakazane dodane. Zgłoszenie zostało rozwiązane.');
    }
}