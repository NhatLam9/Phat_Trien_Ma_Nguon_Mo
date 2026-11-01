<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Module Quản trị (TV3 - Phi Phong)
            if (file_exists(base_path('routes/admin.php'))) {
                Route::middleware('web')->group(base_path('routes/admin.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Bỏ qua kiểm tra CSRF token cho thao tác đăng xuất để tránh lỗi 419 Page Expired khi session hết hạn
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        // Đăng ký alias cho middleware admin (TV3) và verified (TV1)
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'verified' => \App\Http\Middleware\Verified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
