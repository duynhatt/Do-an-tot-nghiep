<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\BinhLuan;

class SanPhamController extends Controller
{
    public function showProduct($slug)
    {
        $sanPham = SanPham::with([
            'variants' => function ($query) {
                $query->where('trang_thai', true)
                    ->with(['color', 'size']);
            },
            'category'
        ])
        ->where('slug', $slug)
        ->where('trang_thai', true)
        ->firstOrFail();

        if (!$sanPham->category || !$sanPham->category->trang_thai) {
            abort(404);
        }

        $variants = $sanPham->variants;

        if ($variants->isEmpty()) {
            $giaMacDinh = (object)[
                'gia' => 0,
                'gia_khuyen_mai' => null,
                'so_luong' => 0
            ];
            $priceRange = 'Liên hệ';
            $totalStock = 0;
        } else {
            $giaMacDinh = $variants->first();

            $prices = $variants->map(fn($v) => $v->gia_khuyen_mai ?? $v->gia)->filter()->values();

            if ($prices->isEmpty()) {
                $priceRange = 'Chưa có giá';
            } elseif ($prices->min() === $prices->max()) {
                $priceRange = number_format($prices->min()) . ' ₫';
            } else {
                $priceRange = number_format($prices->min()) . ' ₫ - ' . number_format($prices->max()) . ' ₫';
            }

            $totalStock = $variants->sum('so_luong');
        }

        // LẤY ĐÁNH GIÁ
        $danhGias = BinhLuan::with(['bienThe.color', 'bienThe.size'])
            ->where('san_pham_id',$sanPham->id)
            ->where('trang_thai',1)
            ->latest()
            ->paginate(5);

        $avgRating = BinhLuan::where('san_pham_id', $sanPham->id)->avg('so_sao');
        $totalRating = BinhLuan::where('san_pham_id', $sanPham->id)->count();
        $avgRating = round($avgRating, 1);

        // Tối đa 48 sản phẩm
        $sanPhamCungDanhMuc = SanPham::with('category')
            ->where('trang_thai', true)
            ->where('danh_muc_id', $sanPham->danh_muc_id)
            ->where('id', '!=', $sanPham->id)
            ->whereHas('danhMuc', fn ($q) => $q->where('trang_thai', 1))
            ->withMin(['variants' => function ($q) {
                $q->where('trang_thai', 1);
            }], 'gia')
            ->orderBy('id', 'desc')
            ->take(48)
            ->get();

        return view('client.productdetail', compact(
            'sanPham',
            'giaMacDinh',
            'priceRange',
            'totalStock',
            'danhGias',
            'avgRating',
            'totalRating',
            'sanPhamCungDanhMuc'
        ));
    }
}
