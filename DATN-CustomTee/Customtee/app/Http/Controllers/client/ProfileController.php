<?php


// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers\Client;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('client.Profile', compact('user'));
    }

  public function update(Request $request)
{
    $request->validate([
        'name'   => 'required|string|max:255',
        'phone'  => 'nullable|string|max:20',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $user = auth()->user();
    $changed = false;

    // So sánh name
    if ($request->name !== $user->name) {
        $user->name = $request->name;
        $changed = true;
    }

    // So sánh phone
    if ($request->phone !== $user->phone) {
        $user->phone = $request->phone;
        $changed = true;
    }

    // Upload avatar
    if ($request->hasFile('avatar')) {

        // Xóa avatar cũ
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $changed = true;
    }

    // ❌ Không có thay đổi
    if (!$changed) {
        return back()->with('error', 'Bạn chưa thay đổi thông tin nào.');
    }

    // ✅ Có thay đổi
    $user->save();

    return back()->with('success', 'Cập nhật thông tin thành công.');
}


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công');
    }
}
