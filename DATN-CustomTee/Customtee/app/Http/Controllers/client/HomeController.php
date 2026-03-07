<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sanPhamsMoiNhat = SanPham::with('category')
            ->where('trang_thai', true)
            ->whereHas('danhMuc', fn ($q) => $q->where('trang_thai', 1))
            ->withMin(['variants' => function ($q) {
                $q->where('trang_thai', 1);
            }], 'gia')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return view('client.Home', compact('sanPhamsMoiNhat'));
    }
}
