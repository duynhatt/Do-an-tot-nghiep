<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\GioHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $query = DonHang::where('nguoi_dung_id', Auth::id())
            ->with(['chiTietDonHangs.sanPham', 'chiTietDonHangs.bienThe.color', 'chiTietDonHangs.bienThe.size'])
            ->orderBy('created_at', 'desc');

        $trangThai = $request->query('trang_thai');

        if ($trangThai) {
            // Lọc theo các trạng thái chuẩn (bao gồm "đã hoàn thành")
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

        $donHangs = $query->paginate(8)->withQueryString();

        foreach ($donHangs as $order) {
            $order->checkAutoCancel();
        }

        return view('client.order.index', [
            'donHangs' => $donHangs,
            'currentStatus' => $trangThai,
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

        DB::transaction(function () use ($donHang) {
            // Chỉ hoàn tồn kho khi hệ thống đã trừ tồn trước đó.
            // - COD: trừ tồn ngay khi tạo đơn, dù trang_thai_thanh_toan vẫn là 'chua_thanh_toan'
            // - VNPAY: chỉ trừ tồn khi return thành công (khi trang_thai_thanh_toan = 'da_thanh_toan')
            $shouldRefundInventory = $donHang->phuong_thuc_thanh_toan === 'cod'
                || $donHang->trang_thai_thanh_toan === 'da_thanh_toan';

            // Với VNPAY: khi đang "chờ thanh toán lại" thì chúng ta đã reserve giỏ bằng `da_dat_hang`.
            // Khi hủy đơn thì cần đưa lại các dòng giỏ về trạng thái "đang trong giỏ".
            $shouldRestoreCart = $donHang->phuong_thuc_thanh_toan === 'vnpay'
                && $donHang->trang_thai_thanh_toan !== 'da_thanh_toan';

            $donHang->load('chiTietDonHangs.bienThe');
            if ($shouldRefundInventory) {
                foreach ($donHang->chiTietDonHangs as $ct) {
                    if ($ct->bienThe) {
                        $ct->bienThe->increment('so_luong', $ct->so_luong);
                    }
                }
            }

            if ($shouldRestoreCart) {
                foreach ($donHang->chiTietDonHangs as $ct) {
                    GioHang::where('nguoi_dung_id', $donHang->nguoi_dung_id)
                        ->where('san_pham_id', $ct->san_pham_id)
                        ->where('bien_the_id', $ct->bien_the_id)
                        ->where('trang_thai', GioHang::TRANG_THAI_DA_DAT_HANG)
                        ->update(['trang_thai' => GioHang::TRANG_THAI_DANG_TRONG_GIO]);
                }
            }
            $donHang->update(['trang_thai' => DonHang::TRANG_THAI_DA_HUY]);
        });

        return back()->with('success', 'Đơn hàng đã được hủy.');
    }

    /**
     * Khách xác nhận đã nhận hàng -> chuyển sang "đã hoàn thành".
     */
    public function confirm(Request $request, $id)
    {
        $donHang = DonHang::where('id', $id)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();

        if ($donHang->trang_thai !== DonHang::TRANG_THAI_DA_GIAO) {
            return back()->with('error', 'Chỉ có thể xác nhận khi đơn hàng đang ở trạng thái "Đã giao".');
        }

        // Phòng trường hợp đơn đang có yêu cầu trả
        if ($donHang->yeu_cau_tra) {
            return back()->with('error', 'Đơn hàng đang có yêu cầu trả. Vui lòng xử lý yêu cầu trả hàng trước.');
        }

        if (!DonHang::coTheChuyenSang($donHang->trang_thai, DonHang::TRANG_THAI_DA_HOAN_THANH)) {
            return back()->with('error', 'Không thể chuyển đơn hàng sang trạng thái hoàn thành.');
        }

        $donHang->update([
            'trang_thai' => DonHang::TRANG_THAI_DA_HOAN_THANH,
            'trang_thai_thanh_toan' => 'da_thanh_toan',
        ]);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận');
    }
}
