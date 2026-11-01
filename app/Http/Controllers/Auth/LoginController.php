<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Nguoidung;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm(Request $request)
    {
        $rememberedEmail = $request->cookie('remember_email');
        return view('auth.login', compact('rememberedEmail'));
    }

    /**
     * Xử lý đăng nhập sử dụng LoginRequest
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');

        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        // Kiểm tra số lần thử đăng nhập thất bại (Rate limiting / throttle)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Quá nhiều lần đăng nhập không thành công. Vui lòng thử lại sau {$seconds} giây.",
            ])->withInput($request->only('email', 'remember'));
        }

        // Kiểm tra tài khoản có bị khóa không trước khi cho phép đăng nhập
        $user = Nguoidung::where('email', $credentials['email'])->first();
        if ($user && $user->trangthai === 'locked') {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.'])->withInput($request->only('email', 'remember'));
        }

        // Xác thực sử dụng guard mặc định trỏ về bảng nguoidung
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['matkhau']], $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = $request->user();
            $fallback = $user->vaitro === 'admin'
                ? route('admin.dashboard')
                : route('practice.index');

            $intended = $request->session()->pull('url.intended');

            $targetUrl = ($intended && ($user->vaitro === 'admin' || !str_starts_with($intended, url('/admin'))))
                ? $intended
                : $fallback;

            $response = redirect()->to($targetUrl);

            if ($remember) {
                // Lưu cookie ghi nhớ email trong 30 ngày (43200 phút)
                $response->withCookie(cookie()->make('remember_email', $credentials['email'], 43200));
            } else {
                // Nếu người dùng không tích chọn, xóa cookie ghi nhớ cũ
                $response->withCookie(cookie()->forget('remember_email'));
            }

            return $response;
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Đăng xuất và đưa về trang chủ
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
