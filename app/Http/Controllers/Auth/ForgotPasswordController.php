<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Hiển thị form quên mật khẩu
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Xử lý gửi yêu cầu reset mật khẩu qua email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Sử dụng Password Broker chuẩn của Laravel
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Chúng tôi đã gửi liên kết đặt lại mật khẩu vào email của bạn!');
        }

        return back()->withErrors([
            'email' => 'Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại sau hoặc kiểm tra địa chỉ email.',
        ])->onlyInput('email');
    }
}
