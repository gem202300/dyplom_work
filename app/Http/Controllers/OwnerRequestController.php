<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Auth\RoleType;
use App\Models\OwnerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\TestNotification;

class OwnerRequestController extends Controller
{
    public function form()
    {
        return view('owner.request-form');
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        $lastRequest = OwnerRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($lastRequest && $lastRequest->status === 'rejected' && !$lastRequest->can_resubmit) {
            return back()->withErrors([
                'error' => 'Administrator zablokował możliwość ponownego złożenia wniosku.',
            ]);
        }

        $request->validate([
            'phone' => ['required', 'regex:/^\+?[0-9]{6,15}$/'],
            'reason' => 'required|string|min:10',
            'terms' => 'accepted'
        ]);

        $user->update(['phone' => $request->phone]);

        if ($lastRequest && $lastRequest->status === 'rejected') {
            $lastRequest->update([
                'phone' => $request->phone,
                'reason' => $request->reason,
                'status' => 'pending',
                'rejection_reason' => null,
                'can_resubmit' => true,
            ]);

            return back()->with('status', 'Wniosek został wysłany ponownie.');
        }

        $ownerRequest = OwnerRequest::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'reason' => $request->reason,
            'accepted_terms' => true,
        ]);

        $admins = User::role(RoleType::ADMIN->value)->get();
        foreach ($admins as $admin) {
            $admin->notify(new TestNotification(
                'Nowy wniosek o rolę właściciela',
                "Użytkownik {$user->name} złożył wniosek o nadanie roli właściciela obiektu.\nWniosek zawiera dane kontaktowe oraz uzasadnienie. Prosimy o jego weryfikację."
            ));
        }

        return back()->with('status', 'Wniosek został wysłany do administratora.');
    }

    public function approve(OwnerRequest $ownerRequest)
    {
        $ownerRequest->update(['status' => 'approved']);
        $ownerRequest->user->assignRole('owner');

        $ownerRequest->user->notify(new TestNotification(
            'Wniosek zaakceptowany',
            "Twoja prośba o nadanie roli właściciela została zaakceptowana przez administratora.\nOd teraz masz dostęp do funkcji właściciela i możesz zarządzać swoim obiektem w systemie."
        ));

        return back()->with('message', 'Wniosek zatwierdzony.');
    }

    public function reject(Request $request, OwnerRequest $ownerRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $ownerRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'can_resubmit' => $request->boolean('can_resubmit'),
        ]);

        $editUrl = $ownerRequest->can_resubmit ? route('owner.request.form') : null;

        DB::table('notifications')
            ->where('notifiable_type', get_class($ownerRequest->user))
            ->where('notifiable_id', $ownerRequest->user->id)
            ->where('type', TestNotification::class)
            ->whereJsonContains('data->can_resubmit', true)
            ->update(['data->can_resubmit' => false]);

        $message = $ownerRequest->can_resubmit
            ? "Twoja prośba o nadanie roli właściciela została odrzucona przez administratora.\n\nPowód odrzucenia:\n{$request->rejection_reason}\n\nMożesz poprawić dane i wysłać wniosek ponownie."
            : "Twoja prośba o nadanie roli właściciela została odrzucona przez administratora.\n\nPowód odrzucenia:\n{$request->rejection_reason}\n\nMożliwość ponownego złożenia wniosku została zablokowana.";

        $ownerRequest->user->notify(new TestNotification(
            'Wniosek odrzucony',
            $message,
            $editUrl,
            $ownerRequest->can_resubmit
        ));

        return back()->with('message', 'Wniosek odrzucony.');
    }

    public function show(OwnerRequest $ownerRequest)
    {
        return view('admin.owner-requests.show', compact('ownerRequest'));
    }
}