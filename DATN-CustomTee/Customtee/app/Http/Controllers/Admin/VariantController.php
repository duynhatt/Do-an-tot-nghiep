<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BienThe;
use App\Models\KichThuoc;
use App\Models\MauSac;
use App\Models\SanPham;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class VariantController extends Controller

{
public function index()
{
    $sanPhams = SanPham::with([
        'danhMuc',
        'variants' => function ($q) {
            $q->with(['color', 'size'])->latest();
        }
    ])->orderBy('ten_san_pham')->get();
    return view('admin.variants.index', compact('sanPhams'));








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
    $request->validate([
        'san_pham_id'    => 'required|exists:san_phams,id',
        'mau_sac_id'     => [
            'required',
            'exists:mau_sacs,id',
            Rule::unique('bien_thes')->where(function ($q) use ($request) {
                return $q->where('san_pham_id', $request->san_pham_id)
                         ->where('kich_thuoc_id', $request->kich_thuoc_id);
            })
        ],
        'kich_thuoc_id'  => 'required|exists:kich_thuocs,id',
        'gia'            => 'required|numeric|min:0|max:999999999999',
        'gia_khuyen_mai' => 'nullable|numeric|min:0|max:999999999999|lte:gia',
        'so_luong'       => 'required|integer|min:0',
        'trang_thai'     => 'nullable|in:0,1',
    ], [
        'mau_sac_id.unique'  => 'Biến thể màu + size này đã tồn tại cho sản phẩm.',
        'gia.min'            => 'Giá không được âm.',
        'gia.max'            => 'Giá vượt quá giới hạn cho phép.',
        'gia_khuyen_mai.min' => 'Giá khuyến mãi không được âm.',
        'gia_khuyen_mai.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',
        'so_luong.min'       => 'Số lượng không được âm.',
        'trang_thai.in'      => 'Trạng thái không hợp lệ.',
    ]);

    BienThe::create($request->only([
        'san_pham_id', 'mau_sac_id', 'kich_thuoc_id',
        'gia', 'gia_khuyen_mai', 'so_luong', 'trang_thai'
    ]));

    return redirect(route('variants.index') . '#product-' . $request->san_pham_id)
        ->with('success', 'Thêm biến thể thành công');
}


public function edit($id)
{
    $variant = BienThe::findOrFail($id);
    $products = SanPham::all();
    $colors = MauSac::all();
    $sizes = KichThuoc::all();

    return view('admin.variants.edit', compact('variant','products','colors','sizes'));
}




public function update(Request $request, $id)
{
    $variant = BienThe::findOrFail($id);

    $request->validate([
        'san_pham_id'    => 'required|exists:san_phams,id',
        'mau_sac_id'     => [
            'required',
            'exists:mau_sacs,id',
            Rule::unique('bien_thes')->where(function ($q) use ($request) {
                return $q->where('san_pham_id', $request->san_pham_id)
                         ->where('kich_thuoc_id', $request->kich_thuoc_id);
            })->ignore($id)
        ],
        'kich_thuoc_id'  => 'required|exists:kich_thuocs,id',
        'gia'            => 'required|numeric|min:0|max:999999999999',
        'gia_khuyen_mai' => 'nullable|numeric|min:0|max:999999999999|lte:gia',
        'so_luong'       => 'required|integer|min:0',
        'trang_thai'     => 'nullable|in:0,1',
    ], [
        'mau_sac_id.unique'  => 'Biến thể màu + size này đã tồn tại cho sản phẩm.',
        'gia.min'            => 'Giá không được âm.',
        'gia.max'            => 'Giá vượt quá giới hạn cho phép.',
        'gia_khuyen_mai.min' => 'Giá khuyến mãi không được âm.',
        'gia_khuyen_mai.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',
        'so_luong.min'       => 'Số lượng không được âm.',
        'trang_thai.in'      => 'Trạng thái không hợp lệ.',
    ]);

    $variant->update($request->only([
        'san_pham_id', 'mau_sac_id', 'kich_thuoc_id',
        'gia', 'gia_khuyen_mai', 'so_luong', 'trang_thai'
    ]));

    return redirect(route('variants.index') . '#product-' . $variant->san_pham_id)
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
