<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;

class DonHangController extends Controller
{
    /**
     * Danh sách đơn hàng (có lọc theo trạng thái).
     */
    public function index(Request $request)
    {
        $query = DonHang::with([
            'nguoiDung',
            'chiTietDonHangs.sanPham',
            'chiTietDonHangs.bienThe.color',
            'chiTietDonHangs.bienThe.size',
        ])->orderBy('created_at', 'desc');

        $trangThai = $request->query('trang_thai');
        if ($trangThai !== null && $trangThai !== '') {
            // Lọc theo các trạng thái chuẩn trong cột trang_thai (bao gồm "đã hoàn thành")
            if (in_array($trangThai, [
                DonHang::TRANG_THAI_CHO_XAC_NHAN,
                DonHang::TRANG_THAI_DANG_XU_LY,
                DonHang::TRANG_THAI_DANG_GIAO,
                DonHang::TRANG_THAI_DA_GIAO,
                DonHang::TRANG_THAI_DA_HOAN_THANH,
                DonHang::TRANG_THAI_DA_HUY,
            ], true)) {
                $query->where('trang_thai', $trangThai);
            }
            // Lọc "Trả hàng": các đơn có yêu cầu trả
            elseif ($trangThai === 'tra_hang') {
                $query->where('yeu_cau_tra', true);
            }
        }

        $donHangs = $query->paginate(10)->withQueryString();

        return view('admin.don-hang.index', compact('donHangs'));
    }

    /**
     * Chi tiết đơn hàng (admin).
     */
    public function show(DonHang $donHang)
    {
        $donHang->load([
            'nguoiDung',
            'chiTietDonHangs.sanPham',
            'chiTietDonHangs.bienThe.color',
            'chiTietDonHangs.bienThe.size',
        ]);

        return view('admin.don-hang.show', compact('donHang'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (chỉ chuyển theo state machine).
     */
    public function updateStatus(Request $request, DonHang $donHang)
    {
        $request->validate([
            'trang_thai' => 'required|string|in:cho_xac_nhan,dang_xu_ly,dang_giao,da_giao,da_hoan_thanh,da_huy',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $trangThaiMoi = $request->trang_thai;

        if (!DonHang::coTheChuyenSang($donHang->trang_thai, $trangThaiMoi)) {
            return back()->with('error', 'Không thể chuyển từ "' . DonHang::tenTrangThai($donHang->trang_thai) . '" sang "' . DonHang::tenTrangThai($trangThaiMoi) . '".');
        }

        $donHang->update(['trang_thai' => $trangThaiMoi]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành "' . DonHang::tenTrangThai($trangThaiMoi) . '".');
    }
}