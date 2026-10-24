<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PracticeController extends Controller
{
    // Hiển thị danh sách các dạng bài luyện tập DET
    public function index()
    {
        // Lấy danh sách dạng bài từ bảng loaicauhoi nếu bảng đã tồn tại (do TV3 quản lý)
        $dsDangBai = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : collect();

        return view('practice.index', compact('dsDangBai'));
    }
}
