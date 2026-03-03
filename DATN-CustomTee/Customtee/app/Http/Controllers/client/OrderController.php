<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function list()
    {
        $donHangs = DonHang::where('nguoi_dung_id', Auth::id())
            ->with(['chiTietDonHangs.sanPham', 'chiTietDonHangs.bienThe.color', 'chiTietDonHangs.bienThe.size'])
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('client.order.index', compact('donHangs'));
    }

    public function show($id)
    {
        $donHang = DonHang::where('id', $id)
            ->where('nguoi_dung_id', Auth::id())
            ->with([
                'chiTietDonHangs.sanPham',
                'chiTietDonHangs.bienThe.color',
                'chiTietDonHangs.bienThe.size',
                'nguoiDung'
            ])
            ->firstOrFail();

        return view('client.order.show', compact('donHang'));
    }
}
