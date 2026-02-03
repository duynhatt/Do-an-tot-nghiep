<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KichThuoc;
use Illuminate\Http\Request;

class KichThuocController extends Controller
{
    public function index()
    {
        $kichThuocs = KichThuoc::orderBy('id', 'desc')->get();
        return view('admin.size.list', compact('kichThuocs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_kich_thuoc' => 'required|string|max:50|unique:kich_thuocs,ten_kich_thuoc',
            'trang_thai'     => 'required|in:0,1',
        ], [
            'ten_kich_thuoc.required' => 'Vui lòng nhập tên kích thước.',
            'ten_kich_thuoc.max'      => 'Tên kích thước không được quá 50 ký tự.',
            'ten_kich_thuoc.unique'   => 'Tên kích thước này đã tồn tại.',
            'trang_thai.required'     => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'           => 'Trạng thái không hợp lệ.',
        ]);

        KichThuoc::create($request->only(['ten_kich_thuoc', 'trang_thai']));

        return response()->json([
            'status'  => true,
            'message' => 'Thêm kích thước thành công',
        ]);
    }

    public function show($id)
    {
        $kichThuoc = KichThuoc::findOrFail($id);
        return response()->json([
            'status' => true,
            'data'   => $kichThuoc,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_kich_thuoc' => 'required|string|max:50|unique:kich_thuocs,ten_kich_thuoc,' . $id,
            'trang_thai'     => 'required|in:0,1',
        ], [
            'ten_kich_thuoc.required' => 'Vui lòng nhập tên kích thước.',
            'ten_kich_thuoc.max'      => 'Tên kích thước không được quá 50 ký tự.',
            'ten_kich_thuoc.unique'   => 'Tên kích thước này đã tồn tại.',
            'trang_thai.required'     => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'           => 'Trạng thái không hợp lệ.',
        ]);

        $kichThuoc = KichThuoc::findOrFail($id);
        $kichThuoc->update($request->only(['ten_kich_thuoc', 'trang_thai']));

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật kích thước thành công',
        ]);
    }

    public function destroy($id)
    {
        $kichThuoc = KichThuoc::withCount('variants')->findOrFail($id);

        if ($kichThuoc->variants_count > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xóa kích thước đang được sử dụng trong biến thể sản phẩm.',
            ], 422);
        }

        $kichThuoc->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa kích thước thành công',
        ]);
    }
}