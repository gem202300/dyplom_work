<?php
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\ReservationController;


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
Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');

    Route::middleware(['auth'])->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', fn () => view('categories.index'))->name('index');
        Route::get('/create', fn () => view('categories.create'))->name('create');
        Route::get('/{category}/edit', fn (Category $category) => view('categories.edit', compact('category')))->name('edit');
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
