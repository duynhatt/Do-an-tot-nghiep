<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\KichThuoc;
use App\Models\MauSac;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BienThe;

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

        $tableVariant = (new BienThe())->getTable();

        $minPrice = SanPham::where('san_phams.trang_thai', 1)
            ->join($tableVariant, 'san_phams.id', '=', $tableVariant.'.san_pham_id')
            ->where($tableVariant.'.trang_thai', 1)
            ->min(DB::raw('COALESCE('.$tableVariant.'.gia_khuyen_mai, '.$tableVariant.'.gia)'));

        $maxPrice = SanPham::where('san_phams.trang_thai', 1)
            ->join($tableVariant, 'san_phams.id', '=', $tableVariant.'.san_pham_id')
            ->where($tableVariant.'.trang_thai', 1)
            ->max(DB::raw('COALESCE('.$tableVariant.'.gia_khuyen_mai, '.$tableVariant.'.gia)'));

        $sizes = KichThuoc::all();
        $colors = MauSac::all();

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('trang_thai', 1)
                ->whereBetween(DB::raw('COALESCE(gia_khuyen_mai, gia)'), [
                    $request->min_price,
                    $request->max_price
                ]);
            });
        }

        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('kich_thuoc_id', $request->size);
            });
        }

        if ($request->filled('color')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('mau_sac_id', $request->color);
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('variants_min_gia', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('variants_min_gia', 'desc');
                    break;
                case 'new':
                    $query->orderBy('id', 'desc');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $sanPhams = $query->paginate(9)->appends($request->query());

        return view('client.Shop', compact(
            'danhMucs',
            'sanPhams',
            'tuKhoa',
            'sizes',
            'colors',
            'minPrice',
            'maxPrice'
        ));
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