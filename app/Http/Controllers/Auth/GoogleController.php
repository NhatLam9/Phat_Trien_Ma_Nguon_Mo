<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Chuyển hướng sang trang đăng nhập Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Nhận thông tin trả về từ Google
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Kiểm tra xem người dùng đã tồn tại bằng google_id hoặc email chưa
            $user = Nguoidung::where('google_id', $googleUser->getId())
                             ->orWhere('email', $googleUser->getEmail())
                             ->first();

            if ($user) {
                // Nếu đã có tài khoản nhưng chưa liên kết google_id hoặc chưa xác thực email
                $updates = [];
                if (!$user->google_id) {
                    $updates['google_id'] = $googleUser->getId();
                }
                if (!$user->email_xacthuc) {
                    $updates['email_xacthuc'] = now();
                }
                if (!empty($updates)) {
                    $user->update($updates);
                }
            } else {
                // Nếu chưa có, tạo tài khoản mới (tài khoản Google đã xác thực email)
                $user = Nguoidung::create([
                    'hoten' => $googleUser->getName() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'email_xacthuc' => now(),
                    'google_id' => $googleUser->getId(),
                    'matkhau' => Hash::make(Str::random(16)), // Mật khẩu ngẫu nhiên cho tài khoản Google
                    'trangthai' => 'active',
                    'vaitro' => 'user',
                ]);
            }

            // Kiểm tra trạng thái tài khoản có bị khóa không
            if ($user->trangthai === 'locked') {
                return redirect()->route('login')->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.']);
            }

            // Đăng nhập hệ thống và tạo mới session ID (chống session fixation)
            Auth::login($user);
            $request->session()->regenerate();

            $fallback = $user->vaitro === 'admin'
                ? route('admin.dashboard')
                : route('practice.index');

            return redirect()->intended($fallback);
        } catch (QueryException $e) {
            Log::error('Lỗi kết nối CSDL khi đăng nhập Google: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Không thể kết nối cơ sở dữ liệu MySQL (vui lòng bật MySQL trong XAMPP Control Panel).'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi đăng nhập Google: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('login')->withErrors([
                'email' => 'Đăng nhập Google thất bại: ' . $e->getMessage()
            ]);
        }
    }
}
