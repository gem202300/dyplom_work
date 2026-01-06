<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ManufacturerController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/users', [UserController::class, 'index']);
    // Маршрути для фільтрів карти
    Route::get('/api/object-types', function () {
        return \App\Models\ObjectType::select('id', 'name')->get();
    })->name('api.object-types');

    Route::get('/api/categories', function () {
        return \App\Models\Category::select('id', 'name')->get();
    })->name('api.categories');
});
