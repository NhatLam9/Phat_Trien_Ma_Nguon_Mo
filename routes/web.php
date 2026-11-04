
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\MockTest\MockTestController;
use App\Http\Controllers\MockTest\MockTestDashboardController;
use App\Http\Controllers\MockTest\MockTestHistoryController;
use App\Http\Controllers\MockTest\MockTestResultController;
use App\Http\Controllers\Certificate\CertificateController;
use App\Http\Controllers\Tutor\TutorController;
use App\Http\Controllers\Practice\PracticeAIController;

// Trang chu
Route::view('/', 'welcome');

// AUTH - Dang nhap
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:6,1')->name('login.submit');

// AUTH - Dang ky
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// AUTH - Dang xuat
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// AUTH - Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

// AUTH - Khoi phuc mat khau
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
Route::get('/mock-test/{id}/result', [MockTestResultController::class, 'show'])
    ->whereNumber('id')
    ->name('mock-test.result');
// MT01 - Danh sach de thi cong khai
Route::get('/mock-test', [MockTestController::class, 'index'])
    ->name('mock-test.index');

// Cac chuc nang yeu cau dang nhap
Route::middleware('auth')->group(function () {

    // // MT07 - Lich su thi cua nguoi dung dang dang nhap
    // Route::get('/mock-test/history', [MockTestHistoryController::class, 'index'])
    //     ->name('mock-test.history');

    // // MT09 - Cap va xem chung chi cho luot thi cua nguoi dung dang nhap
    // Route::post('/mock-test/{id}/certificate', [CertificateController::class, 'issue'])
    //     ->whereNumber('id')->name('certificate.issue');
    // Route::get('/certificate/{id}', [CertificateController::class, 'show'])
    //     ->whereNumber('id')->name('certificate.show');

    // // DB01-DB05 - Dashboard tien do va thong ke cua nguoi hoc
    // Route::get('/learning-dashboard', [MockTestDashboardController::class, 'index'])
    //     ->name('learning.dashboard');

    // // PRACTICE - Luyen tap
    // Route::get('/practice', [PracticeController::class, 'index'])
    //     ->name('practice.index');

    // MT01 - Bat dau hoac tiep tuc bai thi
    Route::post('/mock-test/start', [MockTestController::class, 'start'])
        ->name('mock-test.start');

    // MT02 / MT04 - Hien thi phien thi
    Route::get('/mock-test/{id}', [MockTestController::class, 'show'])
        ->whereNumber('id')->name('mock-test.show');

    // MT02 - Cap cau hoi
    Route::post('/mock-test/{id}/question', [MockTestController::class, 'capCauHoi'])
        ->whereNumber('id')->name('mock-test.question.next');

    // MT02 - Bo qua cau hoi
    Route::post('/mock-test/{id}/skip', [MockTestController::class, 'skip'])
        ->whereNumber('id')->name('mock-test.skip');

    // MT03 - Nop cau tra loi
    Route::post('/mock-test/{id}/submit', [MockTestController::class, 'submit'])
        ->whereNumber('id')->name('mock-test.submit');

    // MT05 - Chot bai thi
    Route::post('/mock-test/{id}/finish', [MockTestController::class, 'finish'])
        ->whereNumber('id')->name('mock-test.finish');

    // MT08 / MT04 - Bo do hoac xu ly het gio
    Route::post('/mock-test/{id}/abandon', [MockTestController::class, 'abandon'])
        ->whereNumber('id')->name('mock-test.abandon');

    // ==========================================
    // AI TUTOR - Tro ly hoc tap thong minh (TV3)
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

    // ===================================================
    // PRACTICE AI - Luyen tap cham diem Writing/Speaking
    // ===================================================
    Route::prefix('practice/ai')->name('practice.ai.')->group(function () {
        Route::get('/question/{id}', [PracticeAIController::class, 'showQuestion'])
            ->whereNumber('id')->name('question');
        Route::post('/submit/{id}', [PracticeAIController::class, 'submit'])
            ->whereNumber('id')->name('submit');
        Route::get('/status/{id}', [PracticeAIController::class, 'status'])
            ->whereNumber('id')->name('status');
        Route::post('/retry/{id}', [PracticeAIController::class, 'retry'])
            ->whereNumber('id')->name('retry');
    });

});