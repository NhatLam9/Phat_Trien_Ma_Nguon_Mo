
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MockTest\MockTestController;

// Trang chu
Route::view('/', 'welcome');

// MT01 - Danh sach de thi cong khai
Route::get('/mock-test', [
    MockTestController::class, 'index'
])->name('mock-test.index');

// Cac thao tac Mock Test yeu cau dang nhap
Route::middleware('auth')->group(function () {

    // MT01 - Bat dau bai thi
    Route::post('/mock-test/start', [
        MockTestController::class, 'start'
    ])->name('mock-test.start');

    // MT02 / MT04 - Xem phien thi
    Route::get('/mock-test/{id}', [
        MockTestController::class, 'show'
    ])->whereNumber('id')->name('mock-test.show');

    // MT02 / MT04 - Cap cau hoi tiep theo
    Route::post('/mock-test/{id}/question', [
        MockTestController::class, 'capCauHoi'
    ])->whereNumber('id')->name('mock-test.question.next');

    // MT08 / MT04 - Bo do hoac xu ly het gio
    Route::post('/mock-test/{id}/abandon', [
        MockTestController::class, 'abandon'
    ])->whereNumber('id')->name('mock-test.abandon');

});
