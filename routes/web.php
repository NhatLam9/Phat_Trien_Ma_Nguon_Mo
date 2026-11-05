<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\MockTest\MockTestController;
use App\Http\Controllers\MockTest\MockTestDashboardController;
use App\Http\Controllers\MockTest\MockTestHistoryController;
use App\Http\Controllers\MockTest\MockTestResultController;
use App\Http\Controllers\Practice\PracticeAIController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tutor\TutorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Certificate\CertificateController;

/*
|--------------------------------------------------------------------------
| Web Routes - DET Practice System
| Phân hệ Tài khoản (TK), Luyện tập (LT) [TV1], AI [TV3] & Mock Test [TV2]
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', function () {
    return view('welcome');
});

// =========================================================================
// PHÂN HỆ TÀI KHOẢN (AUTHENTICATION - TV1)
// =========================================================================

// TK03: Đăng nhập (Login)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');

// TK01: Đăng ký tài khoản (Register)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// TK05: Đăng xuất (Logout) - Hỗ trợ cả POST và GET, chuyển hướng về trang chủ
Route::match(['get', 'post'], '/logout', LogoutController::class)->name('logout');

// TK04: Đăng nhập Google qua Socialite
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// TK06: Quên và đặt lại mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// TK02: Xác minh Email (Email Verification)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
});

// TK07: Hồ sơ cá nhân (Profile Management)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// =========================================================================
// PHÂN HỆ LUYỆN TẬP (PRACTICE - TV1) - Bắt buộc người dùng đăng nhập
// =========================================================================
Route::middleware('auth')->prefix('practice')->name('practice.')->group(function () {
    // LT01: Danh sách các dạng bài luyện tập DET nhóm theo 4 kỹ năng
    Route::get('/', [PracticeController::class, 'index'])->name('index');

    // LT02 & LT09: Chọn bài luyện tập theo Dạng bài & Cấp độ độ khó (gộp theo tiêu đề kỹ năng)
    Route::get('/select/{loai_id?}', [PracticeController::class, 'select'])->name('select');

    // LUYỆN TẬP LIÊN TIẾP THEO DẠNG BÀI & ĐỘ KHÓ (Tự do, không đếm ngược thời gian, hiện kết quả & giải thích tức thì)
    Route::get('/session', [PracticeController::class, 'session'])->name('session');
    Route::post('/session/submit', [PracticeController::class, 'submitSession'])->name('session.submit');
    Route::get('/session/result', [PracticeController::class, 'sessionResult'])->name('session.result');

    // Chấm điểm & kiểm tra bài nói Speaking bằng AI qua nút voice ghi âm
    Route::post('/speaking/evaluate', [PracticeController::class, 'evaluateSpeaking'])->name('speaking.evaluate');

    // Tương thích ngược với liên kết mã đề cũ
    Route::get('/set/{code}', [PracticeController::class, 'showSet'])->name('set.show');
    Route::post('/set/{code}/submit', [PracticeController::class, 'submitSet'])->name('set.submit');
    Route::get('/set/{code}/result', [PracticeController::class, 'resultSet'])->name('set.result');

    // LT08 & LT09: Lịch sử luyện tập và tìm kiếm lịch sử
    Route::get('/history', [PracticeController::class, 'history'])->name('history');

    // LT07: Quản lý câu hỏi yếu đã đánh dấu (Bookmarks) & Dạng bài quan tâm
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');
    Route::post('/question/{cauhoi_id}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::post('/type/{loai_id}/bookmark', [BookmarkController::class, 'toggleType'])->name('bookmark.type.toggle');
    Route::get('/bookmarks/practice', [BookmarkController::class, 'practiceSaved'])->name('bookmarks.practice');

    // LT03: Trang làm bài luyện tập
    Route::get('/question/{id}', [PracticeController::class, 'show'])->name('show');

    // LT04: Nộp bài luyện tập
    Route::post('/question/{id}/submit', [PracticeController::class, 'submit'])->name('submit');

    // LT05: Xem kết quả bài luyện tập và đáp án giải thích
    Route::get('/result/{id}', [PracticeController::class, 'result'])->name('result');

    // LT06: Chuyển bài Writing/Speaking sang cho AI chấm
    Route::post('/result/{id}/ai', [PracticeController::class, 'submitAI'])->name('ai');

    // ===================================================
    // PRACTICE AI - Luyện tập chấm điểm Writing/Speaking (TV3)
    // ===================================================
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/question/{id}', [PracticeAIController::class, 'showQuestion'])
            ->whereNumber('id')->name('question');
        Route::post('/submit/{id}', [PracticeAIController::class, 'submit'])
            ->whereNumber('id')->name('submit');
        Route::get('/status/{id}', [PracticeAIController::class, 'status'])
            ->whereNumber('id')->name('status');
        Route::get('/audio/{id}', [PracticeAIController::class, 'audio'])
            ->whereNumber('id')->name('audio');
        Route::post('/retry/{id}', [PracticeAIController::class, 'retry'])
            ->whereNumber('id')->name('retry');
    });
});

// =========================================================================
// PHÂN HỆ THI THỬ (MOCK TEST - TV2)
// =========================================================================
Route::get('/mock-test/{id}/result', [MockTestResultController::class, 'show'])
    ->whereNumber('id')
    ->name('mock-test.result');

// MT01 - Danh sách đề thi công khai
Route::get('/mock-test', [MockTestController::class, 'index'])
    ->name('mock-test.index');

Route::middleware('auth')->group(function () {
    // MT07 - Lịch sử thi của người dùng
    Route::get('/mock-test/history', [MockTestHistoryController::class, 'index'])
        ->name('mock-test.history');

    // MT09 - Cấp và xem chứng chỉ
    Route::post('/mock-test/{id}/certificate', [CertificateController::class, 'issue'])
        ->whereNumber('id')->name('certificate.issue');

    Route::get('/certificate/{id}', [CertificateController::class, 'show'])
        ->whereNumber('id')->name('certificate.show');

    // DB01-DB05 - Tiến độ và thống kê học tập
    Route::get('/learning-dashboard', [MockTestDashboardController::class, 'index'])
        ->name('learning.dashboard');

    // MT01 - Bắt đầu hoặc tiếp tục bài thi
    Route::post('/mock-test/start', [MockTestController::class, 'start'])
        ->name('mock-test.start');

    // MT02 / MT04 - Hiển thị phiên thi
    Route::get('/mock-test/{id}', [MockTestController::class, 'show'])
        ->whereNumber('id')->name('mock-test.show');

    // MT02 - Cấp câu hỏi
    Route::post('/mock-test/{id}/question', [MockTestController::class, 'capCauHoi'])
        ->whereNumber('id')->name('mock-test.question.next');

    // MT02 - Bỏ qua câu hỏi
    Route::post('/mock-test/{id}/skip', [MockTestController::class, 'skip'])
        ->whereNumber('id')->name('mock-test.skip');

    // MT03 - Nộp câu trả lời
    Route::post('/mock-test/{id}/submit', [MockTestController::class, 'submit'])
        ->whereNumber('id')->name('mock-test.submit');

    // MT05 - Chốt bài thi
    Route::post('/mock-test/{id}/finish', [MockTestController::class, 'finish'])
        ->whereNumber('id')->name('mock-test.finish');

    // MT08 / MT04 - Bỏ dở hoặc xử lý hết giờ
    Route::post('/mock-test/{id}/abandon', [MockTestController::class, 'abandon'])
        ->whereNumber('id')->name('mock-test.abandon');

    // ==========================================
    // AI TUTOR - Trợ lý học tập thông minh (TV3)
    // ==========================================
    Route::prefix('tutor')->name('tutor.')->group(function () {
        Route::get('/', [TutorController::class, 'index'])->name('index');
        Route::get('/grammar', [TutorController::class, 'showGrammar'])->name('grammar');
        Route::post('/grammar', [TutorController::class, 'processGrammar'])->name('grammar.process');
        Route::get('/translate', [TutorController::class, 'showTranslate'])->name('translate');
        Route::post('/translate', [TutorController::class, 'processTranslate'])->name('translate.process');
        Route::get('/rewrite', [TutorController::class, 'showRewrite'])->name('rewrite');
        Route::post('/rewrite', [TutorController::class, 'processRewrite'])->name('rewrite.process');
        Route::get('/history', [TutorController::class, 'history'])->name('history');
    });
});

// Phục vụ file tĩnh trong thư mục storage trên môi trường Windows / XAMPP
Route::get('/storage/{path}', function ($path) {
    $filePath = public_path('storage/'.$path);
    if (! file_exists($filePath)) {
        $filePath = storage_path('app/public/'.$path);
    }
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*')->name('storage.file');
