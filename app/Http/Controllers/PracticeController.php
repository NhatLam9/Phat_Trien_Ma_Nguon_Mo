<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PracticeController extends Controller
{
    // Hiển thị danh sách các dạng bài luyện tập DET
    public function index()
    {
        // Lấy danh sách dạng bài từ bảng loaicaouhoi hoặc danhmuc trong CSDL của nhóm
        $dsDangBai = DB::table('loaicaouhoi')->get();

        return view('practice.index', compact('dsDangBai'));
    }
}
