<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query()->orderBy('id', 'desc');

        $keyword = trim((string) $request->query('q', ''));
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('ma', 'like', "%{$keyword}%")
                    ->orWhere('ten', 'like', "%{$keyword}%");
            });
        }

        $vouchers = $query->paginate(12)->withQueryString();
        return view('admin.vouchers.index', compact('vouchers', 'keyword'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateVoucherRequest($request);
        Voucher::create($this->buildVoucherPayload($validated, true));

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
        $validated = $this->validateVoucherRequest($request, $voucher);
        $voucher->update($this->buildVoucherPayload($validated, false));

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
        $validated = $request->validate([
            'voucher_code' => 'required|string|max:50',
            'total_amount' => 'required|numeric|min:0',
        ], [
            'voucher_code.required' => 'Vui lòng nhập mã giảm giá.',
            'total_amount.required' => 'Thiếu giá trị đơn hàng để áp dụng voucher.',
            'total_amount.numeric' => 'Giá trị đơn hàng không hợp lệ.',
            'total_amount.min' => 'Giá trị đơn hàng không hợp lệ.',
        ]);

        $maVoucher = trim((string) $validated['voucher_code']);
        $tongDonHang = (float) $validated['total_amount'];
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

        if ($voucher->so_luong !== null && (int) $voucher->da_su_dung >= (int) $voucher->so_luong) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá này đã hết lượt sử dụng.'
            ]);
        }

        $maxPerUser = $voucher->max_per_user;
        if ($maxPerUser !== null && Auth::check()) {
            $usageCount = VoucherUsage::where('voucher_id', $voucher->id)
                ->where('user_id', Auth::id())
                ->count();
            if ($usageCount >= (int) $maxPerUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã dùng hết số lượt của mã này.'
                ]);
            }
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

        $soTienGiam = min((float) $soTienGiam, $tongDonHang);
        $newTotal = max(0, $tongDonHang - $soTienGiam);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã thành công!',
            'discount' => $soTienGiam,
            'new_total' => $newTotal,
            'voucher_ma' => $voucher->ma
        ]);
    }

    private function validateVoucherRequest(Request $request, ?Voucher $voucher = null): array
    {
        $voucherId = $voucher?->id;
        $validated = $request->validate([
            'ma' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('vouchers', 'ma')->ignore($voucherId),
            ],
            'loai' => 'required|in:phan_tram,tien_mat',
            'gia_tri' => 'required|numeric|min:1',
            'don_hang_toi_thieu' => 'nullable|numeric|min:0',
            'giam_toi_da' => 'nullable|numeric|min:0|required_if:loai,phan_tram',
            'bat_dau' => 'required|date',
            'ket_thuc' => 'required|date|after_or_equal:bat_dau',
            'so_luong' => 'required|integer|min:1',
            'max_per_user' => 'nullable|integer|min:1|max:255',
        ], [
            'ma.unique' => 'Mã voucher này đã tồn tại!',
            'ma.regex' => 'Mã voucher chỉ được chứa chữ, số, dấu gạch ngang hoặc gạch dưới.',
            'ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu!',
            'giam_toi_da.required_if' => 'Voucher giảm theo % bắt buộc nhập số tiền giảm tối đa.',
            'max_per_user.min' => 'Giới hạn mỗi khách phải lớn hơn hoặc bằng 1.',
        ]);

        $validated['ma'] = trim((string) $validated['ma']);

        $batDau = Carbon::parse((string) $validated['bat_dau']);
        $todayStart = now()->startOfDay();
        if ($batDau->lt($todayStart)) {
            // Khi sửa voucher cũ: cho phép giữ nguyên ngày bắt đầu cũ để chỉnh các trường khác.
            $isUnchangedExistingStart = $voucher
                && $voucher->bat_dau
                && $batDau->equalTo(Carbon::parse((string) $voucher->bat_dau));
            if (!$isUnchangedExistingStart) {
                throw ValidationException::withMessages([
                    'bat_dau' => 'Ngày bắt đầu không được nhỏ hơn hôm nay.',
                ]);
            }
        }

        if ($validated['loai'] === 'phan_tram' && (float) $validated['gia_tri'] > 100) {
            throw ValidationException::withMessages([
                'gia_tri' => 'Voucher giảm theo % không được vượt quá 100%.',
            ]);
        }

        if ($voucher) {
            $daSuDung = (int) ($voucher->da_su_dung ?? 0);
            if ((int) $validated['so_luong'] < $daSuDung) {
                throw ValidationException::withMessages([
                    'so_luong' => "Số lượng không được nhỏ hơn số lượt đã dùng ({$daSuDung}).",
                ]);
            }

            if ($validated['max_per_user'] !== null) {
                $newMaxPerUser = (int) $validated['max_per_user'];
                $isExceededByAnyUser = VoucherUsage::query()
                    ->where('voucher_id', $voucher->id)
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) > ?', [$newMaxPerUser])
                    ->exists();
                if ($isExceededByAnyUser) {
                    throw ValidationException::withMessages([
                        'max_per_user' => 'Giới hạn mỗi khách nhỏ hơn số lượt đã được sử dụng thực tế.',
                    ]);
                }
            }
        }

        return $validated;
    }

    private function buildVoucherPayload(array $validated, bool $isCreate): array
    {
        $payload = [
            'ma' => $validated['ma'],
            'ten' => $validated['ma'],
            'loai' => $validated['loai'],
            'gia_tri' => $validated['gia_tri'],
            'don_hang_toi_thieu' => $validated['don_hang_toi_thieu'] ?? null,
            'giam_toi_da' => $validated['loai'] === 'phan_tram'
                ? ($validated['giam_toi_da'] ?? null)
                : null,
            'bat_dau' => $validated['bat_dau'],
            'ket_thuc' => $validated['ket_thuc'],
            'so_luong' => $validated['so_luong'],
            'max_per_user' => $validated['max_per_user'] ?? null,
        ];

        if ($isCreate) {
            $payload['trang_thai'] = 1;
        }

        return $payload;
    }
}
