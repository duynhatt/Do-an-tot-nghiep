<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MauSac extends Model
{
    use HasFactory;

    protected $table = 'mau_sacs';

    protected $fillable = [
        'ten_mau',
        'ma_mau',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];
}