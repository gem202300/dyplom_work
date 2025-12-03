<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use App\Notifications\TestNotification;

class AdminNoclegiGrid extends Component
{
    use WithPagination, WireUiActions;

    public $search = '';
    public $type = '';
    public $showFilters = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function approveNocleg($id)
    {
        $nocleg = Nocleg::find($id);
        if (!$nocleg) return;

        $nocleg->update(['status' => 'approved']);

        if ($nocleg->user) {
            $nocleg->user->notify(new TestNotification(
                'Twój nocleg został zatwierdzony',
                "Twój nocleg \"{$nocleg->title}\" został zatwierdzony przez administratora."
            ));
        }

        $this->notification()->success(
            'Zatwierdzono',
            "Nocleg \"{$nocleg->title}\" został zatwierdzony."
        );
    }

    public function rejectNocleg($id)
    {
        $nocleg = Nocleg::find($id);
        if (!$nocleg) return;

        $nocleg->update(['status' => 'rejected']);

        if ($nocleg->user) {
            $nocleg->user->notify(new TestNotification(
                'Twój nocleg został odrzucony',
                "Nocleg \"{$nocleg->title}\" został odrzucony."
            ));
        }

        $this->notification()->success(
            'Odrzucono',
            "Nocleg \"{$nocleg->title}\" został odrzucony."
        );
    }

    public function render()
    {
        $query = Nocleg::with('photos', 'user')
            ->where('status', 'pending');

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

        return view('livewire.noclegi.admin-noclegi-grid', [
            'noclegi' => $query->paginate(12),
        ]);
    }
}
