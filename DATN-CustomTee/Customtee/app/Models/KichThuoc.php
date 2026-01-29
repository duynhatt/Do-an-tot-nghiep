<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KichThuoc extends Model
{
    use HasFactory;

    protected $table = 'kich_thuocs';

    protected $fillable = [
        'ten_kich_thuoc',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];
}