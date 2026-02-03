<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MauSac;
use Illuminate\Http\Request;

class MauSacController extends Controller
{
    public function index()
    {
        $mauSacs = MauSac::orderBy('id', 'desc')->get();
        return view('admin.color.list', compact('mauSacs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_mau'     => 'required|string|max:100|unique:mau_sacs,ten_mau',
            'ma_mau'      => 'nullable|string|max:20',
            'trang_thai'  => 'required|in:0,1',
        ], [
            'ten_mau.required' => 'Vui lòng nhập tên màu.',
            'ten_mau.max'      => 'Tên màu không được quá 100 ký tự.',
            'ten_mau.unique'   => 'Tên màu này đã tồn tại.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'    => 'Trạng thái không hợp lệ.',
        ]);

        MauSac::create($request->only(['ten_mau', 'ma_mau', 'trang_thai']));

        return response()->json([
            'status'  => true,
            'message' => 'Thêm màu sắc thành công',
        ]);
    }

    public function show($id)
    {
        $mauSac = MauSac::findOrFail($id);
        return response()->json([
            'status' => true,
            'data'   => $mauSac,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_mau'     => 'required|string|max:100|unique:mau_sacs,ten_mau,' . $id,
            'ma_mau'      => 'nullable|string|max:20',
            'trang_thai'  => 'required|in:0,1',
        ], [
            'ten_mau.required' => 'Vui lòng nhập tên màu.',
            'ten_mau.max'      => 'Tên màu không được quá 100 ký tự.',
            'ten_mau.unique'   => 'Tên màu này đã tồn tại.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in'    => 'Trạng thái không hợp lệ.',
        ]);

        $mauSac = MauSac::findOrFail($id);
        $mauSac->update($request->only(['ten_mau', 'ma_mau', 'trang_thai']));

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật màu sắc thành công',
        ]);
    }

    public function destroy($id)
    {
        $mauSac = MauSac::withCount('variants')->findOrFail($id);

        if ($mauSac->variants_count > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể xóa màu sắc đang được sử dụng trong biến thể sản phẩm.',
            ], 422);
        }

        $mauSac->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa màu sắc thành công',
        ]);
    }
}