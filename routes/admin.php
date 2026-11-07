<?php

use App\Http\Controllers\Admin\AiLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\QuestionImportController;
use App\Http\Controllers\Admin\QuestionTypeController;
use App\Http\Controllers\Admin\UserController;
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

        // AD02 - Dạng bài (chỉ bật/tắt và sửa cấu hình, không đổi mã).
        Route::get('question-types', [QuestionTypeController::class, 'index'])
            ->name('question-types.index');
        Route::patch('question-types/{loai}', [QuestionTypeController::class, 'update'])
            ->name('question-types.update');

        // AD03 - Chủ đề.
        Route::get('categories', [CategoryController::class, 'index'])
            ->name('categories.index');
        Route::post('categories', [CategoryController::class, 'store'])
            ->name('categories.store');
        Route::get('categories/{danhmuc}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit');
        Route::patch('categories/{danhmuc}', [CategoryController::class, 'update'])
            ->name('categories.update');
        Route::delete('categories/{danhmuc}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        // AD07 - Đề thi: xem danh sách, tạo mới bằng giao diện và quản lý câu hỏi.
        Route::get('exams', [ExamController::class, 'index'])
            ->name('exams.index');

        // Đặt trước nhóm {exam} để "create" không bị route model binding nuốt.
        Route::get('exams/create', [ExamController::class, 'create'])
            ->name('exams.create');
        Route::post('exams', [ExamController::class, 'store'])
            ->name('exams.store');

        Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])
            ->whereNumber('exam')
            ->name('exams.edit');
        Route::patch('exams/{exam}', [ExamController::class, 'update'])
            ->whereNumber('exam')
            ->name('exams.update');

        Route::post('exams/{exam}/questions', [ExamController::class, 'addQuestions'])
            ->whereNumber('exam')
            ->name('exams.questions.add');
        Route::delete('exams/{exam}/questions/{question}', [ExamController::class, 'removeQuestion'])
            ->whereNumber('exam')
            ->whereNumber('question')
            ->name('exams.questions.remove');
        Route::patch('exams/{exam}/questions/{question}/move', [ExamController::class, 'moveQuestion'])
            ->whereNumber('exam')
            ->whereNumber('question')
            ->name('exams.questions.move');

        Route::get('exams/{exam}', [ExamController::class, 'show'])
            ->whereNumber('exam')
            ->name('exams.show');

        // AD04 - Người dùng: khóa/mở khóa và đổi vai trò.
        Route::get('users', [UserController::class, 'index'])
            ->name('users.index');
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])
            ->name('users.status');
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])
            ->name('users.role');

        // AD06 - Nhật ký AI (chỉ đọc).
        Route::get('ai-logs', [AiLogController::class, 'index'])
            ->name('ai-logs.index');
        Route::get('ai-logs/{id}', [AiLogController::class, 'show'])
            ->whereNumber('id')
            ->name('ai-logs.show');
    });
