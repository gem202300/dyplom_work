<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use Livewire\Component;
use App\Models\ObjectType;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class NoclegiGrid extends Component
{
    use WithPagination, WireUiActions;

    public $search = '';
    public $selectedTypeIds = [];
    public $selectedAmenities = []; // Додано для зручностей
    public $minCapacity = null;
    public $maxCapacity = null;
    public $minRating = null;
    public $maxRating = null;
    public $showFilters = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    public function resetFilters()
    {
        $this->reset([
            'search', 'selectedTypeIds', 'selectedAmenities',
            'minCapacity', 'maxCapacity', 'minRating', 'maxRating', 'sortBy'
        ]);
        $this->resetPage();
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedTypeIds() { $this->resetPage(); }
    public function updatingSelectedAmenities() { $this->resetPage(); }
    public function updatingMinCapacity() { $this->resetPage(); }
    public function updatingMaxCapacity() { $this->resetPage(); }
    public function updatingMinRating() { $this->resetPage(); }
    public function updatingMaxRating() { $this->resetPage(); }

    // Методи для типів
    public function toggleType($typeId)
    {
        if (in_array($typeId, $this->selectedTypeIds)) {
            $this->selectedTypeIds = array_filter(
                $this->selectedTypeIds, 
                fn($id) => $id != $typeId
            );
        } else {
            $this->selectedTypeIds[] = $typeId;
        }
    }

    public function removeType($typeId)
    {
        $this->selectedTypeIds = array_filter(
            $this->selectedTypeIds, 
            fn($id) => $id != $typeId
        );
    }

    // Методи для зручностей
    public function toggleAmenity($amenity)
    {
        if (in_array($amenity, $this->selectedAmenities)) {
            $this->selectedAmenities = array_filter(
                $this->selectedAmenities, 
                fn($a) => $a != $amenity
            );
        } else {
            $this->selectedAmenities[] = $amenity;
        }
    }

    public function removeAmenity($amenity)
    {
        $this->selectedAmenities = array_filter(
            $this->selectedAmenities, 
            fn($a) => $a != $amenity
        );
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function deleteNocleg($id)
    {
        $nocleg = Nocleg::find($id);
        if (!$nocleg) return;

        $nocleg->delete();

        $this->notification()->success(
            'Usunięto',
            "Nocleg \"{$nocleg->title}\" został usunięty."
        );
    }

    public function render()
    {
        $query = Nocleg::query()
            ->with(['photos', 'objectType'])
            ->select('noclegs.*')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->whereColumn('ratings.rateable_id', 'noclegs.id')
                    ->where('ratings.rateable_type', Nocleg::class);
            }, 'average_rating')
            ->where('status', 'approved');

        // Пошук (місто включено в пошук)
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                  ->orWhere('city', 'like', '%'.$this->search.'%')
                  ->orWhere('street', 'like', '%'.$this->search.'%')
                  ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        // Фільтр по типах
        if (!empty($this->selectedTypeIds)) {
            $query->whereIn('object_type_id', $this->selectedTypeIds);
        }

        // Фільтр по зручностям
        $amenityConditions = [
            'has_kitchen' => 'has_kitchen',
            'has_parking' => 'has_parking',
            'has_wifi' => 'has_wifi',
            'has_tv' => 'has_tv',
            'has_balcony' => 'has_balcony',
            'has_bathroom' => 'has_bathroom',
        ];
        
        foreach ($this->selectedAmenities as $amenity) {
            if (isset($amenityConditions[$amenity])) {
                $query->where($amenityConditions[$amenity], true);
            }
        }

        // Фільтр по місткості
        if (!is_null($this->minCapacity)) {
            $query->where('capacity', '>=', $this->minCapacity);
        }
        
        if (!is_null($this->maxCapacity)) {
            $query->where('capacity', '<=', $this->maxCapacity);
        }

        // Фільтр по рейтингу
        if (!is_null($this->minRating)) {
            $query->having('average_rating', '>=', $this->minRating);
        }
        
        if (!is_null($this->maxRating)) {
            $query->having('average_rating', '<=', $this->maxRating);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $noclegi = $query->paginate(12);
        $objectTypes = ObjectType::orderBy('name')->get();

        return view('livewire.noclegi.grid', [
            'noclegi' => $noclegi,
            'objectTypes' => $objectTypes,
        ]);
    }
}