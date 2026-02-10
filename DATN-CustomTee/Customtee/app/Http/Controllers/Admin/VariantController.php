<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BienThe;
use App\Models\KichThuoc;
use App\Models\MauSac;
use App\Models\SanPham;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class VariantController extends Controller

{
public function index(Request $request)
{
    $selectedProductId = $request->get('san_pham_id');
    $sanPhams = SanPham::with([
        'danhMuc',
        'variants' => function ($q) {
            $q->with(['color', 'size'])->latest();
        }
    ])
        ->when($selectedProductId, function ($query) use ($selectedProductId) {
            $query->where('id', $selectedProductId);
        })
        ->orderBy('ten_san_pham')
        ->get();

    return view('admin.variants.index', compact('sanPhams', 'selectedProductId'));








}

public function create(Request $request)
{
    $products = SanPham::orderBy('ten_san_pham')->get();
    $colors   = MauSac::all();
    $sizes    = KichThuoc::all();
    $selectedProductId = $request->get('san_pham_id');

    return view('admin.variants.create', compact('products', 'colors', 'sizes', 'selectedProductId'));
}





public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'san_pham_id'               => 'required|exists:san_phams,id',
        'variants'                  => 'required|array|min:1',
        'variants.*.mau_sac_id'     => 'required|exists:mau_sacs,id',
        'variants.*.kich_thuoc_id'  => 'required|exists:kich_thuocs,id',
        'variants.*.gia'            => 'required|numeric|min:0|max:999999999999',
        'variants.*.gia_khuyen_mai' => 'nullable|numeric|min:0|max:999999999999',
        'variants.*.so_luong'       => 'required|integer|min:0',
        'variants.*.trang_thai'     => 'nullable|in:0,1',
    ], [
        'variants.*.gia.min'            => 'Giá không được âm.',
        'variants.*.gia.max'            => 'Giá vượt quá giới hạn cho phép.',
        'variants.*.gia_khuyen_mai.min' => 'Giá khuyến mãi không được âm.',
        'variants.*.so_luong.min'       => 'Số lượng không được âm.',
        'variants.*.trang_thai.in'      => 'Trạng thái không hợp lệ.',
    ]);

    $validator->after(function ($validator) use ($request) {
        $variants = $request->input('variants', []);
        $sanPhamId = $request->input('san_pham_id');

        $seen = [];
        foreach ($variants as $index => $variant) {
            $mau = $variant['mau_sac_id'] ?? null;
            $kich = $variant['kich_thuoc_id'] ?? null;
            $gia = $variant['gia'] ?? null;
            $giaKm = $variant['gia_khuyen_mai'] ?? null;

            if ($giaKm !== null && $gia !== null && $giaKm > $gia) {
                $validator->errors()->add("variants.$index.gia_khuyen_mai", 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.');
            }

            if ($mau !== null && $kich !== null) {
                $key = $mau . '-' . $kich;
                if (isset($seen[$key])) {
                    $validator->errors()->add("variants.$index.mau_sac_id", 'Không được trùng biến thể trong danh sách thêm mới.');
                }
                $seen[$key] = true;
            }
        }

        if ($sanPhamId) {
            $existing = BienThe::where('san_pham_id', $sanPhamId)
                ->get(['mau_sac_id', 'kich_thuoc_id'])
                ->map(function ($item) {
                    return $item->mau_sac_id . '-' . $item->kich_thuoc_id;
                })
                ->flip();

            foreach ($variants as $index => $variant) {
                $mau = $variant['mau_sac_id'] ?? null;
                $kich = $variant['kich_thuoc_id'] ?? null;
                if ($mau !== null && $kich !== null) {
                    $key = $mau . '-' . $kich;
                    if ($existing->has($key)) {
                        $validator->errors()->add("variants.$index.mau_sac_id", 'Biến thể màu + size này đã tồn tại cho sản phẩm.');
                    }
                }
            }
        }
    });

    $validated = $validator->validate();

    $rows = [];
    foreach ($validated['variants'] as $variant) {
        $rows[] = [
            'san_pham_id'    => $validated['san_pham_id'],
            'mau_sac_id'     => $variant['mau_sac_id'],
            'kich_thuoc_id'  => $variant['kich_thuoc_id'],
            'gia'            => $variant['gia'],
            'gia_khuyen_mai' => $variant['gia_khuyen_mai'] ?? null,
            'so_luong'       => $variant['so_luong'],
            'trang_thai'     => $variant['trang_thai'] ?? 1,
        ];
    }

    BienThe::insert($rows);

    return redirect(route('variants.index'))
        ->with('success', 'Thêm biến thể thành công');
}


public function edit($id)
{
    $variant = BienThe::findOrFail($id);
    $product = SanPham::with([
        'variants' => function ($q) {
            $q->with(['color', 'size'])->orderBy('id');
        },
        'category'
    ])->findOrFail($variant->san_pham_id);
    $products = SanPham::all();
    $colors = MauSac::all();
    $sizes = KichThuoc::all();

    return view('admin.variants.edit', compact('variant','product','products','colors','sizes'));
}




public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'san_pham_id'               => 'required|exists:san_phams,id',
        'variants'                  => 'required|array|min:1',
        'variants.*.id'             => 'required|exists:bien_thes,id',
        'variants.*.mau_sac_id'     => 'required|exists:mau_sacs,id',
        'variants.*.kich_thuoc_id'  => 'required|exists:kich_thuocs,id',
        'variants.*.gia'            => 'required|numeric|min:0|max:999999999999',
        'variants.*.gia_khuyen_mai' => 'nullable|numeric|min:0|max:999999999999',
        'variants.*.so_luong'       => 'required|integer|min:0',
        'variants.*.trang_thai'     => 'nullable|in:0,1',
    ], [
        'variants.*.gia.min'            => 'Giá không được âm.',
        'variants.*.gia.max'            => 'Giá vượt quá giới hạn cho phép.',
        'variants.*.gia_khuyen_mai.min' => 'Giá khuyến mãi không được âm.',
        'variants.*.so_luong.min'       => 'Số lượng không được âm.',
        'variants.*.trang_thai.in'      => 'Trạng thái không hợp lệ.',
    ]);

    $validator->after(function ($validator) use ($request) {
        $variants = $request->input('variants', []);
        $sanPhamId = $request->input('san_pham_id');
        $ids = collect($variants)->pluck('id')->filter()->values();

        $seen = [];
        foreach ($variants as $index => $variant) {
            $mau = $variant['mau_sac_id'] ?? null;
            $kich = $variant['kich_thuoc_id'] ?? null;
            $gia = $variant['gia'] ?? null;
            $giaKm = $variant['gia_khuyen_mai'] ?? null;

            if ($giaKm !== null && $gia !== null && $giaKm > $gia) {
                $validator->errors()->add("variants.$index.gia_khuyen_mai", 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.');
            }

            if ($mau !== null && $kich !== null) {
                $key = $mau . '-' . $kich;
                if (isset($seen[$key])) {
                    $validator->errors()->add("variants.$index.mau_sac_id", 'Không được trùng biến thể trong danh sách cập nhật.');
                }
                $seen[$key] = true;
            }
        }

        if ($sanPhamId && $ids->isNotEmpty()) {
            $validIds = BienThe::where('san_pham_id', $sanPhamId)
                ->whereIn('id', $ids)
                ->pluck('id')
                ->flip();

            foreach ($variants as $index => $variant) {
                $id = $variant['id'] ?? null;
                if ($id && !$validIds->has($id)) {
                    $validator->errors()->add("variants.$index.id", 'Biến thể không thuộc sản phẩm này.');
                }
            }
        }

        if ($sanPhamId) {
            $existing = BienThe::where('san_pham_id', $sanPhamId)
                ->when($ids->isNotEmpty(), function ($q) use ($ids) {
                    $q->whereNotIn('id', $ids);
                })
                ->get(['mau_sac_id', 'kich_thuoc_id'])
                ->map(function ($item) {
                    return $item->mau_sac_id . '-' . $item->kich_thuoc_id;
                })
                ->flip();

            foreach ($variants as $index => $variant) {
                $mau = $variant['mau_sac_id'] ?? null;
                $kich = $variant['kich_thuoc_id'] ?? null;
                if ($mau !== null && $kich !== null) {
                    $key = $mau . '-' . $kich;
                    if ($existing->has($key)) {
                        $validator->errors()->add("variants.$index.mau_sac_id", 'Biến thể màu + size này đã tồn tại cho sản phẩm.');
                    }
                }
            }
        }
    });

    $validated = $validator->validate();
    $sanPhamId = $validated['san_pham_id'];

    foreach ($validated['variants'] as $variantData) {
        BienThe::where('id', $variantData['id'])->update([
            'san_pham_id'    => $sanPhamId,
            'mau_sac_id'     => $variantData['mau_sac_id'],
            'kich_thuoc_id'  => $variantData['kich_thuoc_id'],
            'gia'            => $variantData['gia'],
            'gia_khuyen_mai' => $variantData['gia_khuyen_mai'] ?? null,
            'so_luong'       => $variantData['so_luong'],
            'trang_thai'     => $variantData['trang_thai'] ?? 1,
        ]);
    }

    return redirect(route('variants.index') . '#product-' . $sanPhamId)
        ->with('success', 'Cập nhật biến thể thành công');
}


public function destroy($id)
{
    $variant = BienThe::findOrFail($id);
    $sanPhamId = $variant->san_pham_id;
    $variant->delete();

    return redirect(route('variants.index') . '#product-' . $sanPhamId)
        ->with('success', 'Đã xoá biến thể');
}



}
