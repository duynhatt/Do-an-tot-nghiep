<?php

// use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ===== CLIENT =====
use App\Http\Controllers\Client\HomeController;

// ===== ADMIN =====
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'home'])->name('home');

// Client
Route::prefix('')->name('client.')->group(function () {});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])
        ->name('dashboard');
});
