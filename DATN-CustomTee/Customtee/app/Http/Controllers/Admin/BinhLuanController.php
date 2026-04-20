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
            'bien_the_id' => 'nullable|exists:bien_thes,id',
            'don_hang_id' => 'required|exists:don_hangs,id',
            'noi_dung' => 'required',
            'so_sao' => 'required|integer|min:1|max:5',
        ]);

        // Kiểm tra đã đánh giá cho biến thể này trong đơn hàng chưa
        $query = BinhLuan::where('user_id', Auth::id())
            ->where('san_pham_id', $request->san_pham_id)
            ->where('don_hang_id', $request->don_hang_id);

        // Nếu có bien_the_id thì kiểm tra theo biến thể
        if ($request->bien_the_id) {
            $query->where('bien_the_id', $request->bien_the_id);
        } else {
            // Nếu không có bien_the_id thì chỉ kiểm tra các đánh giá cũ không có bien_the_id
            $query->whereNull('bien_the_id');
        }

        $daDanhGia = $query->exists();

        if ($daDanhGia) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Bạn đã đánh giá sản phẩm này rồi.'
                ]);
            }
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        BinhLuan::create([
            'user_id' => Auth::id(),
            'san_pham_id' => $request->san_pham_id,
            'bien_the_id' => $request->bien_the_id,
            'don_hang_id' => $request->don_hang_id,
            'noi_dung' => $request->noi_dung,
            'so_sao' => $request->so_sao,
            'trang_thai' => 1,
            'hien_thi_trang_chu' => 0,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá thành công'
            ]);
        }

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
        $binhLuans = BinhLuan::with(['user','sanPham','donHang','bienThe.color','bienThe.size'])
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