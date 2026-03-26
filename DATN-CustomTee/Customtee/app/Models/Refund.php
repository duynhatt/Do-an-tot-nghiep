<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'refunds';

    protected $fillable = [
        'don_hang_id',
        'user_id',
        'trang_thai',
        'ly_do',
        'so_tien_yeu_cau',
        'phuong_thuc_thanh_toan',
        'ngan_hang',
        'so_tai_khoan',
        'chi_nhanh',
        'ten_chu_tk',
        'qr_code_bank'
    ];

    protected $casts = [
        'so_tien_yeu_cau' => 'integer',
    ];

    // Quan hệ với đơn hàng
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }

    // Quan hệ với người dùng yêu cầu hoàn tiền
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với các sản phẩm chi tiết trong yêu cầu hoàn tiền
    public function items()
    {
        return $this->hasMany(RefundItem::class, 'refund_request_id');
    }

    // Helper: trạng thái dạng text đẹp
    public function getTrangThaiTextAttribute(): string
    {
        $map = [
            'cho_xu_ly'     => 'Chờ xử lý',
            'da_chap_nhan'  => 'Đã chấp nhận',
            'da_tu_choi'    => 'Đã từ chối',
            'da_hoan_tien'  => 'Đã hoàn tiền',
        ];

        return $map[$this->trang_thai] ?? $this->trang_thai;
    }

    // Helper: kiểm tra yêu cầu đã được xử lý xong chưa
    public function getDaXuLyAttribute(): bool
    {
        return in_array($this->trang_thai, ['da_chap_nhan', 'da_tu_choi', 'da_hoan_tien']);
    }

    public function images()
    {
        return $this->hasMany(RefundImage::class);
    }
}
