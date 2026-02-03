<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {

        $danhMucs = Category::orderBy('id', 'desc')->get();

        return view('admin.category.list', compact('danhMucs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255|unique:danh_mucs,ten_danh_muc',
            'mo_ta'        => 'nullable|string|max:1000',
            'trang_thai'   => 'required|in:0,1',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ten_danh_muc.max'      => 'Tên danh mục không được quá 255 ký tự.',
            'ten_danh_muc.unique'   => 'Tên danh mục này đã tồn tại.',
            'mo_ta.max'             => 'Mô tả không được quá 1000 ký tự.',
            'trang_thai.required'   => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'         => 'Trạng thái không hợp lệ.',
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

    public function show($id)
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
            'ten_danh_muc' => 'required|string|max:255|unique:danh_mucs,ten_danh_muc,' . $id,
            'mo_ta'        => 'nullable|string|max:1000',
            'trang_thai'   => 'required|in:0,1',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ten_danh_muc.max'      => 'Tên danh mục không được quá 255 ký tự.',
            'ten_danh_muc.unique'   => 'Tên danh mục này đã tồn tại.',
            'mo_ta.max'             => 'Mô tả không được quá 1000 ký tự.',
            'trang_thai.required'   => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'         => 'Trạng thái không hợp lệ.',
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

   
    public function destroy($id)
    {
        $danhMuc = Category::withCount('sanPhams')->findOrFail($id);

        if ($danhMuc->san_phams_count > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xóa danh mục đang có sản phẩm. Vui lòng xóa hoặc chuyển sản phẩm sang danh mục khác trước.',
            ], 422);
        }

        $danhMuc->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa danh mục thành công',
        ]);
    }
}
