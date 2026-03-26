<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefundController extends Controller
{
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

        return view('admin.hoan-tra.show', compact('refund'));
    }

    public function reject(Request $request, Refund $refund)
    {
        if ($refund->da_hoan_tien) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        DB::beginTransaction();

        try {
            $refund->update(['trang_thai' => 'da_tu_choi']);
            DB::commit();

            return redirect()->back()->with('success', 'Đã từ chối yêu cầu hoàn trả.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi từ chối: ' . $e->getMessage());
        }
    }

    public function accept(Request $request, Refund $refund)
    {
        if ($refund->da_hoan_tien) {
            return redirect()->back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        DB::beginTransaction();

        try {
            $refund->update(['trang_thai' => 'da_chap_nhan']);

            $order = $refund->donHang;

            $amount = (int) $refund->so_tien_yeu_cau;

            foreach ($refund->items as $item) {
                if ($item->chiTietDonHang?->bienThe) {
                    $item->chiTietDonHang->bienThe->increment('so_luong', $item->so_luong_yeu_cau);
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

    public function RefundComplete(Refund $refund)
    {
        $refund->update(['trang_thai' => 'da_hoan_tien']);
        DonHang::where('id', $refund->don_hang_id)
            ->update([
                'ly_do_tra' => $refund->ly_do,
                'ngay_yeu_cau_tra' => $refund->created_at
            ]);
        return redirect()->back()->with('success', 'Hoàn tiền thành công cho đơn hàng này');
    }
}
