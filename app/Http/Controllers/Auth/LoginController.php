<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        // Kiểm tra số lần thử đăng nhập thất bại (Rate limiting / throttle)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Quá nhiều lần đăng nhập không thành công. Vui lòng thử lại sau {$seconds} giây.",
            ])->onlyInput('email');
        }

        // Kiểm tra tài khoản có bị khóa không trước khi cho phép đăng nhập
        $user = Nguoidung::where('email', $credentials['email'])->first();
        if ($user && $user->trangthai === 'locked') {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.'])->onlyInput('email');
        }

        // Xác thực sử dụng guard mặc định trỏ về bảng nguoidung
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['matkhau']], $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        RateLimiter::hit($throttleKey, 60);

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
