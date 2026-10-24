<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    // Hiển thị form đặt lại mật khẩu mới
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Xử lý cập nhật mật khẩu mới
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'matkhau' => 'required|string|min:6|confirmed',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->matkhau,
            'password_confirmation' => $request->matkhau_confirmation,
            'token' => $request->token,
        ];

        $status = Password::broker()->reset(
            $credentials,
            function ($user, $password) {
                $user->forceFill([
                    'matkhau' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Mật khẩu của bạn đã được đặt lại thành công! Vui lòng đăng nhập.');
        }

        return back()->withErrors([
            'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
        ])->onlyInput('email');
    }
}

