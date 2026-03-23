<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\Refund;
use App\Models\RefundImage;
use App\Models\RefundItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


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
            'updated_at' => now()
        ]);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận');
    }

    public function requestReturn(Request $request, DonHang $donHang)
    {

        // 2. Kiểm tra trạng thái đơn hàng
        if ($donHang->trang_thai !== 'da_hoan_thanh') {
            return back()->with('error', 'Đơn hàng không ở trạng thái cho phép yêu cầu hoàn tiền.');
        }

        // 3. Kiểm tra thời hạn 3 ngày
        $hoanThanhTime = $donHang->da_hoan_thanh_at ?? $donHang->updated_at;
        if (!Carbon::parse($hoanThanhTime)->addDays(3)->isFuture()) {
            return back()->with('error', 'Đã hết thời hạn yêu cầu hoàn tiền (3 ngày sau khi hoàn thành).');
        }

        // 4. Validation
        $validated = $request->validate([
            'chi_tiet_ids'           => 'required|array|min:1',
            'chi_tiet_ids.*'         => 'exists:don_hang_chi_tiets,id',
            'so_luong'               => 'required|array',
            'so_luong.*'             => 'integer|min:1',
            'ly_do'                  => 'required|string|max:2000',
            'hinh_anh.*'             => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'hinh_tai_khoan.*'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // 5. Kiểm tra số lượng hợp lệ cho từng sản phẩm
        foreach ($request->chi_tiet_ids as $chiTietId) {
            $chiTiet = $donHang->chiTietDonHangs->firstWhere('id', $chiTietId);

            if (!$chiTiet) {
                throw ValidationException::withMessages(['chi_tiet_ids' => 'Sản phẩm không hợp lệ.']);
            }

            $soLuongYeuCau = $request->so_luong[$chiTietId] ?? 0;

            if ($soLuongYeuCau < 1 || $soLuongYeuCau > $chiTiet->so_luong) {
                throw ValidationException::withMessages([
                    "so_luong.$chiTietId" => "Số lượng hoàn trả phải từ 1 đến {$chiTiet->so_luong}."
                ]);
            }
        }

        // 6. Tạo bản ghi Refund
        $refund = Refund::create([
            'don_hang_id'            => $donHang->id,
            'user_id'                => Auth::id(),
            'trang_thai'             => 'cho_xu_ly',
            'ly_do'                  => $request->ly_do,
            'phuong_thuc_thanh_toan' => $donHang->phuong_thuc_thanh_toan,
            'so_tien_yeu_cau'        => 0, // sẽ cập nhật sau
        ]);

        $tongTienYeuCau = 0;

        // 7. Lưu các sản phẩm hoàn trả (RefundItem)
        foreach ($request->chi_tiet_ids as $chiTietId) {
            $chiTiet = $donHang->chiTietDonHangs->firstWhere('id', $chiTietId);
            $soLuong = $request->so_luong[$chiTietId];

            $thanhTien = $soLuong * $chiTiet->don_gia;

            RefundItem::create([
                'refund_request_id'     => $refund->id,
                'chi_tiet_don_hang_id'  => $chiTiet->id,
                'so_luong_yeu_cau'      => $soLuong,
                'thanh_tien_yeu_cau'    => $thanhTien,
            ]);

            $tongTienYeuCau += $thanhTien;
        }

        // Cập nhật tổng tiền yêu cầu hoàn
        $refund->update(['so_tien_yeu_cau' => $tongTienYeuCau]);

        // 8. Lưu ảnh minh chứng vào bảng refund_images
        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                if ($file->isValid()) {
                    $path = $file->store("refund_images/{$refund->id}", 'public');

                    RefundImage::create([
                        'refund_id'     => $refund->id,
                        'path'          => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }
        }

        // 9. Nếu COD → lưu ảnh thông tin tài khoản (cũng vào refund_images hoặc bảng riêng nếu muốn phân biệt)
        if ($donHang->phuong_thuc_thanh_toan === 'cod' && $request->hasFile('hinh_tai_khoan')) {
            foreach ($request->file('hinh_tai_khoan') as $file) {
                if ($file->isValid()) {
                    $path = $file->store("refund_bank_info/{$refund->id}", 'public');

                    RefundImage::create([
                        'refund_id'     => $refund->id,
                        'path'          => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }
        }

        // 10. Chuyển hướng về trang chi tiết + thông báo thành công
        return redirect()->route('order.show', $donHang->id)
            ->with('success', 'Yêu cầu hoàn tiền đã được gửi thành công! Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
    }
}
