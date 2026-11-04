<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Verified
{
    /**
     * Handle an incoming request.
     * Kiểm tra người dùng đã xác minh email chưa, nếu chưa sẽ chặn và chuyển về trang thông báo xác minh.
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        if (!$request->user() || !$request->user()->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Địa chỉ email của bạn chưa được xác thực.'], 403)
                : redirect()->route($redirectToRoute ?: 'verification.notice');
        }

        return $next($request);
    }
}

