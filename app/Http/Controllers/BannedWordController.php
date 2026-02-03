<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\BannedWord;
use Illuminate\Http\Request;

class BannedWordController extends Controller
{
    protected $fillable = [
        'word',
        'partial',
    ];
    
    public function store(Request $request)
    {
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
                $rating->comment = $this->maskBannedWordInComment(
                    $rating->comment, 
                    $word
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

  
    private function maskBannedWordInComment(?string $comment, string $bannedWord): ?string
    {
        if (!$comment || trim($comment) === '') {
            return $comment;
        }

        return preg_replace_callback(
            '/' . preg_quote($bannedWord, '/') . '/iu',
            function($matches) {
                $word = $matches[0];
                $len = mb_strlen($word);

                if ($len < 3) {
                    return str_repeat('*', $len);
                }

                $first = mb_substr($word, 0, 1);
                $last = mb_substr($word, -1);
                $middle = str_repeat('*', $len - 2);

                return $first . $middle . $last;
            },
            $comment
        );
    }
}