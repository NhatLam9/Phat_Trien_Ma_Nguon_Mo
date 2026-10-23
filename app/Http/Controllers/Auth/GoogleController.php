<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Chuyển hướng sang trang đăng nhập Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Nhận thông tin trả về từ Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Kiểm tra xem người dùng đã tồn tại bằng google_id hoặc email chưa
            $user = Nguoidung::where('google_id', $googleUser->id)
                             ->orWhere('email', $googleUser->email)
                             ->first();

            if ($user) {
                // Nếu đã có tài khoản nhưng chưa liên kết google_id, cập nhật lại google_id
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            } else {
                // Nếu chưa có, tạo tài khoản mới
                $user = Nguoidung::create([
                    'hoten' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'matkhau' => Hash::make(Str::random(16)), // Mật khẩu ngẫu nhiên cho tài khoản Google
                    'trangthai' => 'active',
                    'vaitro' => 'user',
                ]);
            }

            // Kiểm tra trạng thái tài khoản có bị khóa không
            if ($user->trangthai === 'locked') {
                return redirect()->route('login')->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.']);
            }

            // Đăng nhập hệ thống
            Auth::login($user);

            return redirect()->intended('/practice');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Đăng nhập Google thất bại. Vui lòng thử lại.']);
        }
    }
}