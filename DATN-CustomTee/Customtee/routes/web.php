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
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\client\SanPhamController as ClientSanPhamController;
use App\Http\Controllers\client\GioHangController;
use App\Http\Controllers\AuthController;
use App\Models\BienThe;
use Illuminate\Http\Request;

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
// Route::get('ShopSingle/{id}', [ShopController::class, 'ShopSingle'])->name('shop.single');
Route::get('/san-pham/{slug}', [ClientSanPhamController::class, 'showProduct'])
    ->name('sanpham.chitiet');

Route::get('/api/product-variant', function (Request $request) {
    $productId = $request->query('product_id');
    $colorId   = $request->query('color');
    $sizeId    = $request->query('size');

    // Kiểm tra sản phẩm có tồn tại và được hiển thị hay không
    $product = \App\Models\SanPham::where('id', $productId)
        ->where('trang_thai', true)
        ->whereHas('danhMuc', fn($q) => $q->where('trang_thai', 1))
        ->first();

    if (!$product) {
        return response()->json([
            'success' => false
        ]);
    }

    $variant = BienThe::where('san_pham_id', $productId)
        ->where('mau_sac_id', $colorId)
        ->where('kich_thuoc_id', $sizeId)
        ->where('trang_thai', true)
        ->first();

    if ($variant) {
        return response()->json([
            'success' => true,
            'variant' => [
                'id'              => $variant->id,
                'gia'             => $variant->gia,
                'gia_khuyen_mai'  => $variant->gia_khuyen_mai,
                'so_luong'        => $variant->so_luong,
            ]
        ]);
    }

    return response()->json([
        'success' => false
    ]);
})->name('api.product.variant');

// routes/web.php

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Giỏ hàng (lưu DB, gắn user)
    Route::get('/gio-hang', [GioHangController::class, 'index'])->name('gio-hang.index');
    Route::post('/gio-hang', [GioHangController::class, 'store'])->name('gio-hang.store');
    Route::put('/gio-hang/{gioHang}', [GioHangController::class, 'update'])->name('gio-hang.update');
    Route::delete('/gio-hang/{gioHang}', [GioHangController::class, 'destroy'])->name('gio-hang.destroy');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');
    Route::resource('danh-muc', CategoryController::class);
    Route::resource('mau-sac', MauSacController::class);
    Route::resource('kich-thuoc', KichThuocController::class);
    Route::resource('san-pham', SanPhamController::class);
});
Route::prefix('admin/variants')->name('variants.')->group(function () {
    Route::get('/', [VariantController::class, 'index'])->name('index');
    Route::get('/create', [VariantController::class, 'create'])->name('create');
    Route::post('/store', [VariantController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [VariantController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [VariantController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [VariantController::class, 'destroy'])->name('delete');
});



Route::get('/admin/products/info/{id}', function ($id) {
    $product = \App\Models\SanPham::with('category')->findOrFail($id);

    return response()->json([
        'name'     => $product->ten_san_pham,
        'image'    => $product->hinh_anh_chinh, // ví dụ: san-pham/abc.jpg
        'category' => $product->category->ten_danh_muc ?? '',
        'desc'     => $product->mo_ta_ngan,
    ]);
});
