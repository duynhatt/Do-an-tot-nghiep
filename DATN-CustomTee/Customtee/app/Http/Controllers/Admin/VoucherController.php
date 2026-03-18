<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderBy('id', 'desc')->get();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma' => 'required|unique:vouchers,ma',
            'loai' => 'required|in:phan_tram,tien_mat',
            'gia_tri' => 'required|numeric|min:1',
            'don_hang_toi_thieu' => 'nullable|numeric|min:0',
            'giam_toi_da' => 'nullable|numeric|min:0|required_if:loai,phan_tram',
            'bat_dau' => 'required|date',
            'ket_thuc' => 'required|date|after_or_equal:bat_dau',
            'so_luong' => 'required|integer|min:1',
        ], [
            'ma.unique' => 'Mã voucher này đã tồn tại!',
            'ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu!',
            'giam_toi_da.required_if' => 'Voucher giảm theo % bắt buộc nhập số tiền giảm tối đa.',
        ]);

        $data = $request->all();
        $data['ten'] = $request->ma;
        $data['trang_thai'] = 1;

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Thêm thành công!');
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'ma' => 'required|unique:vouchers,ma,' . $id,
            'loai' => 'required|in:phan_tram,tien_mat',
            'gia_tri' => 'required|numeric|min:1',
            'don_hang_toi_thieu' => 'nullable|numeric|min:0',
            'giam_toi_da' => 'nullable|numeric|min:0|required_if:loai,phan_tram',
            'bat_dau' => 'required|date',
            'ket_thuc' => 'required|date|after_or_equal:bat_dau',
            'so_luong' => 'required|integer|min:1',
        ], [
            'ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu!',
            'giam_toi_da.required_if' => 'Voucher giảm theo % bắt buộc nhập số tiền giảm tối đa.',
        ]);

        $data = $request->all();
        $data['ten'] = $request->ma;

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy($id)
    {
        Voucher::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Đã xóa!');
    }
    // ==========================================
    // PHẦN DÀNH CHO CLIENT (NHẬP MÃ GIẢM GIÁ)
    // ==========================================
    public function applyVoucher(Request $request)
    {
        $maVoucher = $request->voucher_code;
        $tongDonHang = $request->total_amount;
        $now = Carbon::now();

        // 1. Kiểm tra chính xác HOA/THƯỜNG bằng BINARY
        $voucher = Voucher::whereRaw('BINARY ma = ?', [$maVoucher])
            ->where('bat_dau', '<=', $now)
            ->where('ket_thuc', '>=', $now)
            ->where('trang_thai', 1)
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại (lưu ý chữ hoa/thường) hoặc đã hết hạn.'
            ]);
        }

        if ($voucher->da_su_dung >= $voucher->so_luong) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá này đã hết lượt sử dụng.'
            ]);
        }

        // 2. Kiểm tra điều kiện đơn hàng tối thiểu (nếu có)
        if ($voucher->don_hang_toi_thieu && $tongDonHang < $voucher->don_hang_toi_thieu) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng của bạn chưa đủ điều kiện tối thiểu (' . number_format($voucher->don_hang_toi_thieu) . 'đ)'
            ]);
        }

        // 3. Tính toán số tiền giảm
        $soTienGiam = 0;
        if ($voucher->loai == 'phan_tram') {
            $soTienGiam = ($tongDonHang * $voucher->gia_tri) / 100;
            // Kiểm tra mức giảm tối đa (nếu có)
            if ($voucher->giam_toi_da && $soTienGiam > $voucher->giam_toi_da) {
                $soTienGiam = $voucher->giam_toi_da;
            }
        } else {
            $soTienGiam = $voucher->gia_tri;
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã thành công!',
            'discount' => $soTienGiam,
            'new_total' => $tongDonHang - $soTienGiam,
            'voucher_ma' => $voucher->ma
        ]);
    }
}
