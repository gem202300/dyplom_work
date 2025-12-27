<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use Livewire\Component;
use App\Models\ObjectType;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use App\Notifications\TestNotification;

class AdminNoclegiGrid extends Component
{
    use WithPagination, WireUiActions;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public array $selectedTypeIds = [];
    public array $selectedAmenities = [];
    public $minCapacity = null;
    public $maxCapacity = null;
    public $minRating = null;
    public $maxRating = null;
    public bool $showFilters = false;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public bool $rejectModal = false;
    public ?int $currentNoclegId = null;
    public string $rejectReason = '';

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

    public function openRejectModal(int $id): void
    {
        $this->currentNoclegId = $id;
        $this->rejectReason = '';
        $this->rejectModal = true;
    }

    public function approveNocleg(int $id): void
    {
        $nocleg = Nocleg::with('user')->findOrFail($id);

        $nocleg->update([
            'status' => 'approved',
            'reject_reason' => null,
        ]);

        $nocleg->user?->notify(new TestNotification(
            'Nocleg zatwierdzony',
            "Twój obiekt „{$nocleg->title}” został zatwierdzony."
        ));

        $this->notification()->success('Zatwierdzono', $nocleg->title);
    }

    public function rejectNocleg(): void
    {
        $this->validate([
            'rejectReason' => 'required|min:1',
        ], [
            'rejectReason.required' => 'Podaj uzasadnienie odrzucenia.',
            'rejectReason.min' => 'Aby odrzucić obiekt, musisz podać uzasadnienie decyzji.',
        ]);

        $nocleg = Nocleg::with('user')->findOrFail($this->currentNoclegId);

        $nocleg->update([
            'status' => 'rejected',
            'reject_reason' => $this->rejectReason,
        ]);

        $nocleg->user?->notify(new TestNotification(
            'Nocleg odrzucony',
            "Twój obiekt „{$nocleg->title}” został odrzucony.\n\nPowód:\n{$this->rejectReason}"
        ));

        $this->notification()->success('Odrzucono', $nocleg->title);

        $this->reset([
            'rejectModal',
            'currentNoclegId',
            'rejectReason',
        ]);
    }

    public function render()
    {
        $query = Nocleg::query()
            ->with(['photos', 'objectType', 'user'])
            ->select('noclegs.*')
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->whereColumn('ratings.rateable_id', 'noclegs.id')
                    ->where('ratings.rateable_type', Nocleg::class);
            }, 'average_rating')
            ->whereIn('status', ['pending', 'rejected']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('city', 'like', "%{$this->search}%")
                    ->orWhere('street', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
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

        return view('livewire.noclegi.admin-noclegi-grid', [
            'noclegi' => $query->paginate(12),
            'types' => ObjectType::orderBy('name')->get(),
        ]);
    }
}
