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

    public $search = '';
    public $object_type_id = null;

    protected $paginationTheme = 'tailwind';

    public function updating()
    {
        $this->resetPage();
    }

    public function approveNocleg($id)
    {
        $nocleg = Nocleg::findOrFail($id);
        $nocleg->update(['status' => 'approved']);

        $nocleg->user?->notify(new TestNotification(
            'Nocleg zatwierdzony',
            "Twój nocleg \"{$nocleg->title}\" został zatwierdzony."
        ));

        $this->notification()->success('Zatwierdzono', $nocleg->title);
    }

    public function rejectNocleg($id)
    {
        $nocleg = Nocleg::findOrFail($id);
        $nocleg->update(['status' => 'rejected']);

        $nocleg->user?->notify(new TestNotification(
            'Nocleg odrzucony',
            "Twój nocleg \"{$nocleg->title}\" został odrzucony."
        ));

        $this->notification()->success('Odrzucono', $nocleg->title);
    }

    public function render()
    {
        $query = Nocleg::with(['photos', 'objectType', 'user'])
            ->where('status', 'pending');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->object_type_id) {
            $query->where('object_type_id', $this->object_type_id);
        }

        return view('livewire.noclegi.admin-noclegi-grid', [
            'noclegi' => $query->paginate(12),
            'types' => ObjectType::orderBy('name')->get(),
        ]);
    }
}
