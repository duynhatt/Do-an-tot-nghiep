<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefundController extends Controller
{
    private function isForcedAcceptCase(Refund $refund): bool
    {
        $order = $refund->donHang;

        if (!$order) {
            return false;
        }

        return $order->phuong_thuc_thanh_toan === 'vnpay'
            && $order->trang_thai_thanh_toan === 'da_thanh_toan'
            && $order->trang_thai === DonHang::TRANG_THAI_DA_HUY
            && (
                (bool) $order->yeu_cau_huy
                || !empty($order->ly_do_huy_boi_admin)
            );
    }

    public function index(Request $request)
    {
        $query = Refund::with(['donHang', 'user', 'items.chiTietDonHang.sanPham'])
            ->latest();

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $refunds = $query->paginate(15);

        return view('admin.hoan-tra.index', compact('refunds'));
    }

    public function show(Refund $refund)
    {
        $refund->load([
            'donHang',
            'user',
            'items.chiTietDonHang.sanPham',
            'items.chiTietDonHang.bienThe',
            'items.chiTietDonHang.bienThe.color',
            'items.chiTietDonHang.bienThe.size',
            'images'
        ]);

        $isForcedAcceptCase = $this->isForcedAcceptCase($refund);

        return view('admin.hoan-tra.show', compact('refund', 'isForcedAcceptCase'));
    }

    public function reject(Request $request, Refund $refund)
    {
        if ($refund->da_hoan_tien) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        $refund->loadMissing('donHang');
        if ($this->isForcedAcceptCase($refund)) {
            return redirect()->back()->with(
                'error',
                'Đơn online đã thanh toán và bị admin hủy chủ động thì không được từ chối hoàn tiền.'
            );
        }

        DB::beginTransaction();

        try {
            $refund->update(['trang_thai' => 'da_tu_choi']);
            DonHang::where('id', $refund->don_hang_id)->update(['yeu_cau_tra' => 0]);
            DB::commit();

            return redirect()->back()->with('success', 'Đã từ chối yêu cầu hoàn trả.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi từ chối: ' . $e->getMessage());
        }
    }

    public function accept(Request $request, Refund $refund)
    {
        DB::beginTransaction();

        try {
            // Khóa bản ghi để tránh duyệt trùng khi có nhiều request đồng thời.
            $refund = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();
            if ($refund->trang_thai !== 'cho_xu_ly') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Yêu cầu này không còn ở trạng thái chờ xử lý.');
            }

            $refund->loadMissing('donHang', 'items.chiTietDonHang.bienThe');
            $refund->update(['trang_thai' => 'da_chap_nhan']);

            $order = $refund->donHang;

            $amount = (int) $refund->so_tien_yeu_cau;

            // Chỉ cộng lại tồn kho cho case "trả hàng sau khi đã giao".
            // Với case đơn online đã thanh toán rồi hủy, tồn đã được cộng ở luồng hủy đơn.
            $shouldRestockInventory = $order
                && $order->trang_thai === DonHang::TRANG_THAI_DA_GIAO;

            if ($shouldRestockInventory) {
                foreach ($refund->items as $item) {
                    if ($item->chiTietDonHang?->bienThe) {
                        $item->chiTietDonHang->bienThe->increment('so_luong', $item->so_luong_yeu_cau);
                    }
                }
            }

            $order->update([
                'ghi_chu' => trim(($order->ghi_chu ?? '') . "\nHoàn tiền: "
                    . number_format($amount, 0, ',', '.') . "₫ - " . now()->format('d/m/Y H:i')),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Đã chấp nhận hoàn tiền cho đơn hàng này.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Hoàn tiền thất bại: ' . $e->getMessage());
        }
    }

    public function RefundComplete(Request $request, Refund $refund)
    {
        $request->validate([
            'hinh_anh_xac_nhan' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('hinh_anh_xac_nhan')) {
            $path = $request->file('hinh_anh_xac_nhan')
                ->store('refund_confirm', 'public');

            $refund->hinh_anh_xac_nhan = $path;
        }

        $refund->trang_thai = 'da_hoan_tien';
        $refund->save();

        return back()->with('success', 'Hoàn tiền thành công');
    }
}
