<?php

namespace App\Livewire\Attractions;

use Livewire\Component;
use App\Models\Category;
use App\Models\Attraction;
use Livewire\WithPagination;

class AttractionsGrid extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedCategories = [];
    public $minRating = 0; // ЗМІНА: за замовчуванням 0
    public $maxRating = 5; // ЗМІНА: за замовчуванням null (не 5)
    public $showFilters = false;
    public $hasRatingError = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategories' => ['except' => []],
        'minRating' => ['except' => 0], // ЗМІНА: 0 за замовчуванням
        'maxRating' => ['except' => null], // ЗМІНА: null за замовчуванням
    ];
    
    // Правила валідації
    protected $rules = [
        'minRating' => ['nullable', 'numeric', 'min:0', 'max:5'],
        'maxRating' => ['nullable', 'numeric', 'min:0', 'max:5'],
    ];
    
    // Валідація рейтингу
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
    
    // Слухачі для валідації в реальному часі
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
        
        // Для неавторизованих користувачів показуємо тільки активні
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
        
        // Фільтр по рейтингу
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
    
    public function removeCategory($categoryId)
    {
        $this->selectedCategories = array_diff($this->selectedCategories, [$categoryId]);
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategories = [];
        $this->minRating = 0; // ЗМІНА: скидаємо на 0
        $this->maxRating = null; // ЗМІНА: скидаємо на null
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
        // Перевірка авторизації
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
    
    public function deleteAttraction($attractionId)
    {
        // Перевірка авторизації
        if (!auth()->check()) {
            return;
        }
        
        $attraction = Attraction::findOrFail($attractionId);
        $attraction->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Atrakcja została usunięta.'
        ]);
    }
}