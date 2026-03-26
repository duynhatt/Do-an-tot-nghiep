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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{
    public function list(Request $request)
    {
        $query = DonHang::where('nguoi_dung_id', Auth::id())
            ->with([
                'chiTietDonHangs.sanPham',
                'chiTietDonHangs.bienThe.color',
                'chiTietDonHangs.bienThe.size',
                'refunds' => function ($query) {
                    $query->latest();
                },
            ])
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
                $query->where('trang_thai', $trangThai)
                    ->where(function ($q) {
                        // Các đơn đã gửi yêu cầu hoàn tiền chỉ hiển thị ở tab "Trả hàng"
                        $q->where('yeu_cau_tra', false)->orWhereNull('yeu_cau_tra');
                    });
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
        if (
            $donHang->trang_thai === 'da_hoan_thanh'
            || $donHang->trang_thai === 'dang_giao'
            || $donHang->trang_thai === 'da_huy'
            || ($donHang->phuong_thuc_thanh_toan === 'cod' && $donHang->trang_thai !== 'da_giao')
        ) {
            return back()->with('error', 'Đơn hàng không ở trạng thái cho phép yêu cầu hoàn tiền.');
        }

        $hoanThanhTime = $donHang->da_hoan_thanh_at ?? $donHang->updated_at;
        if (!Carbon::parse($hoanThanhTime)->addDays(3)->isFuture()) {
            return back()->with('error', 'Đã hết thời hạn yêu cầu hoàn tiền (3 ngày sau khi hoàn thành).');
        }

        $isOnlineCancelRefund = $donHang->phuong_thuc_thanh_toan === 'vnpay' && $donHang->trang_thai === 'dang_xu_ly';
        $rules = [
            'ly_do' => 'required|string|max:2000',
        ];
        if (!$isOnlineCancelRefund) {
            $rules['chi_tiet_ids'] = 'required|array|min:1';
            $rules['chi_tiet_ids.*'] = 'exists:don_hang_chi_tiets,id';
            $rules['so_luong'] = 'required|array';
            $rules['so_luong.*'] = 'integer|min:1';
            $rules['hinh_anh.*'] = 'nullable|image|mimes:jpg,jpeg,png|max:5120';
        }

        if (in_array($donHang->phuong_thuc_thanh_toan, ['cod', 'vnpay'])) {
            $rules['refund_method'] = 'required|in:upload,manual';

            if ($request->refund_method === 'upload') {
                $rules['hinh_tai_khoan'] = 'required|array|min:1|max:5';
                $rules['hinh_tai_khoan.*'] = 'required|image|mimes:jpg,jpeg,png|max:5120';
            }

            if ($request->refund_method === 'manual') {
                $rules['ngan_hang']     = 'required|string|max:100';
                $rules['so_tai_khoan']  = 'required|string|max:50';
                $rules['chi_nhanh']     = 'nullable|string|max:100';
                $rules['ten_chu_tk']    = 'required|string|max:100';
            }
        }

        $request->validate($rules);

        $chiTietIds = [];
        if (!$isOnlineCancelRefund) {
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
            $chiTietIds = $request->chi_tiet_ids;
        } else {
            $chiTietIds = $donHang->chiTietDonHangs->pluck('id')->all();
        }

        $storedNewImagePaths = [];
        $oldImagePaths = [];

        DB::beginTransaction();
        try {
            $refund = Refund::where('don_hang_id', $donHang->id)
                ->where('user_id', Auth::id())
                ->where('trang_thai', 'da_tu_choi')
                ->latest()
                ->first();

            if ($refund) {
                // Gửi lại yêu cầu: dùng lại bản ghi đã bị từ chối để không tạo thêm dòng mới bên admin.
                $oldImagePaths = $refund->images()->pluck('path')->filter()->all();
                $refund->items()->delete();
                $refund->images()->delete();
                $refund->update([
                    'trang_thai'             => 'cho_xu_ly',
                    'ly_do'                  => $request->ly_do,
                    'phuong_thuc_thanh_toan' => $donHang->phuong_thuc_thanh_toan,
                    'so_tien_yeu_cau'        => 0,
                    'ngan_hang'              => $request->ngan_hang ?? null,
                    'so_tai_khoan'           => $request->so_tai_khoan ?? null,
                    'chi_nhanh'              => $request->chi_nhanh ?? null,
                    'ten_chu_tk'             => $request->ten_chu_tk ?? null,
                ]);
            } else {
                $refund = Refund::create([
                    'don_hang_id'            => $donHang->id,
                    'user_id'                => Auth::id(),
                    'trang_thai'             => 'cho_xu_ly',
                    'ly_do'                  => $request->ly_do,
                    'phuong_thuc_thanh_toan' => $donHang->phuong_thuc_thanh_toan,
                    'so_tien_yeu_cau'        => 0,
                    'ngan_hang'              => $request->ngan_hang ?? null,
                    'so_tai_khoan'           => $request->so_tai_khoan ?? null,
                    'chi_nhanh'              => $request->chi_nhanh ?? null,
                    'ten_chu_tk'             => $request->ten_chu_tk ?? null,
                ]);
            }

            $tongTienYeuCau = 0;

            foreach ($chiTietIds as $chiTietId) {
                $chiTiet = $donHang->chiTietDonHangs->firstWhere('id', $chiTietId);
                $soLuong = $isOnlineCancelRefund
                    ? (int) $chiTiet->so_luong
                    : (int) ($request->so_luong[$chiTietId] ?? 0);
                $thanhTien = $soLuong * $chiTiet->don_gia;

                RefundItem::create([
                    'refund_request_id'    => $refund->id,
                    'chi_tiet_don_hang_id' => $chiTiet->id,
                    'so_luong_yeu_cau'     => $soLuong,
                    'thanh_tien_yeu_cau'   => $thanhTien,
                ]);

                $tongTienYeuCau += $thanhTien;
            }

            $refund->update(['so_tien_yeu_cau' => $tongTienYeuCau]);

            if (!$isOnlineCancelRefund && $request->hasFile('hinh_anh')) {
                foreach ($request->file('hinh_anh') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store("refund_images/{$refund->id}", 'public');
                        $storedNewImagePaths[] = $path;

                        RefundImage::create([
                            'refund_id'      => $refund->id,
                            'path'           => $path,
                            'original_name'  => $file->getClientOriginalName(),
                            'mime_type'      => $file->getMimeType(),
                            'size'           => $file->getSize(),
                        ]);
                    }
                }
            }

            if (
                in_array($donHang->phuong_thuc_thanh_toan, ['cod', 'vnpay'])
                && $request->refund_method === 'upload'
                && $request->hasFile('hinh_tai_khoan')
            ) {
                $bankFiles = $request->file('hinh_tai_khoan');
                if (!is_array($bankFiles)) {
                    $bankFiles = [$bankFiles];
                }
                foreach ($bankFiles as $file) {
                    if ($file->isValid()) {
                        $path = $file->store("refund_bank_info/{$refund->id}", 'public');
                        $storedNewImagePaths[] = $path;

                        RefundImage::create([
                            'refund_id'      => $refund->id,
                            'path'           => $path,
                            'original_name'  => $file->getClientOriginalName(),
                            'mime_type'      => $file->getMimeType(),
                            'size'           => $file->getSize(),
                        ]);
                    }
                }
            }

            $donHang->update([
                'yeu_cau_tra' => 1,
                'ly_do_tra' => $refund->ly_do,
                'ngay_yeu_cau_tra' => $refund->created_at,
            ]);

            DB::commit();

            if (!empty($oldImagePaths)) {
                Storage::disk('public')->delete($oldImagePaths);
            }

            return redirect()->route('order.show', $donHang->id)
                ->with('success', 'Yêu cầu hoàn tiền đã được gửi thành công! Chúng tôi sẽ xem xét trong thời gian sớm nhất.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($storedNewImagePaths)) {
                Storage::disk('public')->delete($storedNewImagePaths);
            }
            throw $e;
        }
    }
}
