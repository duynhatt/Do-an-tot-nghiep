<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function list()
    {

        $danhMucs = Category::orderBy('id', 'desc')->get();

        return view('admin.category.list', compact('danhMucs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'mo_ta'        => 'nullable|string',
            'trang_thai'   => 'required|in:0,1',
        ]);

        Category::create([
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug'         => Str::slug($request->ten_danh_muc),
            'mo_ta'        => $request->mo_ta,
            'trang_thai'   => $request->trang_thai,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm danh mục thành công',
        ]);
    }

    public function edit($id)
    {
        $danhMuc = Category::findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $danhMuc,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'mo_ta'        => 'nullable|string',
            'trang_thai'   => 'required|in:0,1',
        ]);

        $danhMuc = Category::findOrFail($id);

        $danhMuc->update([
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug'         => Str::slug($request->ten_danh_muc),
            'mo_ta'        => $request->mo_ta,
            'trang_thai'   => $request->trang_thai,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật danh mục thành công',
        ]);
    }

   
    public function delete($id)
    {
        $danhMuc = Category::findOrFail($id);
        $danhMuc->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa danh mục thành công',
        ]);
    }
}
