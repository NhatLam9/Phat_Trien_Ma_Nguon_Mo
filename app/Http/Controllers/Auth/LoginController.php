<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Nguoidung;

class LoginController extends Controller
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'matkhau' => ['required'],
        ]);

        // Kiểm tra tài khoản có bị khóa không trước khi cho phép đăng nhập
        $user = Nguoidung::where('email', $credentials['email'])->first();
        if ($user && $user->trangthai === 'locked') {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.']);
        }

        // Xác thực sử dụng guard mặc định trỏ về bảng nguoidung
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['matkhau']], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}