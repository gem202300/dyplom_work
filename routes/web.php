<?php
use App\Models\Category;
use App\Http\Controllers;
use App\Livewire\Noclegi\NoclegiGrid;
use Illuminate\Support\Facades\Route;
use App\Livewire\Noclegi\NoclegiTable;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NoclegController;
use App\Http\Controllers\RatingController;
use App\Http\Livewire\Admin\ReportedRatings;
use App\Http\Controllers\MyNoclegiController;
use App\Http\Controllers\AttractionController;
use App\Http\Livewire\Admin\OwnerRequestTable;
use App\Http\Controllers\AdminNoclegController;
use App\Http\Controllers\OwnerRequestController;
use App\Http\Controllers\RatingReportController;
use App\Http\Controllers\NoclegCalendarController;

Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/attractions', function () {
        return view('attractions.index');
    })->name('attractions.index');
    Route::middleware(['auth'])->group(function () {
        Route::get('/my-noclegi', [MyNoclegiController::class, 'index'])->name('my-noclegi');
    });

Route::middleware(['auth'])->prefix('admin/noclegi')->name('admin.noclegi.')->group(function () {
    Route::get('/', fn() => view('admin.noclegi.index'))->name('index');
});

Route::post('/admin/noclegi/{nocleg}/approve', [AdminNoclegController::class, 'approve'])
    ->name('admin.noclegi.approve');
Route::post('/admin/noclegi/{nocleg}/reject', [AdminNoclegController::class, 'reject'])
    ->name('admin.noclegi.reject');
Route::get('/admin/noclegi/{nocleg}/details', [NoclegController::class, 'details'])
    ->name('admin.noclegi.details');

Route::prefix('noclegi')->name('noclegi.')->group(function () {
    Route::get('/', function() { return view('noclegi.index'); })->name('index');
    Route::get('/create', [NoclegController::class, 'create'])->name('create');
    Route::get('/{nocleg}', [NoclegController::class, 'show'])->name('show');
    Route::get('/{nocleg}/edit', [NoclegController::class, 'edit'])->name('edit');
});
    Route::middleware(['auth'])->prefix('attractions')->name('attractions.')->group(function () {
        Route::get('/', [AttractionController::class, 'index'])->name('index');
        Route::get('/create', [AttractionController::class, 'create'])->name('create');
        Route::get('/{attraction}/edit', [AttractionController::class, 'edit'])->name('edit');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/ratings/reports', function() {
        return view('admin.ratings-reports'); 
    })->name('admin.ratings.reports');
});
Route::get('/admin/reports/{rating}', [App\Http\Controllers\RatingReportController::class, 'details'])
    ->name('ratings.report.details');
Route::post('/ratings/{rating}/delete', [RatingReportController::class, 'delete'])
    ->name('ratings.delete');

Route::post('/ratings/{rating}/clear-reports', [RatingReportController::class, 'clearReports'])
    ->name('ratings.clear-reports');

Route::post('/admin/banned-words/store', [App\Http\Controllers\BannedWordController::class, 'store'])
    ->name('banned-words.store');
Route::post('/ratings/{rating}/delete', [RatingReportController::class, 'delete'])->name('ratings.delete');
Route::post('/ratings/{rating}/clear-reports', [RatingReportController::class, 'clearReports'])->name('ratings.clear-reports');
Route::middleware(['auth'])->prefix('noclegi')->name('noclegi.')->group(function () {
    Route::get('/{nocleg}/calendar', [NoclegCalendarController::class, 'index'])
        ->name('calendar');

    Route::post('/{nocleg}/calendar', [NoclegCalendarController::class, 'update'])
        ->name('calendar.update');
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


Route::middleware('auth')->group(function () {
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::post('/ratings/{rating}/report', [RatingController::class, 'report'])->name('ratings.report');
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
