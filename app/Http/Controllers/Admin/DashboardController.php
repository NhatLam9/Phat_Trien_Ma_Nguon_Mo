<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

/**
 * AD05 - Thống kê hệ thống (TV3 - Phi Phong).
 *
 * Dùng query builder thay vì model của thành viên khác để module
 * quản trị không phụ thuộc vào tiến độ TV1/TV2.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $thongke = [
            'nguoidung' => DB::table('nguoidung')->count(),
            'cauhoi' => DB::table('cauhoi')->count(),
            'cauhoi_published' => DB::table('cauhoi')->where('trangthai', 'published')->count(),
            'dethi' => DB::table('dethi')->count(),
            'lanthi' => DB::table('lanthi')->count(),
            'ailog' => DB::table('ailog')->count(),
            'ailog_homnay' => DB::table('ailog')
                ->whereDate('ngaytao', today())
                ->count(),
        ];

        return view('admin.dashboard', compact('thongke'));
    }
}
