<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $query = DonHang::where('nguoi_dung_id', Auth::id())
            ->with(['chiTietDonHangs.sanPham', 'chiTietDonHangs.bienThe.color', 'chiTietDonHangs.bienThe.size'])
            ->orderBy('created_at', 'desc');

        $trangThai = $request->query('trang_thai');
        $traHang = $request->boolean('tra_hang');

        if ($trangThai && in_array($trangThai, [
            DonHang::TRANG_THAI_CHO_XAC_NHAN,
            DonHang::TRANG_THAI_DANG_XU_LY,
            DonHang::TRANG_THAI_DANG_GIAO,
            DonHang::TRANG_THAI_DA_GIAO,
            DonHang::TRANG_THAI_DA_HUY,
        ], true)) {
            $query->where('trang_thai', $trangThai);
        }

        if ($traHang) {
            $query->where('yeu_cau_tra', true);
        }

        $donHangs = $query->paginate(8)->withQueryString();

        return view('client.order.index', [
            'donHangs' => $donHangs,
            'currentStatus' => $trangThai,
            'traHang' => $traHang,
        ]);
    }

    public function show($id)
    {
        $donHang = DonHang::where('id', $id)
            ->where('nguoi_dung_id', Auth::id())
            ->with([
                'chiTietDonHangs.sanPham',
                'chiTietDonHangs.bienThe.color',
                'chiTietDonHangs.bienThe.size',
                'nguoiDung'
            ])
            ->firstOrFail();

        return view('client.order.show', compact('donHang'));
    }

    /**
     * Khách hàng yêu cầu trả hàng (chỉ khi đơn đã giao).
     */
    public function requestReturn(Request $request, $id)
    {
        $donHang = DonHang::where('id', $id)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();

        if ($donHang->trang_thai !== DonHang::TRANG_THAI_DA_GIAO) {
            return back()->with('error', 'Chỉ có thể yêu cầu trả hàng khi đơn đã giao thành công.');
        }

        if ($donHang->yeu_cau_tra) {
            return back()->with('error', 'Bạn đã gửi yêu cầu trả hàng cho đơn này rồi.');
        }

        $validated = $request->validate([
            'ly_do_tra' => 'nullable|string|max:1000',
        ]);

        $donHang->update([
            'yeu_cau_tra' => true,
            'ly_do_tra' => $validated['ly_do_tra'] ?? null,
            'ngay_yeu_cau_tra' => now(),
        ]);

        return back()->with('success', 'Đã gửi yêu cầu trả hàng. Cửa hàng sẽ liên hệ lại bạn trong thời gian sớm nhất.');
    }

    /**
     * Khách hàng hủy đơn (chỉ khi trạng thái đang "chờ xác nhận").
     */
    public function cancel(Request $request, $id)
    {
        $donHang = DonHang::where('id', $id)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();

        if ($donHang->trang_thai !== DonHang::TRANG_THAI_CHO_XAC_NHAN) {
            return back()->with('error', 'Chỉ có thể hủy đơn khi đơn đang chờ xác nhận.');
        }

        if (!DonHang::coTheChuyenSang($donHang->trang_thai, DonHang::TRANG_THAI_DA_HUY)) {
            return back()->with('error', 'Không thể hủy đơn hàng này.');
        }

        $donHang->update(['trang_thai' => DonHang::TRANG_THAI_DA_HUY]);

        return back()->with('success', 'Đơn hàng đã được hủy.');
    }
}
