<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /**
     * Hiển thị thông báo yêu cầu người dùng xác minh email.
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended('/practice')
            : view('auth.verify');
    }

    /**
     * Đánh dấu địa chỉ email của người dùng là đã xác minh.
     */
    public function verify(Request $request)
    {
        $user = $request->user();

        if (!hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            return redirect()->route('verification.notice')->withErrors(['email' => 'Liên kết xác minh không hợp lệ.']);
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            return redirect()->route('verification.notice')->withErrors(['email' => 'Mã xác minh không khớp.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/practice')->with('status', 'Email của bạn đã được xác minh trước đó.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended('/practice')->with('status', 'Xác minh địa chỉ email thành công!');
    }

    /**
     * Gửi lại email xác minh cho người dùng.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/practice');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Đã gửi liên kết xác minh mới vào hộp thư của bạn!');
    }
}

