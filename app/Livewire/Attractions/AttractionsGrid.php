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
    public $showFilters = false;
    
    public function resetFilters()
    {
        $this->reset(['search', 'selectedCategories', 'minRating', 'maxRating']);
        $this->resetPage();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedCategories() { $this->resetPage(); }
    public function updatingMinRating() { $this->resetPage(); }
    public function updatingMaxRating() { $this->resetPage(); }

    public function render()
    {
        // Створюємо підзапит для середньої оцінки
        $query = Attraction::query()
            ->with('photos', 'categories')
            ->select('attractions.*')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->whereColumn('ratings.rateable_id', 'attractions.id')
                    ->where('ratings.rateable_type', Attraction::class);
            }, 'average_rating');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('location', 'like', '%'.$this->search.'%')
                  ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if (!empty($this->selectedCategories)) {
            $query->whereHas('categories', function($q) {
                $q->whereIn('categories.id', $this->selectedCategories);
            });
        }

        // Фільтрація за середньою оцінкою
        if (!is_null($this->minRating)) {
            $query->having('average_rating', '>=', $this->minRating);
        }
        
        if (!is_null($this->maxRating)) {
            $query->having('average_rating', '<=', $this->maxRating);
        }

        $attractions = $query->paginate(12);

        return view('livewire.attractions.grid', [
            'attractions' => $attractions,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_filter(
                $this->selectedCategories, 
                fn($id) => $id != $categoryId
            );
        } else {
            $this->selectedCategories[] = $categoryId;
        }
    }

    public function removeCategory($categoryId)
    {
        $this->selectedCategories = array_filter(
            $this->selectedCategories, 
            fn($id) => $id != $categoryId
        );
    }

    public function deleteAttraction($id): void
    {
        $attraction = Attraction::findOrFail($id);
        $attraction->delete();

        $this->notification()->success(
            'Sukces',
            "Atrakcja \"{$attraction->name}\" została usunięta."
        );
    }
}