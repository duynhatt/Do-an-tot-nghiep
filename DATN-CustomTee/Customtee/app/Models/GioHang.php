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

    /**
     * Cập nhật giá và số lượng từ bien_thes hiện tại
     * - Cập nhật don_gia nếu giá thay đổi
     * - Điều chỉnh so_luong nếu tồn kho không đủ
     */
    public function syncGiaMoi()
    {
        if ($this->bienThe) {
            $giaMoi = $this->bienThe->gia_khuyen_mai ?? $this->bienThe->gia;
            $soLuongTon = $this->bienThe->so_luong;

            // Cập nhật giá nếu thay đổi
            if ($giaMoi != $this->don_gia) {
                $this->don_gia = $giaMoi;
            }

            // Điều chỉnh số lượng nếu tồn kho không đủ
            if ($this->so_luong > $soLuongTon) {
                $this->so_luong = $soLuongTon;
            }

        }
        return $this;
    }
}
