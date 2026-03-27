<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();
        if ($request->keyword) {
            $query->where('ten_danh_muc', 'like', '%' . $request->keyword . '%');
        }
        
        if ($request->trang_thai !== null && $request->trang_thai !== '') {
            $query->where('trang_thai', $request->trang_thai);
        }

        $danhMucs = $query->orderBy('id', 'desc')->get();

        return view('admin.category.list', compact('danhMucs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required|string|max:255|unique:danh_mucs,ten_danh_muc',
            'mo_ta'        => 'nullable|string|max:1000',
            'hinh_anh'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'trang_thai'   => 'required|in:0,1',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ten_danh_muc.max'      => 'Tên danh mục không được quá 255 ký tự.',
            'ten_danh_muc.unique'   => 'Tên danh mục này đã tồn tại.',
            'mo_ta.max'             => 'Mô tả không được quá 1000 ký tự.',
            'hinh_anh.image'       => 'Ảnh danh mục không hợp lệ.',
            'trang_thai.required'   => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'         => 'Trạng thái không hợp lệ.',
        ]);

        $data = [
            'ten_danh_muc' => $request->ten_danh_muc,
            'slug'         => Str::slug($request->ten_danh_muc),
            'mo_ta'        => $request->mo_ta,
            'trang_thai'   => $request->trang_thai,
        ];

        if ($request->hasFile('hinh_anh')) {
            $data['hinh_anh'] = $request->file('hinh_anh')->store('categories', 'public');
        }

        Category::create($data);

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
            'hinh_anh'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'trang_thai'   => 'required|in:0,1',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục.',
            'ten_danh_muc.max'      => 'Tên danh mục không được quá 255 ký tự.',
            'ten_danh_muc.unique'   => 'Tên danh mục này đã tồn tại.',
            'mo_ta.max'             => 'Mô tả không được quá 1000 ký tự.',
            'hinh_anh.image'       => 'Ảnh danh mục không hợp lệ.',
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

        if ($request->hasFile('hinh_anh')) {
            if ($danhMuc->hinh_anh) {
                Storage::disk('public')->delete($danhMuc->hinh_anh);
            }
            $danhMuc->hinh_anh = $request->file('hinh_anh')->store('categories', 'public');
            $danhMuc->save();
        }

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

        if ($danhMuc->hinh_anh) {
            Storage::disk('public')->delete($danhMuc->hinh_anh);
        }

        $danhMuc->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa danh mục thành công',
        ]);
    }
}
