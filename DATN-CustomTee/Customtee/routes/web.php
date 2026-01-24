<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\AboutController;
use App\Http\Controllers\client\ContactController;
use App\Http\Controllers\client\ShopController;
use App\Http\Controllers\Admin\DashboardController;


Route::get('/', [HomeController::class, 'index']);
Route::get('About', [AboutController::class, 'About']);
Route::get('Contact', [ContactController::class, 'Contact']);
Route::get('Shop', [ShopController::class, 'Shop']);
Route::get('ShopSingle', [ShopController::class, 'ShopSingle']);









//admin
Route::get('/DashBoard', [DashboardController::class, 'DashBoard']);
