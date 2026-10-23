<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Hiển thị form quên mật khẩu
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Xử lý gửi yêu cầu reset (giả lập thông báo thành công cho đồ án)
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:nguoidung,email']);

        // Logic gửi token đặt lại mật khẩu (có thể tích hợp gửi mail thực tế qua Mailtrap/Log)
        return back()->with('status', 'Chúng tôi đã gửi liên kết đặt lại mật khẩu vào email của bạn!');
    }
}