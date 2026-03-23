<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    use HasFactory;

    protected $table = 'refund_items';

    protected $fillable = [
        'refund_request_id',
        'chi_tiet_don_hang_id',
        'so_luong_yeu_cau',
        'thanh_tien_yeu_cau',
    ];

    protected $casts = [
        'so_luong_yeu_cau'     => 'integer',
        'thanh_tien_yeu_cau'   => 'integer',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class, 'refund_request_id');
    }

    public function chiTietDonHang()
    {
        return $this->belongsTo(ChiTietDonHang::class, 'chi_tiet_don_hang_id');
    }

    public function tinhThanhTien(): int
    {
        if ($this->chiTietDonHang && $this->so_luong_yeu_cau) {
            return $this->so_luong_yeu_cau * $this->chiTietDonHang->don_gia;
        }
        return 0;
    }
}