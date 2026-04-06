<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DonHangController;
use App\Http\Controllers\Admin\KichThuocController;
use App\Http\Controllers\Admin\MauSacController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Admin\BinhLuanController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\client\AboutController;
use App\Http\Controllers\client\CheckoutController;
use App\Http\Controllers\client\ContactController;
use App\Http\Controllers\client\GioHangController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\ProfileController;
use App\Http\Controllers\client\SanPhamController as ClientSanPhamController;
use App\Http\Controllers\client\ShopController;
use App\Models\BienThe;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\client\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Client Authentication
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('About', [AboutController::class, 'About']);

// Route Liên hệ cho khách (Client)
Route::get('Contact', [ContactController::class, 'Contact'])->name('contact');
Route::post('Contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('Shop', [ShopController::class, 'Shop']);

Route::get('/san-pham/{slug}', [ClientSanPhamController::class, 'showProduct'])
    ->name('sanpham.chitiet');

Route::get('/api/product-variant', function (Request $request) {
    $productId = $request->query('product_id');
    $colorId   = $request->query('color');
    $sizeId    = $request->query('size');

    $product = \App\Models\SanPham::where('id', $productId)
        ->where('trang_thai', true)
        ->whereHas('danhMuc', fn($q) => $q->where('trang_thai', 1))
        ->first();

    if (!$product) {
        return response()->json(['success' => false]);
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
    return response()->json(['success' => false]);
})->name('api.product.variant');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    Route::get('/order', [OrderController::class, 'list'])->name('order');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::post('/order/{id}/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    Route::post('/order/{donHang}/return-request', [OrderController::class, 'requestReturn'])
        ->name('order.return.request');

    Route::get('/gio-hang', [GioHangController::class, 'index'])->name('gio-hang.index');
    Route::post('/gio-hang', [GioHangController::class, 'store'])->name('gio-hang.store');
    Route::put('/gio-hang/{gioHang}', [GioHangController::class, 'update'])->name('gio-hang.update');
    Route::delete('/gio-hang/{gioHang}', [GioHangController::class, 'destroy'])->name('gio-hang.destroy');
    Route::post('/gio-hang/selection', [GioHangController::class, 'updateSelection'])->name('gio-hang.selection');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('dat-hang');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Mua ngay (không dùng giỏ hàng)
    Route::post('/buy-now', [CheckoutController::class, 'buyNow'])->name('buy-now');
    Route::get('/checkout/buy-now', [CheckoutController::class, 'checkoutBuyNow'])->name('checkout.buy-now');
    Route::post('/checkout/buy-now/process', [CheckoutController::class, 'processBuyNow'])->name('checkout.buy-now.process');
    Route::get('/checkout/vnpay/return', [CheckoutController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::get('/order/{id}/repay', [CheckoutController::class, 'repay'])->name('order.repay');

    // ROUTE ÁP DỤNG VOUCHER CHO CLIENT
    Route::post('/apply-voucher', [VoucherController::class, 'applyVoucher'])->name('voucher.apply');

    Route::post('binh-luan', [BinhLuanController::class, 'store'])->name('binh-luan.store');

    Route::get('/order/success/{ma_don_hang}', function ($ma_don_hang) {
        $donHang = \App\Models\DonHang::where('ma_don_hang', $ma_don_hang)->firstOrFail();
        return view('client.checkout.success', compact('donHang'));
    })->name('order.success');
});

// KHU VỰC ADMIN
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'home'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');
    Route::resource('danh-muc', CategoryController::class);
    Route::resource('mau-sac', MauSacController::class);
    Route::resource('kich-thuoc', KichThuocController::class);
    Route::resource('san-pham', SanPhamController::class);

    Route::resource('vouchers', VoucherController::class);

    Route::resource('binh-luan', BinhLuanController::class);
    Route::get('binh-luan', [BinhLuanController::class, 'index'])->name('binh-luan.index');
    Route::get('binh-luan/toggle/{id}', [BinhLuanController::class, 'toggle'])
        ->name('binh-luan.toggle');

    // Đơn hàng: danh sách, chi tiết, cập nhật trạng thái
    Route::get('don-hang', [DonHangController::class, 'index'])->name('don-hang.index');
    Route::get('don-hang/{donHang}', [DonHangController::class, 'show'])->name('don-hang.show');
    Route::patch('don-hang/{donHang}/status', [DonHangController::class, 'updateStatus'])->name('don-hang.update-status');
    // QUẢN LÝ LIÊN HỆ TRONG ADMIN


    Route::prefix('lien-he')->name('lien-he.')->group(function () {
        // Chỉ định rõ là \App\Http\Controllers\Admin\ContactController
        Route::get('/', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('index');
        Route::post('/{id}/status', [\App\Http\Controllers\Admin\ContactController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('destroy');
    });

    Route::get('/hoan-tra', [RefundController::class, 'index'])->name('hoan-tra.index');
    Route::get('/hoan-tra/{refund}', [RefundController::class, 'show'])->name('hoan-tra.show');
    Route::patch('/hoan-tra/{refund}/accept', [RefundController::class, 'accept'])
        ->name('hoan-tra.accept');
    Route::patch('/hoan-tra/{refund}/reject', [RefundController::class, 'reject'])
        ->name('hoan-tra.reject');
    Route::patch('hoan_tra/{refund}/refund-complete', [RefundController::class, 'RefundComplete'])->name('hoan-tra.complete');

    Route::patch('/don-hang/{id}/chap-nhan-hoan-tien', [DonHangController::class, 'chapNhanHoanTien'])
        ->name('don-hang.chap-nhan-hoan-tien');

    Route::patch('/don-hang/{id}/tu-choi-hoan-tien', [DonHangController::class, 'tuChoiHoanTien'])
        ->name('don-hang.tu-choi-hoan-tien');
});

Route::prefix('admin/variants')->name('variants.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [VariantController::class, 'index'])->name('index');
    Route::get('/create', [VariantController::class, 'create'])->name('create');
    Route::post('/store', [VariantController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [VariantController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [VariantController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [VariantController::class, 'destroy'])->name('delete');
});

// Các Route API bổ trợ cho Admin
Route::get('/admin/products/info/{id}', function ($id) {
    $product = \App\Models\SanPham::with('category')->findOrFail($id);
    return response()->json([
        'name'     => $product->ten_san_pham,
        'image'    => $product->hinh_anh_chinh,
        'category' => $product->category->ten_danh_muc ?? '',
        'desc'     => $product->mo_ta_ngan,
    ]);
})->middleware(['auth', 'admin']);

Route::get('/admin/variants/by-product/{id}', function ($id) {
    $product = \App\Models\SanPham::with(['variants.color', 'variants.size'])->findOrFail($id);
    return response()->json([
        'variants' => $product->variants->map(function ($variant) {
            return [
                'id'             => $variant->id,
                'mau_sac_id'     => $variant->mau_sac_id,
                'kich_thuoc_id'  => $variant->kich_thuoc_id,
                'mau'            => $variant->color->ten_mau ?? '',
                'size'           => $variant->size->ten_kich_thuoc ?? '',
                'gia'            => $variant->gia,
                'gia_khuyen_mai' => $variant->gia_khuyen_mai,
                'so_luong'       => $variant->so_luong,
                'trang_thai'     => (bool) $variant->trang_thai,
            ];
        })->values(),
    ]);
})->middleware(['auth', 'admin']);

Route::get('/admin/dashboard/orders-by-status', [DashboardController::class, 'ordersByStatus'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard.orders-by-status');

Route::get('/admin/dashboard/revenue-time-table', [DashboardController::class, 'revenueTimeTable'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard.revenue-time-table');

Route::get('/admin/dashboard/low-stock-variants', [DashboardController::class, 'lowStockVariants'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard.low-stock-variants');
