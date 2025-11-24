<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $count = 0;

    protected $listeners = [
        'notification-added' => 'updateCount',
        'notification-read' => 'updateCount'
    ];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->count = auth()->user()->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
