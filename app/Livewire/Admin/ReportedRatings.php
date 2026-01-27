<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RatingReport;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class ReportedRatings extends Component
{
    use WithPagination;
    use WireUiActions; // ← додаємо це!

    protected $paginationTheme = 'tailwind';

    public $showAddBannedWord = false;
    public $bannedWord = [
        'word'    => '',
        'partial' => true,
    ];

    public function deleteRating($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);

        if (!$rating) {
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Czy na pewno chcesz usunąć ten komentarz?',
            'description' => 'Komentarz zostanie trwale usunięty wraz z powiązanymi zgłoszeniami.',
            'icon'        => 'warning',
            'acceptLabel' => 'Tak, usuń',
            'rejectLabel' => 'Anuluj',
            'method'      => 'deleteRatingConfirmed',
            'params'      => $ratingId,
        ]);
    }

    public function deleteRatingConfirmed($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);

        if ($rating) {
            $rating->delete();

            $this->notification()->success(
                title: 'Sukces',
                description: 'Komentarz został usunięty.'
            );
        }
    }
    public function clearReport($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);

        if (!$rating) {
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Czy na pewno chcesz odrzucić zgłoszenia?',
            'description' => 'Wszystkie zgłoszenia dla tego komentarza zostaną usunięte, a flaga zgłoszenia wyłączona.',
            'icon'        => 'question',
            'acceptLabel' => 'Tak, odrzuć',
            'rejectLabel' => 'Anuluj',
            'method'      => 'clearReportConfirmed',
            'params'      => $ratingId,
        ]);
    }

    public function clearReportConfirmed($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);

        if ($rating) {
            $rating->reports()->delete();
            $rating->is_flagged = false;
            $rating->save();

            $this->notification()->success(
                title: 'Sukces',
                description: 'Zgłoszenia zostały wyczyszczone.'
            );
        }
    }


    public function saveBannedWord()
    {
        $this->validate([
            'bannedWord.word'    => 'required|string|max:50',
            'bannedWord.partial' => 'required|boolean',
        ]);

        \App\Models\BannedWord::create([
            'word'    => $this->bannedWord['word'],
            'partial' => $this->bannedWord['partial'],
        ]);

        $this->showAddBannedWord = false;
        $this->bannedWord = ['word' => '', 'partial' => true];

        $this->notification()->success(
            title: 'Sukces',
            description: 'Słowo zostało dodane do listy banowanych.'
        );
    }

    public function render()
    {
        $reports = RatingReport::with('rating.user', 'rating.rateable')
            ->latest()
            ->paginate(12);

        return view('livewire.admin.reported-ratings', compact('reports'));
    }
}