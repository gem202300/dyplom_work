<?php

use App\Models\Category;
use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AttractionController;
use App\Http\Livewire\Admin\OwnerRequestTable;

use App\Http\Controllers\OwnerRequestController;
Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/attractions', function () {
        return view('attractions.index');
    })->name('attractions.index');

    Route::middleware(['auth'])->prefix('attractions')->name('attractions.')->group(function () {
        Route::get('/', [AttractionController::class, 'index'])->name('index');
        Route::get('/create', [AttractionController::class, 'create'])->name('create');
        Route::get('/{attraction}/edit', [AttractionController::class, 'edit'])->name('edit');
    });

    Route::get('/attractions/{attraction}', [AttractionController::class, 'show'])->name('attractions.show');
    Route::post('/ratings', [\App\Http\Controllers\RatingController::class, 'store'])
        ->middleware('auth')
        ->name('ratings.store');
    Route::post('/notifications/read/{id}', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'ok']);
    });

    Route::post('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'ok']);
    });
    Route::post('/admin/owner-requests/{ownerRequest}/approve',
        [\App\Http\Controllers\OwnerRequestController::class, 'approve']
    )->name('admin.owner-requests.approve');

    Route::post('/admin/owner-requests/{ownerRequest}/reject',
        [\App\Http\Controllers\OwnerRequestController::class, 'reject']
    )->name('admin.owner-requests.reject');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'ok']);
    });

    Route::get('/notifications', function () {
        $query = auth()->user()->notifications();

        $from = request('from');
        $to = request('to');
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $notifications = $query->latest()->paginate(10)->withQueryString();
        return view('notifications.index', compact('notifications'));
    })->middleware('auth')->name('notifications.index');

    Route::get('/notifications/{id}', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        return view('notifications.show', compact('notification'));
    })->name('notifications.show');
    Route::middleware(['auth'])->group(function () {

        Route::get('/owner/request', [
            \App\Http\Controllers\OwnerRequestController::class, 'form'
        ])->name('owner.request.form');

        Route::post('/owner/request', [
            \App\Http\Controllers\OwnerRequestController::class, 'submit'
        ])->name('owner.request.submit');

    });
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin/owner-requests', function () {
            return view('admin.owner-requests.index');
        })->name('admin.owner-requests.index');
    });
   
Route::middleware(['auth'])->prefix('admin/owner-requests')->name('admin.owner-requests.')->group(function () {
    
    Route::get('/', function () {
        return view('admin.owner-requests.index');
    })->name('index');

    Route::get('/{owner_request}', [OwnerRequestController::class, 'show'])
        ->whereNumber('owner_request')
        ->name('show');
});





    Route::middleware(['auth'])->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', fn() => view('categories.index'))->name('index');
        Route::get('/create', fn() => view('categories.create'))->name('create');
        Route::get('/{category}/edit', fn(Category $category) => view('categories.edit', compact('category')))->name('edit');
    });
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
