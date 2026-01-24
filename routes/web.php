<?php

// use App\Http\Controllers\HomeController;

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\HomeController;

use App\Http\Controllers\Admin\DashboardController;


Route::get('/', [HomeController::class, 'home'])->name('home');

Route::prefix('')->name('client.')->group(function () {});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');

    //Danh muc
    Route::get('/danh-muc', [CategoryController::class, 'list'])
        ->name('danh-muc');

    Route::post('/danh-muc/store', [CategoryController::class, 'store'])
        ->name('danh-muc.store');

    Route::get('/danh-muc/edit/{id}', [CategoryController::class, 'edit'])
        ->name('danh-muc.edit');

    Route::post('/danh-muc/update/{id}', [CategoryController::class, 'update'])
        ->name('danh-muc.update');

    Route::delete('/danh-muc/delete/{id}', [CategoryController::class, 'delete'])
        ->name('danh-muc.delete');

    
});
