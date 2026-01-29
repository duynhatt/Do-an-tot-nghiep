<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'danh_mucs';

    protected $fillable = [
        'ten_danh_muc',
        'slug',
        'mo_ta',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'integer',
    ];

    public function scopeHienThi($query)
    {
        return $query->where('trang_thai', 1);
    }
}
