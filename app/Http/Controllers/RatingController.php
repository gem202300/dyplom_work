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
        // Публічний, без authorize (фільтрація рейтингів)
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
        $this->authorize('create', Rating::class);  // Для tourist/owner (create rating)

        $validated = $request->validate([
            'rateable_type' => 'required|string',
            'rateable_id' => 'required|integer',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existingRating = Rating::where('user_id', auth()->id())
            ->where('rateable_id', $validated['rateable_id'])
            ->where('rateable_type', $validated['rateable_type'])
            ->first();

        if ($existingRating) {
            return back()->withErrors([
                'rating' => 'Można dodać tylko jedną opinię dla danego obiektu.'
            ])->withInput();
        }

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


    /**
     * Report a rating.
     */
    public function report(Request $request, Rating $rating)
    {
        $this->authorize('view', $rating);

        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

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
    /**
 * Clean comment from banned words.
 */
   
private function cleanComment(?string $comment): ?string
{
    if (!$comment || trim($comment) === '') {
        return $comment;
    }

    // Отримуємо унікальні заборонені слова в нижньому регістрі + їх partial
    $bannedWords = BannedWord::all()
        ->mapWithKeys(function ($word) {
            return [mb_strtolower($word->word) => $word->partial];
        })
        ->unique()
        ->all();

    foreach ($bannedWords as $badWordLower => $isPartial) {
        // Повний бан
        if (!$isPartial) {
            if (preg_match('/\b' . preg_quote($badWordLower, '/') . '\b/iu', $comment)) {
                return null;
            }
            continue;
        }

        // Часткове маскування — замінюємо ВСІ входження правильно
        $comment = preg_replace_callback(
            '/\b' . preg_quote($badWordLower, '/') . '\b/iu',
            function ($matches) {
                $word = $matches[0];
                $length = mb_strlen($word);

                if ($length < 3) {
                    return str_repeat('*', $length);
                }

                $first = mb_substr($word, 0, 1);
                $last = mb_substr($word, -1);
                $middle = str_repeat('*', $length - 2);

                return $first . $middle . $last;
            },
            $comment
        );
    }

    return $comment;
}
}