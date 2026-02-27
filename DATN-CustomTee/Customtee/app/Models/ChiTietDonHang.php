<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;

    protected $table = 'don_hang_chi_tiets';

    protected $fillable = [
        'don_hang_id',
        'san_pham_id',
        'bien_the_id',
        'don_gia',
        'so_luong',
        'thanh_tien'
    ];

    protected $casts = [
        'don_gia' => 'float',
        'so_luong' => 'integer',
        'thanh_tien' => 'float',
    ];


    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    public function bienThe()
    {
        return $this->belongsTo(BienThe::class, 'bien_the_id');
    }
}