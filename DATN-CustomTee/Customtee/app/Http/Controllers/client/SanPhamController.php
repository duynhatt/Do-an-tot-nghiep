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
            'variants.color',
            'variants.size'
        ])->where('slug', $slug)->firstOrFail();

        $giaMacDinh = $sanPham->variants->first();

        return view('client.productdetail', compact('sanPham', 'giaMacDinh'));
    }
}
