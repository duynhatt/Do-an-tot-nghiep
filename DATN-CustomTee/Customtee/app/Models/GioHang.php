<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'gio_hangs';

    const TRANG_THAI_DANG_TRONG_GIO = 'dang_trong_gio';
    const TRANG_THAI_DA_DAT_HANG = 'da_dat_hang';

    protected $fillable = [
        'nguoi_dung_id',
        'san_pham_id',
        'bien_the_id',
        'thiet_ke_ao_id',
        'so_luong',
        'don_gia',
        'thanh_tien',
        'trang_thai',
    ];

    protected $casts = [
        'don_gia'   => 'decimal:0',
        'thanh_tien' => 'decimal:0',
    ];

    protected static function booted(): void
    {
        static::saving(function (GioHang $gioHang) {
            $gioHang->thanh_tien = (int) round($gioHang->so_luong * $gioHang->don_gia);
        });
    }

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    public function bienThe()
    {
        return $this->belongsTo(BienThe::class, 'bien_the_id');
    }

    public function scopeDangTrongGio($query)
    {
        return $query->where('trang_thai', self::TRANG_THAI_DANG_TRONG_GIO);
    }
}
