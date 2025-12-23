<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\BannedWord;
use App\Models\RatingReport;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * AJAX фільтрація відгуків
     */
    public function filter(Request $request, $rateableType, $rateableId)
    {
        $query = Rating::where('rateable_type', $rateableType)
            ->where('rateable_id', $rateableId)
            ->with('user')
            ->where('is_flagged', false);

        // Фільтр за оцінкою
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        // Сортування
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            default: // 'latest'
                $query->orderBy('created_at', 'desc');
        }

        $ratings = $query->paginate(10)->withQueryString();

        // Повертаємо JSON з HTML
        return response()->json([
            'html' => view('components.partials.ratings-list', compact('ratings'))->render(),
            'pagination' => $ratings->hasPages() ? $ratings->withQueryString()->links()->toHtml() : '',
            'total' => $ratings->total()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rateable_type' => 'required|string',
            'rateable_id' => 'required|integer',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Перевірка, чи користувач вже додав рейтинг для цього об'єкта
        $existingRating = Rating::where('user_id', auth()->id())
            ->where('rateable_id', $validated['rateable_id'])
            ->where('rateable_type', $validated['rateable_type'])
            ->first();

        if ($existingRating) {
            return back()->withErrors([
                'rating' => 'Można dodać tylko jedną opinię dla danego obiektu.'
            ])->withInput();
        }

        // Чистимо коментар від заборонених слів
        $comment = $this->cleanComment($validated['comment']);

        if ($comment === null) {
            return back()->withErrors([
                'comment' => 'Twój komentarz zawiera niedozwolone słowa.'
            ])->withInput();
        }

        // Створюємо рейтинг
        Rating::create([
            'rating' => $validated['rating'],
            'comment' => $comment,
            'user_id' => auth()->id(),
            'rateable_type' => $validated['rateable_type'],
            'rateable_id' => $validated['rateable_id'],
        ]);

        return back()->with('success', 'Komentarz został dodany.');
        
    }


    /**
     * Report a rating.
     */
    public function report(Request $request, Rating $rating)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        // Перевірка, чи користувач вже скаржився на цей коментар
        $existingReport = $rating->reports()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReport) {
            return back()->with('error', 'Już zgłosiłeś ten komentarz.');
        }

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

    /**
     * Clean comment from banned words.
     */
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