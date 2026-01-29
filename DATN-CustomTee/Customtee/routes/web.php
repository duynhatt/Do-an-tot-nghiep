<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\AboutController;
use App\Http\Controllers\client\ContactController;
use App\Http\Controllers\client\ShopController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\client\ProfileController;
use App\Http\Controllers\Admin\KichThuocController;
use App\Http\Controllers\Admin\MauSacController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\CategoryController;


use App\Http\Controllers\AuthController;

// Client Authentication
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Trang chủ sau khi đăng nhập
Route::get('/', function () {
    return view('home'); // tạo view resources/views/home.blade.php
})->middleware('auth');

Route::get('/', [HomeController::class, 'index']);
Route::get('About', [AboutController::class, 'About']);
Route::get('Contact', [ContactController::class, 'Contact']);
Route::get('Shop', [ShopController::class, 'Shop']);
Route::get('ShopSingle', [ShopController::class, 'ShopSingle']);


// routes/web.php

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');

    Route::resource('danh-muc', CategoryController::class);

    Route::resource('mau-sac', MauSacController::class);
    Route::resource('kich-thuoc', KichThuocController::class);
    Route::resource('san-pham', SanPhamController::class);
});
