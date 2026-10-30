<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionImportController;
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

        Route::get('questions/import', [QuestionImportController::class, 'create'])
            ->name('questions.import.create');
        Route::get('questions/import/template', [QuestionImportController::class, 'template'])
            ->name('questions.import.template');
        Route::post('questions/import/preview', [QuestionImportController::class, 'preview'])
            ->name('questions.import.preview');
        Route::post('questions/import', [QuestionImportController::class, 'store'])
            ->name('questions.import.store');
    });
