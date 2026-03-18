<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $fillable = [
        'ma', 'ten', 'mo_ta', 'loai', 'gia_tri', 
        'giam_toi_da', 'don_hang_toi_thieu', 'so_luong', 
        'da_su_dung', 'bat_dau', 'ket_thuc', 'trang_thai'
    ];
}