<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\BannedWord;
use App\Models\RatingReport;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rateable_type' => 'required|string',
            'rateable_id' => 'required|integer',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $comment = $this->cleanComment($validated['comment']);

        if ($comment === null) {
            return back()->withErrors([
                'comment' => 'Twój komentarz zawiera niedozwolone słowa.'
            ])->withInput();
        }

        Rating::create([
            'rating' => $validated['rating'],
            'comment' => $comment,
            'user_id' => auth()->id(),
            'rateable_type' => $validated['rateable_type'],
            'rateable_id' => $validated['rateable_id'],
        ]);

        return back()->with('success', 'Komentarz został dodany.');

    }

    public function report(Request $request, Rating $rating)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $rating->reports()->create([
            'user_id' => $request->user()->id,
            'reason' => $request->reason
        ]);

        if ($rating->reports()->count() >= 3) {
            $rating->is_flagged = true;
            $rating->save();
        }

        return back()->with('success', 'Komentarz został zgłoszony.');
    }

    private function cleanComment(?string $comment): ?string
    {
        if (!$comment) return null;

        $bannedWords = BannedWord::all();

        foreach ($bannedWords as $word) {

            $bad = mb_strtolower($word->word);

            if (!$word->partial) {
                if (preg_match('/\b' . preg_quote($bad, '/') . '\b/iu', $comment)) {
                    return null;
                }
            } else {
                $comment = preg_replace_callback(
                    '/' . preg_quote($bad, '/') . '/iu',
                    function ($matches) {
                        $w = $matches[0];
                        if (mb_strlen($w) <= 2) {
                            return str_repeat('*', mb_strlen($w));
                        }
                        return mb_substr($w, 0, 1)
                            . str_repeat('*', mb_strlen($w) - 2)
                            . mb_substr($w, -1);
                    },
                    $comment
                );
            }
        }

        return $comment;
    }
}
