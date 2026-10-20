<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chặn người dùng không phải quản trị viên (vaitro = admin).
 * Thuộc module Quản trị (TV3 - Phi Phong).
 */
class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            // Khi TV1 đã có route đăng nhập thì chuyển hướng, ngược lại trả 401.
            if (Route::has('login')) {
                return redirect()->route('login');
            }

            abort(401, 'Bạn cần đăng nhập để truy cập trang quản trị.');
        }

        if (($user->vaitro ?? null) !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}
