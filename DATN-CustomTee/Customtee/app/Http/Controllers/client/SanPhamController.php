<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\SanPham;

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

        // Kiểm tra xem danh mục có được hiển thị hay không
        if (!$sanPham->category || !$sanPham->category->trang_thai) {
            abort(404);
        }
        $variants = $sanPham->variants;

        if ($variants->isEmpty()) {
            $giaMacDinh = (object) [
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

        return view('client.productdetail', compact(
            'sanPham',
            'giaMacDinh',
            'priceRange',
            'totalStock'
        ));
    }
}
