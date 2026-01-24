<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SanPham extends Model
{
    use HasFactory;

    protected $table = 'san_phams';

    protected $fillable = [
        'danh_muc_id',
        'ten_san_pham',
        'slug',
        'mo_ta_ngan',
        'mo_ta_chi_tiet',
        'hinh_anh_chinh',
        'cho_phep_thiet_ke',
        'trang_thai',
    ];

    protected $casts = [
        'cho_phep_thiet_ke' => 'boolean',
        'trang_thai'        => 'boolean',
    ];

    public function danhMuc()
    {
        return $this->belongsTo(Category::class, 'danh_muc_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sanPham) {
            $sanPham->slug = Str::slug($sanPham->ten_san_pham);
        });

        static::updating(function ($sanPham) {
            if ($sanPham->isDirty('ten_san_pham')) {
                $sanPham->slug = Str::slug($sanPham->ten_san_pham);
            }
        });
    }
}