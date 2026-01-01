<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Notifications extends Component
{
    public function render()
    {
        return view('components.notifications');
    }
}
