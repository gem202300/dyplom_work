<?php

namespace App\Livewire\Attractions;
use Livewire\Component;
use App\Models\Category;
use App\Models\Attraction;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

use WireUi\Traits\WireUiActions;
class AttractionsGrid extends Component
{
    use WithPagination;
    use WireUiActions;
    #[Url]
    public $focusAttractionId = null;
    public $search = '';
    public $selectedCategories = [];
    public $minRating = 0; 
    public $maxRating = 5; 
    public $showFilters = false;
    public $hasRatingError = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategories' => ['except' => []],
        'minRating' => ['except' => 0], 
        'maxRating' => ['except' => null], 
    ];
    
    protected $rules = [
        'minRating' => ['nullable', 'numeric', 'min:0', 'max:5'],
        'maxRating' => ['nullable', 'numeric', 'min:0', 'max:5'],
    ];
    
    protected function validateRating()
    {
        $this->hasRatingError = false;
        
        if ($this->minRating !== null && $this->maxRating !== null) {
            if ((float)$this->minRating > (float)$this->maxRating) {
                $this->hasRatingError = true;
                return false;
            }
        }
        
        return true;
    }
    
    public function updatedMinRating($value)
    {
        $this->validateOnly('minRating');
        $this->validateRating();
    }
    
    public function updatedMaxRating($value)
    {
        $this->validateOnly('maxRating');
        $this->validateRating();
    }
    
    public function render()
    {
        $query = Attraction::with(['photos', 'categories']);
        
        if (!auth()->check()) {
            $query->where('is_active', true);
        }
        
        // Пошук
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }
        
        // Фільтр по категоріям
        if (!empty($this->selectedCategories)) {
            $query->whereHas('categories', function($q) {
                $q->whereIn('categories.id', $this->selectedCategories);
            });
        }
        
        if ($this->minRating !== null && $this->minRating > 0) {
            $query->where('rating', '>=', $this->minRating);
        }
        
        if ($this->maxRating !== null) {
            $query->where('rating', '<=', $this->maxRating);
        }
        
        $attractions = $query->orderBy('name')->paginate(12);
        $categories = Category::orderBy('name')->get();
        
        return view('livewire.attractions.grid', [
            'attractions' => $attractions,
            'categories' => $categories,
        ]);
    }
    
    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_diff($this->selectedCategories, [$categoryId]);
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->resetPage();
    }
    public function mount()
    {
        if ($this->focusAttractionId) {
            $attraction = Attraction::find($this->focusAttractionId);
            if (!$attraction) {
                $this->focusAttractionId = null;
            }
        }
    }
    public function showOnMap($attractionId)
    {
        $attraction = Attraction::find($attractionId);
        
        if (!$attraction || !$attraction->latitude || !$attraction->longitude) {
            $this->notification()->warning(
                title: 'Uwaga',
                description: 'Ta atrakcja nie ma współrzędnych na mapie.'
            );
            return;
        }
        
        return redirect()->route('map.index', [
            'focus' => $attractionId,
            'type' => 'attraction'
        ]);
    }
    public function removeCategory($categoryId)
    {
        $this->selectedCategories = array_diff($this->selectedCategories, [$categoryId]);
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategories = [];
        $this->minRating = 0; 
        $this->maxRating = null; 
        $this->hasRatingError = false;
        $this->resetPage();
    }
    
    public function applyFilters()
    {
        // Валідація перед застосуванням
        if (!$this->validateRating()) {
            session()->flash('error', 'Min ocena nie może być większa niż max ocena.');
            return;
        }
        
        $this->showFilters = false;
    }
    
    public function toggleActive($attractionId)
    {
        if (!auth()->check()) {
            return;
        }
        
        $attraction = Attraction::findOrFail($attractionId);
        $attraction->is_active = !$attraction->is_active;
        $attraction->save();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Status atrakcji został zmieniony.'
        ]);
    }
    public function attemptDelete($attractionId)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return;
        }

        $attraction = Attraction::findOrFail($attractionId);

        $this->dialog()->confirm([
            'title'       => 'Czy na pewno chcesz usunąć atrakcję?',
            'description' => "Atrakcja \"{$attraction->name}\" zostanie trwale usunięta.",
            'acceptLabel' => 'Tak, usuń',
            'rejectLabel' => 'Anuluj',
            'method'      => 'deleteConfirmed',
            'params'      => $attractionId,
        ]);
    }
    public function deleteConfirmed($attractionId)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return;
        }

        $attraction = Attraction::findOrFail($attractionId);

        $attraction->categories()->detach();
        $attraction->delete();

        $this->notification()->success(
            title: 'Sukces',
            description: 'Atrakcja została usunięta.'
        );

        $this->resetPage();
    }
}