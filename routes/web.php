
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\MockTest\MockTestController;
use App\Http\Controllers\MockTest\MockTestResultController;

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

    // PRACTICE - Luyen tap
    Route::get('/practice', [PracticeController::class, 'index'])
        ->name('practice.index');

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

});
