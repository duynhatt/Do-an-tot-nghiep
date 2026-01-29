<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SanPhamController extends Controller
{
    public function index()
    {
        $sanPhams = SanPham::with('danhMuc')->orderBy('id', 'desc')->get();

        $danhMucs = Category::where('trang_thai', 1)->get();

        return view('admin.product.list', compact('sanPhams', 'danhMucs'));
    }

    public function create()
    {
        $danhMucs = Category::where('trang_thai', 1)->get();
        return view('admin.san-pham.create', compact('danhMucs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'danh_muc_id'       => 'required|exists:danh_mucs,id',
            'ten_san_pham'      => 'required|string|max:255',
            'mo_ta_ngan'        => 'nullable|string|max:500',
            'mo_ta_chi_tiet'    => 'nullable|string',
            'hinh_anh_chinh'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cho_phep_thiet_ke' => 'boolean',
            'trang_thai'        => 'required|in:0,1',
        ]);

        if ($request->hasFile('hinh_anh_chinh')) {
            $path = $request->file('hinh_anh_chinh')->store('san-pham', 'public');
            $validated['hinh_anh_chinh'] = $path;
        }

        SanPham::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm sản phẩm thành công',
        ]);
    }

    public function edit($id)
    {
        $sanPham = SanPham::findOrFail($id);
        $danhMucs = Category::where('trang_thai', 1)->get();

        return response()->json([
            'status' => true,
            'data'   => $sanPham,
            'danh_mucs' => $danhMucs,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sanPham = SanPham::findOrFail($id);

        $validated = $request->validate([
            'danh_muc_id'       => 'required|exists:danh_mucs,id',
            'ten_san_pham'      => 'required|string|max:255',
            'mo_ta_ngan'        => 'nullable|string|max:500',
            'mo_ta_chi_tiet'    => 'nullable|string',
            'hinh_anh_chinh'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cho_phep_thiet_ke' => 'boolean',
            'trang_thai'        => 'required|in:0,1',
        ]);

        if ($request->hasFile('hinh_anh_chinh')) {
            if ($sanPham->hinh_anh_chinh) {
                Storage::disk('public')->delete($sanPham->hinh_anh_chinh);
            }
            $path = $request->file('hinh_anh_chinh')->store('san-pham', 'public');
            $validated['hinh_anh_chinh'] = $path;
        }

        $sanPham->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật sản phẩm thành công',
        ]);
    }

    public function destroy($id)
    {
        $sanPham = SanPham::findOrFail($id);

        if ($sanPham->hinh_anh_chinh) {
            Storage::disk('public')->delete($sanPham->hinh_anh_chinh);
        }

        $sanPham->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa sản phẩm thành công',
        ]);
    }
}
