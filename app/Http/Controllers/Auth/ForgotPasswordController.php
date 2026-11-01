<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Nguoidung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form quên mật khẩu (nhập email)
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Xử lý gửi yêu cầu đặt lại mật khẩu qua email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        // Sử dụng Password Broker chuẩn của Laravel
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Chúng tôi đã gửi liên kết đặt lại mật khẩu vào email của bạn!');
        }

        return back()->withErrors([
            'email' => 'Không tìm thấy tài khoản với địa chỉ email này hoặc không thể gửi liên kết khôi phục.',
        ])->onlyInput('email');
    }

    /**
     * Hiển thị form đặt lại mật khẩu mới từ liên kết có token
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.passwords.email', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Xử lý lưu mật khẩu mới (yêu cầu >= 8 ký tự, chữ hoa, thường, số và ký tự đặc biệt)
     */
    public function reset(Request $request)
    {
        // Cho phép nhận cả trường 'matkhau' hoặc 'password'
        if ($request->has('password') && !$request->has('matkhau')) {
            $request->merge([
                'matkhau' => $request->input('password'),
                'matkhau_confirmation' => $request->input('password_confirmation'),
            ]);
        }

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'matkhau' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                'confirmed',
            ],
        ], [
            'token.required' => 'Mã khôi phục mật khẩu không hợp lệ.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'matkhau.required' => 'Mật khẩu mới không được để trống.',
            'matkhau.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'matkhau.regex' => 'Mật khẩu mới phải bao gồm chữ hoa, chữ thường, chữ số và ký tự đặc biệt.',
            'matkhau.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $status = Password::broker()->reset(
            [
                'email' => $request->input('email'),
                'password' => $request->input('matkhau'),
                'password_confirmation' => $request->input('matkhau_confirmation'),
                'token' => $request->input('token'),
            ],
            function (Nguoidung $user, string $newPassword) {
                $user->forceFill([
                    'matkhau' => Hash::make($newPassword),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Đặt lại mật khẩu thành công! Bây giờ bạn có thể đăng nhập bằng mật khẩu mới.');
        }

        return back()->withErrors([
            'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng yêu cầu gửi lại liên kết mới.',
        ])->onlyInput('email');
    }
}
