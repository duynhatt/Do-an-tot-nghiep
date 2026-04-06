<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BinhLuan;
use Illuminate\Support\Facades\Auth;

class BinhLuanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'san_pham_id' => 'required|exists:san_phams,id',
            'don_hang_id' => 'required|exists:don_hangs,id',
            'noi_dung' => 'required',
            'so_sao' => 'required|integer|min:1|max:5',
        ]);

        // Kiểm tra đã đánh giá trong đơn hàng chưa
        $daDanhGia = BinhLuan::where('user_id', Auth::id())
            ->where('san_pham_id', $request->san_pham_id)
            ->where('don_hang_id', $request->don_hang_id)
            ->exists();

        if ($daDanhGia) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.');
        }

        BinhLuan::create([
            'user_id' => Auth::id(),
            'san_pham_id' => $request->san_pham_id,
            'don_hang_id' => $request->don_hang_id,
            'noi_dung' => $request->noi_dung,
            'so_sao' => $request->so_sao,
            'trang_thai' => 1,
            'hien_thi_trang_chu' => 0,
        ]);

        return back()->with('success', 'Đánh giá thành công');
    }

    public function destroy($id)
    {
        $binhLuan = BinhLuan::findOrFail($id);

        if ($binhLuan->user_id != Auth::id()) {
            return back()->with('error', 'Bạn không có quyền xóa');
        }

        $binhLuan->delete();

        return back()->with('success', 'Đã xóa bình luận');
    }

    public function index()
    {
        $binhLuans = BinhLuan::with(['user','sanPham','donHang'])
            ->latest()
            ->paginate(10);

        return view('admin.binh-luan.index', compact('binhLuans'));
    }

    public function toggle($id)
    {
        $binhLuan = BinhLuan::findOrFail($id);

        $binhLuan->trang_thai = !$binhLuan->trang_thai;
        if (!$binhLuan->trang_thai) {
            // Khi bình luận bị ẩn khỏi hệ thống thì cũng tắt khỏi trang chủ.
            $binhLuan->hien_thi_trang_chu = false;
        }
        $binhLuan->save();

        return back();
    }

    public function toggleHome($id)
    {
        $binhLuan = BinhLuan::findOrFail($id);

        if (!$binhLuan->trang_thai) {
            return back()->with('error', 'Bình luận đang ẩn, hãy bật hiển thị trước.');
        }

        $binhLuan->hien_thi_trang_chu = !$binhLuan->hien_thi_trang_chu;
        $binhLuan->save();

        return back()->with('success', 'Đã cập nhật hiển thị trang chủ.');
    }
}