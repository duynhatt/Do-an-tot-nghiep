<?php


use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\HomeController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KichThuocController;
use App\Http\Controllers\Admin\MauSacController;
use App\Http\Controllers\Admin\SanPhamController;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::prefix('')->name('client.')->group(function () {});


Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');

    Route::resource('danh-muc', CategoryController::class);

    Route::resource('mau-sac', MauSacController::class);
    Route::resource('kich-thuoc', KichThuocController::class);
    Route::resource('san-pham', SanPhamController::class);
});
