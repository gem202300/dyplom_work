<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RatingReport;
use Livewire\WithPagination;

class ReportedRatings extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $showAddBannedWord = false;
    public $bannedWord = [
        'word' => '',
        'partial' => true,
    ];
    public function deleteRating($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);
        if ($rating) {
            $rating->delete();
            session()->flash('success', 'Komentarz został usunięty.');
        }
    }
    public function saveBannedWord()
    {
        $this->validate([
            'bannedWord.word' => 'required|string|max:50',
            'bannedWord.partial' => 'required|boolean',
        ]);

        \App\Models\BannedWord::create([
            'word' => $this->bannedWord['word'],
            'partial' => $this->bannedWord['partial'],
        ]);

        $this->showAddBannedWord = false;
        $this->bannedWord = ['word' => '', 'partial' => true];

        session()->flash('success', 'Słowo zostało dodane do banu');
    }

    public function clearReport($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);
        if ($rating) {
            $rating->reports()->delete();
            $rating->is_flagged = false;
            $rating->save();
            session()->flash('success', 'Zgłoszenia zostały wyczyszczone.');
        }
    }

    public function render()
    {
        $reports = RatingReport::with('rating.user', 'rating.rateable')
                    ->latest()
                    ->paginate(12);

        return view('livewire.admin.reported-ratings', compact('reports'));
    }
}
