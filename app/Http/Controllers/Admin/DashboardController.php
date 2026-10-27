<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            'nguoidung' => $this->countTable('nguoidung'),
            'cauhoi' => $this->countTable('cauhoi'),
            'cauhoi_published' => Schema::hasTable('cauhoi')
                ? DB::table('cauhoi')->where('trangthai', 'published')->count()
                : 0,
            'dethi' => $this->countTable('dethi'),
            'lanthi' => $this->countTable('lanthi'),
            'ailog' => $this->countTable('ailog'),
            'ailog_homnay' => Schema::hasTable('ailog')
                ? DB::table('ailog')->whereDate('ngaytao', today())->count()
                : 0,
        ];

        return view('admin.dashboard', compact('thongke'));
    }

    private function countTable(string $table): int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }
}
