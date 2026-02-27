<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;

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
        'trang_thai_thanh_toan',
        'trang_thai',
        'ghi_chu',
        'dia_chi_chi_tiet',
        'so_dien_thoai_nhan_hang',
        'ten_nguoi_nhan'
    ];

    protected $casts = [
        'tam_tinh' => 'float',
        'tien_giam' => 'float',
        'phi_van_chuyen' => 'float',
        'tong_tien' => 'float',
    ];


    // Người đặt hàng
    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    // // Địa chỉ nhận hàng
    // public function diaChi()
    // {
    //     return $this->belongsTo(DiaChi::class, 'dia_chi_id');
    // }

    // // Voucher
    // public function voucher()
    // {
    //     return $this->belongsTo(Voucher::class, 'voucher_id');
    // }

    // Chi tiết đơn hàng
    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'don_hang_id');
    }
}