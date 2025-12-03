<?php

namespace App\Http\Controllers;

use App\Models\Nocleg;
use Illuminate\Http\Request;
use App\Notifications\TestNotification;

class AdminNoclegController extends Controller
{
    public function approve(Nocleg $nocleg)
    {
        $nocleg->update(['status' => 'approved']);

        if ($nocleg->user) {
            $nocleg->user->notify(new TestNotification(
                'Twój nocleg został zatwierdzony',
                "Twój nocleg \"{$nocleg->title}\" został zatwierdzony przez administratora."
            ));
        }

        return back()->with('success', 'Nocleg zatwierdzony.');
    }

    public function reject(Nocleg $nocleg)
    {
        $nocleg->update(['status' => 'rejected']);

        if ($nocleg->user) {
            $nocleg->user->notify(new TestNotification(
                'Twój nocleg został odrzucony',
                "Twój nocleg \"{$nocleg->title}\" został odrzucony przez administratora."
            ));
        }

        return back()->with('success', 'Nocleg odrzucony.');
    }
}
