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
    public $object_type_id = null;
    public ?int $userId = null;

    protected $paginationTheme = 'tailwind';

    public function mount($userId = null)
    {
        $this->userId = $userId ?? auth()->id();
    }

    public function updating()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Nocleg::with(['photos', 'objectType'])
            ->where('user_id', $this->userId); 

        if ($this->search) {
            $query->where(fn ($q) =>
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
            );
        }

        if ($this->object_type_id) {
            $query->where('object_type_id', $this->object_type_id);
        }

        return view('livewire.noclegi.my-noclegi-grid', [
            'noclegi' => $query->paginate(12),
            'types' => ObjectType::orderBy('name')->get(),
        ]);
    }
}
