<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function Shop(Request $request)
    {
        $danhMucs = Category::hienThi()->orderBy('ten_danh_muc')->get();
        $tuKhoa = $request->get('q');
        
        $query = SanPham::with('category')
            ->where('trang_thai', true)
            ->whereHas('danhMuc', fn($q) => $q->where('trang_thai', 1))
            ->when($tuKhoa, function ($q) use ($tuKhoa) {
                $q->where(function ($sub) use ($tuKhoa) {
                    $sub->where('ten_san_pham', 'like', '%' . $tuKhoa . '%')
                        ->orWhere('mo_ta_ngan', 'like', '%' . $tuKhoa . '%');
                });
            })
            ->withMin(['variants' => function ($q) {
                $q->where('trang_thai', 1);
            }], 'gia');

        if ($request->filled('danh_muc')) {
            $query->where('danh_muc_id', $request->danh_muc);
        }

        $sanPhams = $query->orderBy('id', 'desc')->paginate(9)->appends($request->query());
        
        return view('client.Shop', compact('danhMucs', 'sanPhams', 'tuKhoa'));
    }

    public function ShopSingle($id)
    {
        $product = SanPham::with(['category', 'variants.color', 'variants.size'])
            ->where('trang_thai', true)
            ->whereHas('danhMuc', fn($q) => $q->where('trang_thai', 1))
            ->findOrFail($id);
        return view('client.ShopSingle', compact('product'));
    }
}