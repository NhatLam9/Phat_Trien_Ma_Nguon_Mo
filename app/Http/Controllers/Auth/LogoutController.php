<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Xử lý đăng xuất người dùng khỏi hệ thống:
     * - Hủy session đăng nhập
     * - Xóa thông tin xác thực
     * - Tạo lại token CSRF
     * - Chuyển hướng người dùng về trang chủ (/)
     */
    public function __invoke(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
