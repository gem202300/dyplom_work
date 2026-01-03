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

        return redirect()->route('admin.noclegi.index')->with('success', 'Nocleg zatwierdzony.');
    }

    public function reject(Request $request, Nocleg $nocleg)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:1'
        ]);

        $nocleg->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reason']
        ]);

        if ($nocleg->user) {
            $nocleg->user->notify(new TestNotification(
                'Twój nocleg został odrzucony',
                "Twój nocleg \"{$nocleg->title}\" został odrzucony przez administratora.\n\nPowód: " . $validated['reason']
            ));
        }

        return redirect()->route('admin.noclegi.index')->with('success', 'Nocleg odrzucony.');
    }
}