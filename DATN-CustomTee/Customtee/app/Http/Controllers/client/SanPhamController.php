<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        $giaMacDinh = $sanPham->variants->first() ?? (object)[
            'gia' => 0,
            'gia_khuyen_mai' => null,
            'so_luong' => 0
        ];

        return view('client.productdetail', compact('sanPham', 'giaMacDinh'));
    }
}
