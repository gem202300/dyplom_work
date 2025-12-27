<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use Livewire\Component;
use App\Models\ObjectType;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class MyNoclegiGrid extends Component
{
    use WithPagination, WireUiActions;

    public $search = '';
    public $selectedTypeIds = [];
    public $selectedAmenities = [];
    public $minCapacity = null;
    public $maxCapacity = null;
    public $minRating = null;
    public $maxRating = null;
    public $showFilters = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public ?int $userId = null;

    protected $paginationTheme = 'tailwind';

    public function mount($userId = null)
    {
        $this->userId = $userId ?? auth()->id();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingSelectedTypeIds()
    {
        $this->resetPage();
    }
    public function updatingSelectedAmenities()
    {
        $this->resetPage();
    }
    public function updatingMinCapacity()
    {
        $this->resetPage();
    }
    public function updatingMaxCapacity()
    {
        $this->resetPage();
    }
    public function updatingMinRating()
    {
        $this->resetPage();
    }
    public function updatingMaxRating()
    {
        $this->resetPage();
    }

    public function toggleType(int $typeId): void
    {
        if (in_array($typeId, $this->selectedTypeIds)) {
            $this->selectedTypeIds = array_values(
                array_diff($this->selectedTypeIds, [$typeId])
            );
        } else {
            $this->selectedTypeIds[] = $typeId;
        }
    }

    public function removeType(int $typeId): void
    {
        $this->selectedTypeIds = array_filter(
            $this->selectedTypeIds,
            fn($id) => $id != $typeId
        );
    }

    public function toggleAmenity(string $amenity): void
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

    public function removeAmenity(string $amenity): void
    {
        $this->selectedAmenities = array_filter(
            $this->selectedAmenities,
            fn($a) => $a != $amenity
        );
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'selectedTypeIds',
            'selectedAmenities',
            'minCapacity',
            'maxCapacity',
            'minRating',
            'maxRating',
            'sortBy'
        ]);

        $this->resetPage();
    }

    public function deleteNocleg($noclegId)
    {
        $nocleg = Nocleg::where('id', $noclegId)
            ->where('user_id', $this->userId)
            ->firstOrFail();

        $this->dialog()->confirm([
            'title'       => __('noclegi.delete.confirm_title'),
            'description' => __('noclegi.delete.confirm_description', ['title' => $nocleg->title]),
            'acceptLabel' => __('noclegi.delete.confirm_accept'),
            'rejectLabel' => __('noclegi.delete.confirm_reject'),
            'method'      => 'confirmDeleteNocleg',
            'params'      => $noclegId,
        ]);
    }

    public function confirmDeleteNocleg($noclegId)
    {
        $nocleg = Nocleg::where('id', $noclegId)
            ->where('user_id', $this->userId)
            ->firstOrFail();
        $nocleg->delete();

        $this->notification()->success(
            title: __('noclegi.messages.success'),
            description: __('noclegi.messages.deleted', ['title' => $nocleg->title])
        );
    }
    public function resubmit($id)
    {
        $nocleg = Nocleg::findOrFail($id);

        if ($nocleg->user_id !== auth()->id()) {
            abort(403);
        }

        return $this->redirect(route('noclegi.edit', $id));
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
            ->where('user_id', $this->userId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%')
                    ->orWhere('street', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->selectedTypeIds)) {
            $query->whereIn('object_type_id', $this->selectedTypeIds);
        }

        foreach ($this->selectedAmenities as $amenity) {
            $query->where($amenity, true);
        }

        if (!is_null($this->minCapacity)) {
            $query->where('capacity', '>=', $this->minCapacity);
        }

        if (!is_null($this->maxCapacity)) {
            $query->where('capacity', '<=', $this->maxCapacity);
        }

        if (!is_null($this->minRating)) {
            $query->having('average_rating', '>=', $this->minRating);
        }

        if (!is_null($this->maxRating)) {
            $query->having('average_rating', '<=', $this->maxRating);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.noclegi.my-noclegi-grid', [
            'noclegi' => $query->paginate(12),
            'types' => ObjectType::orderBy('name')->get(),
        ]);
    }
}
