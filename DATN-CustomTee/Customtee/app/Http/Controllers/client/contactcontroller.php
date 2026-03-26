<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // [CLIENT] Hiển thị trang liên hệ cho khách
    public function Contact() {
        return view('client.Contact'); 
    }

    // [CLIENT] Lưu dữ liệu khi khách gửi Form liên hệ
    public function store(Request $request) {
        $request->validate([
            'tieu_de'  => 'required|max:255',
            'noi_dung' => 'required',
        ], [
            'tieu_de.required'  => 'Vui lòng nhập tiêu đề',
            'tieu_de.max'       => 'Tiêu đề không quá 255 ký tự',
            'noi_dung.required' => 'Vui lòng nhập nội dung liên hệ',
        ]);

        DB::table('lien_hes')->insert([
            'user_id'    => Auth::id(), // Tự động lấy ID nếu khách đã đăng nhập
            'tieu_de'    => $request->tieu_de,
            'noi_dung'   => $request->noi_dung,
            'trang_thai' => '0', // 0: Chờ xử lý (Lưu dạng integer cho chuẩn DB)
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn! Thông tin liên hệ đã được gửi đi.');
    }

    // --- [ADMIN] QUẢN LÝ ---

    // Danh sách liên hệ trong Admin
    public function index() {
        $contacts = DB::table('lien_hes')
            ->leftJoin('users', 'lien_hes.user_id', '=', 'users.id')
            ->select('lien_hes.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('lien_hes.trang_thai', 'asc') // Ưu tiên cái chưa xử lý lên đầu
            ->latest('lien_hes.created_at')
            ->paginate(10);

        return view('admin.contact.index', compact('contacts'));
    }

    // Cập nhật trạng thái xử lý
    public function updateStatus($id) {
        $contact = DB::table('lien_hes')->where('id', $id)->first();
        
        if ($contact) {
            // Đảo trạng thái: Nếu 0 thì thành 1, nếu 1 thì thành 0
            $newStatus = ($contact->trang_thai == 0) ? 1 : 0;
            
            DB::table('lien_hes')->where('id', $id)->update([
                'trang_thai' => $newStatus,
                'updated_at' => now()
            ]);

            return back()->with('success', 'Cập nhật trạng thái liên hệ thành công!');
        }

        return back()->with('error', 'Không tìm thấy thông tin liên hệ.');
    }

    // Xóa liên hệ
    public function destroy($id) {
        DB::table('lien_hes')->where('id', $id)->delete();
        return back()->with('success', 'Đã xóa liên hệ thành công!');
    }
}