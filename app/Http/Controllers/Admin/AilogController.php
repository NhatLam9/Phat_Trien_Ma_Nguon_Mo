<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ailog;
use App\Models\Nguoidung;
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
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Ailog::with('nguoidung')->orderByDesc('ngaytao');

        if ($loai) {
            $query->where('loai', $loai);
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

        return view('admin.ai-logs.index', compact('logs', 'loai', 'search', 'dateFrom', 'dateTo', 'loaiOptions'));
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

