<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index() {
        // Join với bảng users để lấy tên người gửi
        $contacts = DB::table('lien_hes')
            ->leftJoin('users', 'lien_hes.user_id', '=', 'users.id')
            ->select('lien_hes.*', 'users.name as user_name', 'users.email as user_email')
            ->latest('lien_hes.created_at')
            ->paginate(10);

        return view('admin.contact.index', compact('contacts'));
    }

public function updateStatus($id) {
    // 1. Lấy dữ liệu của liên hệ này ra trước
    $contact = DB::table('lien_hes')->where('id', $id)->first();

    if ($contact) {
        // 2. Kiểm tra: Nếu đang 'da_xu_ly' thì đổi thành 'moi', và ngược lại
        $statusMoi = ($contact->trang_thai == 'da_xu_ly') ? 'moi' : 'da_xu_ly';

        // 3. Cập nhật vào Database
        DB::table('lien_hes')->where('id', $id)->update([
            'trang_thai' => $statusMoi,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    return back()->with('error', 'Không tìm thấy liên hệ!');
}

    public function destroy($id) {
        DB::table('lien_hes')->where('id', $id)->delete();
        return back()->with('success', 'Đã xóa liên hệ thành công!');
    }
}