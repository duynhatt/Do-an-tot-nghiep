<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $donHangs = $query->paginate(9)->withQueryString();

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

        // Chặn admin chuyển đơn sang "đã hoàn thành" – chỉ khách hàng được xác nhận nhận hàng
        if ($trangThaiMoi === DonHang::TRANG_THAI_DA_HOAN_THANH) {
            return back()->with('error', 'Chỉ khách hàng mới có thể xác nhận hoàn thành đơn hàng.');
        }

        // Với đơn thanh toán online (VNPAY), nếu CHƯA thanh toán thành công thì
        // KHÔNG cho phép admin chuyển sang các trạng thái xử lý/giao hàng.
        if (
            $donHang->phuong_thuc_thanh_toan === 'vnpay'
            && $donHang->trang_thai_thanh_toan !== 'da_thanh_toan'
            && in_array($trangThaiMoi, [
                DonHang::TRANG_THAI_DANG_XU_LY,
                DonHang::TRANG_THAI_DANG_GIAO,
                DonHang::TRANG_THAI_DA_GIAO,
            ], true)
        ) {
            return back()->with(
                'error',
                'Đơn thanh toán online chưa được thanh toán thành công, không thể chuyển sang trạng thái xử lý/giao hàng.'
            );
        }

        if (!DonHang::coTheChuyenSang($donHang->trang_thai, $trangThaiMoi)) {
            return back()->with('error', 'Không thể chuyển từ "' . DonHang::tenTrangThai($donHang->trang_thai) . '" sang "' . DonHang::tenTrangThai($trangThaiMoi) . '".');
        }

        $payload = ['trang_thai' => $trangThaiMoi];
        if ($trangThaiMoi === DonHang::TRANG_THAI_DA_HOAN_THANH) {
            $payload['trang_thai_thanh_toan'] = 'da_thanh_toan';
        }

        if ($trangThaiMoi === DonHang::TRANG_THAI_DA_HUY) {
            DB::transaction(function () use ($donHang, $payload) {
                $donHang->load('chiTietDonHangs.bienThe');
                foreach ($donHang->chiTietDonHangs as $ct) {
                    if ($ct->bienThe) {
                        $ct->bienThe->increment('so_luong', $ct->so_luong);
                    }
                }
                $donHang->update($payload);
            });
        } else {
            $donHang->update($payload);
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành "' . DonHang::tenTrangThai($trangThaiMoi) . '".');
    }
}