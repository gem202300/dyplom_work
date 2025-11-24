<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Auth\RoleType;
use App\Models\OwnerRequest;
use Illuminate\Http\Request;
use App\Notifications\TestNotification;

class OwnerRequestController extends Controller
{
    public function form()
    {
        return view('owner.request-form');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:6',
            'reason' => 'required|string|min:10',
            'terms' => 'accepted'
        ]);

        OwnerRequest::create([
            'user_id' => auth()->id(),
            'phone' => $request->phone,
            'reason' => $request->reason,
            'accepted_terms' => true,
        ]);
        $admins = \App\Models\User::role(\App\Enums\Auth\RoleType::ADMIN->value)->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\TestNotification(
                'Nowa prośba o rolę właściciela',
                'Użytkownik ' . $request->user()->name . ' (' . $request->user()->username . ') wysłał wniosek o rolę właściciela.'
            ));
        }


        return redirect()->back()->with('status', 'Wniosek został wysłany do administratora.');
    }
    public function approve(OwnerRequest $ownerRequest)
    {
        $ownerRequest->update(['status' => 'approved']);
        $ownerRequest->user->assignRole('owner');

        $ownerRequest->user->notify(new \App\Notifications\TestNotification(
            'Twoja prośba została zaakceptowana',
            'Twoja prośba o rolę właściciela została zatwierdzona przez administratora.'
        ));

        session()->flash('message', 'Wniosek zatwierdzony.');
        return back();
    }

    public function reject(OwnerRequest $ownerRequest)
    {
        $ownerRequest->update(['status' => 'rejected']);

        $ownerRequest->user->notify(new \App\Notifications\TestNotification(
            'Twoja prośba została odrzucona',
            'Twoja prośba o rolę właściciela została odrzucona przez administratora.'
        ));

        session()->flash('message', 'Wniosek odrzucony.');
        return back();
    }
    public function show(OwnerRequest $owner_request)
    {
        return view('admin.owner-requests.show', compact('owner_request'));
    }
}
