<?php

namespace App\Livewire\Attractions;

use Livewire\Component;
use App\Models\Category;
use App\Models\Attraction;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class AttractionsGrid extends Component
{
    use WithPagination, WireUiActions;

    public $search = '';
    public $selectedCategories = [];
    public $minRating = null;
    public $maxRating = null;
    public $showFilters = false; // модалка

    protected $listeners = [
        'deleteAttractionAction' => 'deleteAttractionAction',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedCategories() { $this->resetPage(); }
    public function updatingMinRating() { $this->resetPage(); }
    public function updatingMaxRating() { $this->resetPage(); }

    public function deleteAttractionAction($data)
    {
        $id = $data['attraction'];
        $this->dialog()->confirm([
            'title' => 'Підтвердьте видалення',
            'description' => "Ви впевнені, що хочете видалити цю атракцію?",
            'icon' => 'warning',
            'accept' => [
                'label' => 'Так',
                'method' => 'destroy',
                'params' => $id,
            ],
            'reject' => [
                'label' => 'Ні',
            ],
        ]);
    }

    public function destroy($id)
    {
        Attraction::findOrFail($id)->delete();
        $this->notification()->success('Успіх', 'Атракція видалена.');
    }

    public function render()
    {
        $query = Attraction::query()
            ->with('photos', 'categories')
            ->withAvg('ratings', 'rating');

        // Фільтр по назві та локації
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('location', 'like', '%'.$this->search.'%');
            });
        }

        // Фільтр по категоріях
        if (!empty($this->selectedCategories)) {
            $query->whereHas('categories', fn($q) => $q->whereIn('id', $this->selectedCategories));
        }

        // Фільтр по рейтингу
        if (!is_null($this->minRating)) {
            $query->where('ratings_avg_rating', '>=', $this->minRating);
        }
        if (!is_null($this->maxRating)) {
            $query->where('ratings_avg_rating', '<=', $this->maxRating);
        }

        return view('livewire.attractions.grid', [
            'attractions' => $query->paginate(12),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
