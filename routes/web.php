<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NoclegController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\MyNoclegiController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\AdminNoclegController;
use App\Http\Controllers\OwnerRequestController;
use App\Http\Controllers\RatingReportController;
use App\Http\Controllers\CategoryDeleteController;
use App\Http\Controllers\NoclegCalendarController;

Route::get('/', fn() => view('welcome'));

Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map-data', [App\Http\Controllers\MapDataController::class, 'index'])->name('map.data');
Route::get('/api/object-types', function () {
    return \App\Models\ObjectType::select('id', 'name')->get();
})->name('api.object-types');

Route::get('/api/categories', function () {
    return \App\Models\Category::select('id', 'name')->get();
})->name('api.categories');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/attractions', fn() => view('attractions.index'))->name('attractions.index');

    Route::prefix('attractions')->name('attractions.')->group(function () {
        Route::get('/', [AttractionController::class, 'index'])->name('index');
        Route::get('/create', [AttractionController::class, 'create'])->name('create');
        Route::get('/{attraction}/edit', [AttractionController::class, 'edit'])->name('edit');
        Route::get('/{attraction}', [AttractionController::class, 'show'])->name('show');
    });

    Route::prefix('noclegi')->name('noclegi.')->group(function () {
        Route::get('/', fn() => view('noclegi.index'))->name('index');
        Route::get('/create', [NoclegController::class, 'create'])->name('create');
        Route::get('/{nocleg}', [NoclegController::class, 'show'])->name('show');
        Route::get('/{nocleg}/edit', [NoclegController::class, 'edit'])->name('edit');
        Route::get('/{nocleg}/calendar', [NoclegCalendarController::class, 'index'])->name('calendar');
        Route::post('/{nocleg}/calendar', [NoclegCalendarController::class, 'update'])->name('calendar.update');
    });

    Route::get('/my-noclegi', [MyNoclegiController::class, 'index'])->name('my-noclegi');

    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::post('/ratings/{rating}/report', [RatingController::class, 'report'])->name('ratings.report');
    Route::get('/ratings/filter/{rateableType}/{rateableId}', [RatingController::class, 'filter'])->name('ratings.filter');

    Route::get('/notifications', function () {
        $query = auth()->user()->notifications();
        if (request('from')) $query->whereDate('created_at', '>=', request('from'));
        if (request('to')) $query->whereDate('created_at', '<=', request('to'));
        $notifications = $query->latest()->paginate(10)->withQueryString();
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::get('/notifications/{id}', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        return view('notifications.show', compact('notification'));
    })->name('notifications.show');

    Route::post('/notifications/read/{id}', fn($id) => auth()->user()->notifications()->find($id)?->markAsRead());
    Route::post('/notifications/{id}/read', fn($id) => auth()->user()->notifications()->find($id)?->markAsRead());
    Route::post('/notifications/read-all', fn() => auth()->user()->unreadNotifications->markAsRead());

    Route::get('/owner/request', [OwnerRequestController::class, 'form'])->name('owner.request.form');
    Route::post('/owner/request', [OwnerRequestController::class, 'submit'])->name('owner.request.submit');
});

Route::middleware(['auth', 'can:admin-access'])
    ->get('/users', [UserController::class, 'index'])
    ->name('users.index');

Route::middleware(['auth', 'can:admin-access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/noclegi', fn() => view('admin.noclegi.index'))->name('noclegi.index');
        Route::post('/noclegi/{nocleg}/approve', [AdminNoclegController::class, 'approve'])->name('noclegi.approve');
        Route::post('/noclegi/{nocleg}/reject', [AdminNoclegController::class, 'reject'])->name('noclegi.reject');
        Route::get('/noclegi/{nocleg}/details', [NoclegController::class, 'details'])->name('noclegi.details');

        Route::get('/ratings/reports', fn() => view('admin.ratings-reports'))->name('ratings.reports');
        Route::get('/reports/{rating}', [RatingReportController::class, 'details'])->name('ratings.report.details');
        Route::post('/ratings/{rating}/delete', [RatingReportController::class, 'delete'])->name('ratings.delete');
        Route::post('/ratings/{rating}/clear-reports', [RatingReportController::class, 'clearReports'])->name('ratings.clear-reports');

        Route::get('/owner-requests', fn() => view('admin.owner-requests.index'))->name('owner-requests.index');
        Route::get('/owner-requests/{owner_request}', [OwnerRequestController::class, 'show'])->whereNumber('owner_request')->name('owner-requests.show');
        Route::post('/owner-requests/{ownerRequest}/approve', [OwnerRequestController::class, 'approve'])->name('owner-requests.approve');
        Route::post('/owner-requests/{ownerRequest}/reject', [OwnerRequestController::class, 'reject'])->name('owner-requests.reject');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/{user}/noclegi', fn(\App\Models\User $user) => view('admin.users.noclegi', compact('user')))->name('noclegi');
        });
    });

Route::middleware(['auth', 'can:admin-access'])
    ->post('/admin/banned-words/store', [App\Http\Controllers\BannedWordController::class, 'store'])
    ->name('banned-words.store');

Route::middleware(['auth', 'can:admin-access'])
    ->prefix('categories')
    ->name('categories.')
    ->group(function () {
        Route::get('/', fn() => view('categories.index'))->name('index');
        Route::get('/create', fn() => view('categories.create'))->name('create');
        Route::get('/{category}/edit', fn(Category $category) => view('categories.edit', compact('category')))->name('edit');
        Route::get('/{category}/delete', [CategoryDeleteController::class, 'show'])->name('delete-form');
        Route::post('/{category}/delete', [CategoryDeleteController::class, 'destroy'])->name('delete');
    });
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
});