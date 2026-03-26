<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class DonHang extends Model
{
    use HasFactory;

    /** Trạng thái đơn hàng */
    const TRANG_THAI_CHO_XAC_NHAN = 'cho_xac_nhan';
    const TRANG_THAI_DANG_XU_LY = 'dang_xu_ly';
    const TRANG_THAI_DANG_GIAO = 'dang_giao';
    const TRANG_THAI_DA_GIAO = 'da_giao';
    const TRANG_THAI_DA_HOAN_THANH = 'da_hoan_thanh';
    const TRANG_THAI_DA_HUY = 'da_huy';

    /** Các trạng thái có thể chuyển từ trạng thái hiện tại (admin) */
    public static function trangThaiTiepTheo(string $trangThaiHienTai): array
    {
        $map = [
            self::TRANG_THAI_CHO_XAC_NHAN => [
                self::TRANG_THAI_DANG_XU_LY   => 'Đang xử lý',
                self::TRANG_THAI_DA_HUY       => 'Đã hủy',
            ],
            self::TRANG_THAI_DANG_XU_LY => [
                self::TRANG_THAI_DANG_GIAO    => 'Đang giao',
                self::TRANG_THAI_DA_HUY       => 'Đã hủy',
            ],
            self::TRANG_THAI_DANG_GIAO => [
                self::TRANG_THAI_DA_GIAO      => 'Đã giao',
            ],
            self::TRANG_THAI_DA_GIAO => [
                self::TRANG_THAI_DA_HOAN_THANH => 'Đã hoàn thành',
            ],
            self::TRANG_THAI_DA_HOAN_THANH => [], // Kết thúc
            self::TRANG_THAI_DA_HUY        => [],  // Kết thúc
        ];
        return $map[$trangThaiHienTai] ?? [];
    }

    /** Tên hiển thị trạng thái */
    public static function tenTrangThai(string $trangThai): string
    {
        $ten = [
            self::TRANG_THAI_CHO_XAC_NHAN   => 'Chờ xác nhận',
            self::TRANG_THAI_DANG_XU_LY     => 'Đang xử lý',
            self::TRANG_THAI_DANG_GIAO      => 'Đang giao',
            self::TRANG_THAI_DA_GIAO        => 'Đã giao',
            self::TRANG_THAI_DA_HOAN_THANH  => 'Đã hoàn thành',
            self::TRANG_THAI_DA_HUY         => 'Đã hủy',
        ];
        return $ten[$trangThai] ?? $trangThai;
    }
    /** Kiểm tra có thể chuyển sang trạng thái mới không */
    public static function coTheChuyenSang(string $tuTrangThai, string $sangTrangThai): bool
    {
        $tiepTheo = self::trangThaiTiepTheo($tuTrangThai);
        return array_key_exists($sangTrangThai, $tiepTheo);
    }

    protected $table = 'don_hangs';

    protected $fillable = [
        'nguoi_dung_id',
        'dia_chi_id',
        'voucher_id',
        'ma_don_hang',
        'tam_tinh',
        'tien_giam',
        'phi_van_chuyen',
        'tong_tien',
        'phuong_thuc_thanh_toan',
        'vnp_TxnRef',
        'vnp_PayDate',
        'vnp_TransactionNo',
        'trang_thai_thanh_toan',
        'trang_thai',
        'ghi_chu',
        'dia_chi_chi_tiet',
        'so_dien_thoai_nhan_hang',
        'ten_nguoi_nhan',
        'yeu_cau_tra',
        'ly_do_tra',
        'ngay_yeu_cau_tra',
        'da_giao_at',
    ];

    protected $casts = [
        'tam_tinh' => 'float',
        'tien_giam' => 'float',
        'phi_van_chuyen' => 'float',
        'tong_tien' => 'float',
        'yeu_cau_tra' => 'boolean',
        'ngay_yeu_cau_tra' => 'datetime',
        'da_giao_at' => 'datetime',
    ];


    // Người đặt hàng
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'don_hang_id');
    }

    public function checkAutoCancel()
    {
        if (
            $this->trang_thai_thanh_toan === 'chua_thanh_toan' &&
            $this->trang_thai === 'cho_xac_nhan' &&
            // Chỉ auto-hủy khi "thanh toán trực tuyến" hết hạn.
            // Với COD, trạng thái 'chua_thanh_toan' là bình thường vì thanh toán khi nhận hàng.
            $this->phuong_thuc_thanh_toan === 'vnpay' &&
            // Dùng copy() để tránh Carbon mutate trong cùng request.
            $this->created_at->copy()->addMinutes(15)->isPast()
        ) {

            DB::transaction(function () {
                // Chỉ hoàn tồn kho khi hệ thống đã trừ tồn trước đó.
                // - COD: đã trừ tồn ngay khi tạo đơn.
                // - VNPAY chưa thanh toán: chưa trừ tồn, hoàn kho sẽ gây cộng gấp đôi.
                $shouldRefundInventory = $this->phuong_thuc_thanh_toan === 'cod'
                    || $this->trang_thai_thanh_toan === 'da_thanh_toan';

                // Với VNPAY chưa thanh toán: đã reserve giỏ bằng `da_dat_hang`,
                // cần đưa lại các dòng giỏ về "đang trong giỏ" khi auto-hủy.
                $shouldRestoreCart = $this->phuong_thuc_thanh_toan === 'vnpay'
                    && $this->trang_thai_thanh_toan !== 'da_thanh_toan';

                foreach ($this->chiTietDonHangs as $item) {
                    if ($shouldRefundInventory && $item->bienThe) {
                        $item->bienThe->increment('so_luong', $item->so_luong);
                    }

                    if ($shouldRestoreCart) {
                        GioHang::where('nguoi_dung_id', $this->nguoi_dung_id)
                            ->where('san_pham_id', $item->san_pham_id)
                            ->where('bien_the_id', $item->bien_the_id)
                            ->where('trang_thai', GioHang::TRANG_THAI_DA_DAT_HANG)
                            ->update(['trang_thai' => GioHang::TRANG_THAI_DANG_TRONG_GIO]);
                    }
                }

                $this->update([
                    'trang_thai' => 'da_huy',
                    'ly_do_tra' => 'Hết hạn thanh toán',
                    'ngay_yeu_cau_tra' => now(),
                    'ghi_chu' => 'Tự động hủy đơn hàng vì khách hàng quá hạn thanh toán'
                ]);
            });
        }
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'don_hang_id');
    }
}
