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
            'ten_kich_thuoc' => 'required|string|max:50',
            'trang_thai'     => 'required|in:0,1',
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
            'ten_kich_thuoc' => 'required|string|max:50',
            'trang_thai'     => 'required|in:0,1',
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
        $kichThuoc = KichThuoc::findOrFail($id);
        $kichThuoc->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa kích thước thành công',
        ]);
    }
}