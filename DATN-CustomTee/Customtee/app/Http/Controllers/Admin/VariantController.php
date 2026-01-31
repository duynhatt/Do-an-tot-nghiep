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
    $variants = BienThe::with([
    'product.category',  // load danh mục luôn
    'color',
    'size'
])->latest()->get();
    return view('admin.variants.index', compact('variants'));








}

public function create()
{
    $products = SanPham::all();
    $colors   = MauSac::all();
    $sizes    = KichThuoc::all();

    return view('admin.variants.create', compact('products','colors','sizes'));
}





public function store(Request $request)
{
    $request->validate([
        'san_pham_id'   => 'required',
        'mau_sac_id'    => [
            'required',
            Rule::unique('bien_thes')->where(function ($q) use ($request) {
                return $q->where('san_pham_id', $request->san_pham_id)
                         ->where('kich_thuoc_id', $request->kich_thuoc_id);
            })
        ],
        'kich_thuoc_id' => 'required',
        'gia'           => 'required|numeric',
        'so_luong'      => 'required|integer',
    ], [
        'mau_sac_id.unique' => 'Biến thể màu + size này đã tồn tại cho sản phẩm.'
    ]);

    BienThe::create($request->all());

    return redirect()->route('variants.index')
        ->with('success','Thêm biến thể thành công');
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
        'san_pham_id' => 'required',
        'mau_sac_id' => 'required',
        'kich_thuoc_id' => 'required',
        'gia' => 'required|numeric',
        'so_luong' => 'required|integer',
    ]);

    $variant->update($request->all());

    return redirect()->route('variants.index')
        ->with('success', 'Cập nhật biến thể thành công');
}


public function destroy($id)
{
    $variant = BienThe::findOrFail($id);
    $variant->delete();

    return redirect()->route('variants.index')
        ->with('success', 'Đã xoá biến thể');
}



}
