<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\BienThe;
use App\Models\GioHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GioHangController extends Controller
{
    /**
     * Danh sách giỏ hàng (chỉ dòng đang_trong_gio).
     */
    public function index()
    {
        $items = GioHang::with(['sanPham', 'bienThe.color', 'bienThe.size'])
            ->where('nguoi_dung_id', Auth::id())
            ->dangTrongGio()
            ->whereNotNull('bien_the_id')
            ->orderBy('updated_at', 'desc')
            ->get();

        $tongTien = $items->sum('thanh_tien');

        return view('client.gio-hang.index', compact('items', 'tongTien'));
    }

    /**
     * Thêm sản phẩm (biến thể) vào giỏ.
     * Nếu đã có cùng user + product + variant + dang_trong_gio → cộng dồn so_luong.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'san_pham_id' => 'required|exists:san_phams,id',
            'bien_the_id' => 'required|exists:bien_thes,id',
            'so_luong'    => 'required|integer|min:1',
        ], [
            'san_pham_id.required' => 'Thiếu thông tin sản phẩm.',
            'bien_the_id.required'  => 'Vui lòng chọn màu và kích thước.',
            'bien_the_id.exists'    => 'Biến thể không tồn tại.',
            'so_luong.min'          => 'Số lượng tối thiểu là 1.',
        ]);

        $bienThe = BienThe::where('id', $validated['bien_the_id'])
            ->where('san_pham_id', $validated['san_pham_id'])
            ->where('trang_thai', true)
            ->firstOrFail();

        $soLuongTon = $bienThe->so_luong;
        if ($validated['so_luong'] > $soLuongTon) {
            throw ValidationException::withMessages([
                'so_luong' => "Chỉ còn {$soLuongTon} sản phẩm trong kho.",
            ]);
        }

        $donGia = $bienThe->gia_khuyen_mai ?? $bienThe->gia;
        if ($donGia === null || $donGia < 0) {
            throw ValidationException::withMessages(['bien_the_id' => 'Sản phẩm chưa có giá.']);
        }

        $existing = GioHang::where('nguoi_dung_id', Auth::id())
            ->where('san_pham_id', $validated['san_pham_id'])
            ->where('bien_the_id', $validated['bien_the_id'])
            ->dangTrongGio()
            ->first();

        if ($existing) {
            $newSoLuong = $existing->so_luong + $validated['so_luong'];
            if ($newSoLuong > $soLuongTon) {
                throw ValidationException::withMessages([
                    'so_luong' => "Tổng số lượng vượt tồn kho (tối đa {$soLuongTon}).",
                ]);
            }
            $existing->so_luong = $newSoLuong;
            $existing->save();
            $message = 'Đã cập nhật số lượng trong giỏ hàng.';
        } else {
            GioHang::create([
                'nguoi_dung_id' => Auth::id(),
                'san_pham_id'   => $validated['san_pham_id'],
                'bien_the_id'   => $validated['bien_the_id'],
                'so_luong'      => $validated['so_luong'],
                'don_gia'       => $donGia,
                'trang_thai'    => GioHang::TRANG_THAI_DANG_TRONG_GIO,
            ]);
            $message = 'Đã thêm vào giỏ hàng.';
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('gio-hang.index')->with('success', $message);
    }

    /**
     * Cập nhật số lượng một dòng giỏ hàng.
     */
    public function update(Request $request, GioHang $gioHang)
    {
        $this->authorizeCartItem($gioHang);

        $validated = $request->validate([
            'so_luong' => 'required|integer|min:1',
        ], [
            'so_luong.min' => 'Số lượng tối thiểu là 1.',
        ]);

        $bienThe = $gioHang->bienThe;
        if (!$bienThe || $validated['so_luong'] > $bienThe->so_luong) {
            $max = $bienThe ? $bienThe->so_luong : 0;
            throw ValidationException::withMessages([
                'so_luong' => "Số lượng tối đa theo kho là {$max}.",
            ]);
        }

        $gioHang->so_luong = $validated['so_luong'];
        $gioHang->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'thanh_tien' => (int) $gioHang->fresh()->thanh_tien,
                'message'    => 'Đã cập nhật số lượng.',
            ]);
        }

        return redirect()->route('gio-hang.index')->with('success', 'Đã cập nhật số lượng.');
    }

    /**
     * Xóa một dòng khỏi giỏ hàng.
     */
    public function destroy(GioHang $gioHang)
    {
        $this->authorizeCartItem($gioHang);

        $gioHang->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng.']);
        }

        return redirect()->route('gio-hang.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    private function authorizeCartItem(GioHang $gioHang): void
    {
        if ($gioHang->nguoi_dung_id !== Auth::id()) {
            abort(403);
        }
        if ($gioHang->trang_thai !== GioHang::TRANG_THAI_DANG_TRONG_GIO) {
            abort(404);
        }
    }
}
