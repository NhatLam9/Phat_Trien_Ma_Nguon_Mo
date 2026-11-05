<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dethi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AD07 - Quản lý đề thi (TV3 - Phi Phong).
 *
 * Màn hình chỉ đọc: liệt kê đề thi đã tạo (từ import JSON, seeder hoặc
 * luồng làm đề) kèm số câu, thời gian, trạng thái và người tạo. Việc tạo
 * đề được thực hiện qua chức năng Nhập câu hỏi từ JSON để giữ đúng quy
 * trình duyệt nội dung.
 */
class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $boLoc = [
            'q' => trim((string) $request->query('q', '')),
            'trangthai' => (string) $request->query('trangthai', ''),
            'kynang' => (string) $request->query('kynang', ''),
        ];

        $dethis = Dethi::query()
            ->with('nguoiTao')
            ->withCount('dethicauhoi')
            ->when($boLoc['q'] !== '', function (Builder $query) use ($boLoc): void {
                $like = '%'.$boLoc['q'].'%';

                $query->where(function (Builder $q) use ($like): void {
                    $q->where('ten', 'like', $like)
                        ->orWhere('mota', 'like', $like);
                });
            })
            ->when(
                in_array($boLoc['trangthai'], ['draft', 'published', 'hidden'], true),
                fn (Builder $query) => $query->where('trangthai', $boLoc['trangthai'])
            )
            ->when(
                in_array($boLoc['kynang'], ['reading', 'listening', 'writing', 'speaking'], true),
                fn (Builder $query) => $query->where('kynang', $boLoc['kynang'])
            )
            ->orderByDesc('ngaytao')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.exams.index', [
            'dethis' => $dethis,
            'boLoc' => $boLoc,
            'coBoLoc' => collect($boLoc)->filter(fn ($giaTri) => $giaTri !== '')->isNotEmpty(),
            'thongKe' => [
                'tong' => Dethi::query()->count(),
                'published' => Dethi::query()->where('trangthai', 'published')->count(),
                'draft' => Dethi::query()->where('trangthai', 'draft')->count(),
                'hidden' => Dethi::query()->where('trangthai', 'hidden')->count(),
            ],
        ]);
    }

    public function show(Dethi $exam): View
    {
        $exam->load([
            'nguoiTao',
            'dethicauhoi.cauhoi.loai',
            'dethicauhoi.cauhoi.danhmuc',
        ]);

        return view('admin.exams.show', [
            'dethi' => $exam,
        ]);
    }
}
