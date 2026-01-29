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
            'ten_mau' => 'required|string|max:100',
            'ma_mau'  => 'nullable|string|max:7', 
            'trang_thai' => 'required|in:0,1',
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
            'ten_mau' => 'required|string|max:100',
            'ma_mau'  => 'nullable|string|max:7',
            'trang_thai' => 'required|in:0,1',
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
        $mauSac = MauSac::findOrFail($id);
        $mauSac->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa màu sắc thành công',
        ]);
    }
}