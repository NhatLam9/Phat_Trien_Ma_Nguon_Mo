<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionBankController;
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

        // Import JSON - giữ trước nhóm {question} để không bị route model binding nuốt.
        Route::get('questions/import', [QuestionImportController::class, 'create'])
            ->name('questions.import.create');
        Route::get('questions/import/template', [QuestionImportController::class, 'template'])
            ->name('questions.import.template');
        Route::post('questions/import/preview', [QuestionImportController::class, 'preview'])
            ->name('questions.import.preview');
        Route::post('questions/import', [QuestionImportController::class, 'store'])
            ->name('questions.import.store');

        // Ngân hàng câu hỏi - duyệt và xuất bản.
        Route::get('questions', [QuestionBankController::class, 'index'])
            ->name('questions.index');
        Route::post('questions/bulk-status', [QuestionBankController::class, 'bulkStatus'])
            ->name('questions.bulk-status');
        Route::get('questions/{question}', [QuestionBankController::class, 'show'])
            ->whereNumber('question')
            ->name('questions.show');
        Route::patch('questions/{question}/status', [QuestionBankController::class, 'updateStatus'])
            ->whereNumber('question')
            ->name('questions.status');
        Route::delete('questions/{question}', [QuestionBankController::class, 'destroy'])
            ->whereNumber('question')
            ->name('questions.destroy');
    });
