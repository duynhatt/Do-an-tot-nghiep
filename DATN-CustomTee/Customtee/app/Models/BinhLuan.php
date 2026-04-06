<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;

    protected $table = 'binh_luans';

    protected $fillable = [
        'user_id',
        'san_pham_id',
        'don_hang_id',
        'noi_dung',
        'so_sao',
        'trang_thai',
        'hien_thi_trang_chu',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
        'hien_thi_trang_chu' => 'boolean',
    ];

    // Người bình luận
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sản phẩm được bình luận
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }
}