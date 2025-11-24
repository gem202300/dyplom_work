<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->notifications()->latest();

        if ($request->type) {
            $query->where('data->type', $request->type);
        }

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $notifications = $query->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['status' => 'ok']);
    }

    public function markAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }
}
