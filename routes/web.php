<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MockTest\MockTestController;

// Trang chu
Route::view('/', 'welcome');

// MT01 - Danh sach de thi
Route::get('/mock-test', [
    MockTestController::class, 'index'
])->name('mock-test.index');

// MT01 - Cac thao tac yeu cau dang nhap
Route::middleware('auth')->group(function () {

    Route::post('/mock-test/start', [
        MockTestController::class, 'start'
    ])->name('mock-test.start');

    Route::get('/mock-test/{id}', [
        MockTestController::class, 'show'
    ])->whereNumber('id')->name('mock-test.show');

});
