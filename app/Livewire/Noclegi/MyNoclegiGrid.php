<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class MyNoclegiGrid extends Component
{
    use WithPagination, WireUiActions;

    public $search = '';
    public $type = '';
    public $showFilters = false;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingType() { $this->resetPage(); }

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
        $query = Nocleg::with('photos')
            ->where('user_id', auth()->id()); 

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
                  ->orWhere('street', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $query->where('object_type', $this->type);
        }

        return view('livewire.noclegi.my-noclegi-grid', [
            'noclegi' => $query->paginate(12),
        ]);
    }
}
