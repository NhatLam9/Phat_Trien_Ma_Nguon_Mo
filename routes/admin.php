<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes quản trị (TV3 - Phi Phong)
|--------------------------------------------------------------------------
| Chỉ tài khoản có vaitro = 'admin' mới truy cập được.
| Tiền tố URL: /admin, tên route: admin.*
*/

Route::middleware('admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
