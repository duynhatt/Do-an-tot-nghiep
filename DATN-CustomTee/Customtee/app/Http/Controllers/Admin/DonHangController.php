<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\Refund;
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
            'refunds' => function ($query) {
                $query->latest();
            },
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
                $query->where('trang_thai', $trangThai)
                    ->where(function ($q) {
                        // Đơn đã có yêu cầu trả/hoàn chỉ hiển thị ở tab "Trả hàng".
                        $q->where('yeu_cau_tra', false)->orWhereNull('yeu_cau_tra');
                    });
            }
            // Lọc "Trả hàng": các đơn có yêu cầu trả
            elseif ($trangThai === 'tra_hang') {
                $query->where('yeu_cau_tra', true);
            }
        }

        $q = trim((string) $request->query('q'));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($query) use ($like) {
                $query->where('ma_don_hang', 'like', $like)
                    ->orWhere('ten_nguoi_nhan', 'like', $like)
                    ->orWhere('so_dien_thoai_nhan_hang', 'like', $like)
                    ->orWhere('ghi_chu', 'like', $like)
                    ->orWhereHas('nguoiDung', function ($nguoiDungQuery) use ($like) {
                        $nguoiDungQuery->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            });
        }

        // Lọc nâng cao: thanh toán / phương thức / thời gian / khoảng tiền
        $trangThaiThanhToan = $request->query('trang_thai_thanh_toan');
        if ($trangThaiThanhToan !== null && $trangThaiThanhToan !== '') {
            if (in_array($trangThaiThanhToan, ['da_thanh_toan', 'that_bai', 'chua_thanh_toan'], true)) {
                $query->where('trang_thai_thanh_toan', $trangThaiThanhToan);
            }
        }

        $phuongThucThanhToan = $request->query('phuong_thuc_thanh_toan');
        if ($phuongThucThanhToan !== null && $phuongThucThanhToan !== '') {
            if (in_array($phuongThucThanhToan, ['cod', 'vnpay'], true)) {
                $query->where('phuong_thuc_thanh_toan', $phuongThucThanhToan);
            }
        }

        $ngayTu = $request->query('ngay_tu');
        $ngayDen = $request->query('ngay_den');
        if (!empty($ngayTu) || !empty($ngayDen)) {
            $from = !empty($ngayTu) ? \Carbon\Carbon::parse($ngayTu)->startOfDay() : null;
            $to = !empty($ngayDen) ? \Carbon\Carbon::parse($ngayDen)->endOfDay() : null;

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to);
            }
        }

        $tongTienMin = $request->query('tong_tien_min');
        $tongTienMax = $request->query('tong_tien_max');
        if ($tongTienMin !== null && $tongTienMin !== '' && is_numeric($tongTienMin)) {
            $query->where('tong_tien', '>=', (float) $tongTienMin);
        }
        if ($tongTienMax !== null && $tongTienMax !== '' && is_numeric($tongTienMax)) {
            $query->where('tong_tien', '<=', (float) $tongTienMax);
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
            'refunds' => function ($query) {
                $query->latest();
            },
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
        $latestRefundStatus = Refund::where('don_hang_id', $donHang->id)
            ->latest()
            ->value('trang_thai');

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

        // Khi đơn đang trong luồng hoàn tiền/trả hàng, chặn chuyển sang các trạng thái giao vận.
        if (
            (
                (bool) $donHang->yeu_cau_tra
                || in_array($latestRefundStatus, ['cho_xu_ly', 'da_chap_nhan', 'da_hoan_tien'], true)
            )
            && in_array($trangThaiMoi, [
                DonHang::TRANG_THAI_DANG_XU_LY,
                DonHang::TRANG_THAI_DANG_GIAO,
                DonHang::TRANG_THAI_DA_GIAO,
            ], true)
        ) {
            return back()->with(
                'error',
                'Đơn hàng đang trong quy trình hoàn tiền/trả hàng, không thể chuyển sang trạng thái xử lý hoặc giao hàng.'
            );
        }

        if (!DonHang::coTheChuyenSang($donHang->trang_thai, $trangThaiMoi)) {
            return back()->with('error', 'Không thể chuyển từ "' . DonHang::tenTrangThai($donHang->trang_thai) . '" sang "' . DonHang::tenTrangThai($trangThaiMoi) . '".');
        }

        $payload = ['trang_thai' => $trangThaiMoi];
        if ($trangThaiMoi === DonHang::TRANG_THAI_DA_GIAO) {
            $payload['da_giao_at'] = now();
            // COD được thu tiền khi giao thành công.
            if ($donHang->phuong_thuc_thanh_toan === 'cod') {
                $payload['trang_thai_thanh_toan'] = 'da_thanh_toan';
            }
        }
        if ($trangThaiMoi === DonHang::TRANG_THAI_DA_HOAN_THANH) {
            $payload['trang_thai_thanh_toan'] = 'da_thanh_toan';
        }

        if ($trangThaiMoi === DonHang::TRANG_THAI_DA_HUY) {
            DB::transaction(function () use ($donHang, $payload) {
                // Chỉ hoàn tồn kho khi hệ thống đã trừ tồn trước đó.
                // - COD: trừ ngay khi tạo đơn.
                // - VNPAY: chỉ trừ tồn khi thanh toán thành công (trang_thai_thanh_toan = da_thanh_toan).
                $shouldRefundInventory = $donHang->phuong_thuc_thanh_toan === 'cod'
                    || $donHang->trang_thai_thanh_toan === 'da_thanh_toan';

                // Với VNPAY chưa thanh toán: đã reserve giỏ bằng `da_dat_hang`,
                // cần đưa lại các dòng giỏ về "đang trong giỏ" khi admin hủy.
                $shouldRestoreCart = $donHang->phuong_thuc_thanh_toan === 'vnpay'
                    && $donHang->trang_thai_thanh_toan !== 'da_thanh_toan';

                $donHang->load('chiTietDonHangs.bienThe');
                foreach ($donHang->chiTietDonHangs as $ct) {
                    if ($shouldRefundInventory && $ct->bienThe) {
                        $ct->bienThe->increment('so_luong', $ct->so_luong);
                    }

                    if ($shouldRestoreCart) {
                        GioHang::where('nguoi_dung_id', $donHang->nguoi_dung_id)
                            ->where('san_pham_id', $ct->san_pham_id)
                            ->where('bien_the_id', $ct->bien_the_id)
                            ->where('trang_thai', GioHang::TRANG_THAI_DA_DAT_HANG)
                            ->update(['trang_thai' => GioHang::TRANG_THAI_DANG_TRONG_GIO]);
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