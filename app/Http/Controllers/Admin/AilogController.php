<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ailog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AilogController extends Controller
{
    /**
     * AD06 - Danh sách nhật ký AI với bộ lọc theo loại, người dùng, ngày.
     */
    public function index(Request $request): View
    {
        $loai = $request->query('loai');
        $trangthai = $request->query('trangthai');
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Ailog::with('nguoidung')->orderByDesc('ngaytao');

        if ($loai) {
            $query->where('loai', $loai);
        }

        if (in_array($trangthai, ['success', 'failed'], true)) {
            $query->where('trangthai', $trangthai);
        }

        if ($search) {
            $query->whereHas('nguoidung', function ($q) use ($search) {
                $q->where('hoten', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('ngaytao', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('ngaytao', '<=', $dateTo);
        }

        $logs = $query->paginate(20)->withQueryString();

        $loaiOptions = ['speaking', 'writing', 'grammar', 'translate', 'rewrite', 'chat'];
        $trangthaiOptions = ['success' => 'Thành công', 'failed' => 'Thất bại'];

        return view('admin.ai-logs.index', compact('logs', 'loai', 'trangthai', 'search', 'dateFrom', 'dateTo', 'loaiOptions', 'trangthaiOptions'));
    }

    /**
     * AD06 - Xem chi tiết 1 bản ghi nhật ký AI.
     */
    public function show(int $id): View
    {
        $log = Ailog::with('nguoidung')->findOrFail($id);

        return view('admin.ai-logs.show', compact('log'));
    }
}
