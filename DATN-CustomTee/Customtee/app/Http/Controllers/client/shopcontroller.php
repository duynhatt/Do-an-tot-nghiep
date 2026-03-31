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
        $selectedDanhMucs = array_filter((array) $request->input('danh_muc', []), fn($id) => is_numeric($id));
        $selectedSizes = array_filter((array) $request->input('size', []), fn($id) => is_numeric($id));
        $selectedColors = array_filter((array) $request->input('color', []), fn($id) => is_numeric($id));

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

        if (!empty($selectedDanhMucs)) {
            $query->whereIn('danh_muc_id', $selectedDanhMucs);
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

        $inputMinPrice = $request->filled('min_price') ? (float) $request->min_price : null;
        $inputMaxPrice = $request->filled('max_price') ? (float) $request->max_price : null;

        if (!is_null($inputMinPrice) || !is_null($inputMaxPrice)) {
            if (!is_null($inputMinPrice) && !is_null($inputMaxPrice) && $inputMinPrice > $inputMaxPrice) {
                [$inputMinPrice, $inputMaxPrice] = [$inputMaxPrice, $inputMinPrice];
            }

            $query->whereHas('variants', function ($q) use ($inputMinPrice, $inputMaxPrice) {
                $q->where('trang_thai', 1);

                if (!is_null($inputMinPrice)) {
                    $q->where(DB::raw('COALESCE(gia_khuyen_mai, gia)'), '>=', $inputMinPrice);
                }

                if (!is_null($inputMaxPrice)) {
                    $q->where(DB::raw('COALESCE(gia_khuyen_mai, gia)'), '<=', $inputMaxPrice);
                }
            });
        }

        if (!empty($selectedSizes)) {
            $query->whereHas('variants', function ($q) use ($selectedSizes) {
                $q->where('trang_thai', 1)
                    ->whereIn('kich_thuoc_id', $selectedSizes);
            });
        }

        if (!empty($selectedColors)) {
            $query->whereHas('variants', function ($q) use ($selectedColors) {
                $q->where('trang_thai', 1)
                    ->whereIn('mau_sac_id', $selectedColors);
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