<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ailog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AD06 - Nhật ký AI (TV3 - Phi Phong).
 *
 * Chỉ đọc: xem ai gọi AI, loại yêu cầu, model, token, trạng thái và trích đoạn đầu vào/đầu ra.
 * Giúp kiểm tra lỗi chấm Writing/Speaking mà không cần mở database.
 */
class AiLogController extends Controller
{
    /**
     * @var array<int, string>
     */
    public const LOAI = ['speaking', 'writing', 'grammar', 'translate', 'rewrite', 'chat'];

    /**
     * @var array<string, string>
     */
    public const LOAI_NHAN = [
        'speaking' => 'Speaking',
        'writing' => 'Writing',
        'grammar' => 'Ngữ pháp',
        'translate' => 'Dịch',
        'rewrite' => 'Viết lại',
        'chat' => 'Chat',
    ];

    public function index(Request $request): View
    {
        $boLoc = [
            'q' => trim((string) $request->query('q', '')),
            'loai' => (string) $request->query('loai', ''),
            'trangthai' => (string) $request->query('trangthai', ''),
            'tu_ngay' => (string) $request->query('tu_ngay', ''),
            'den_ngay' => (string) $request->query('den_ngay', ''),
        ];

        $duLieu = $request->validate([
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
            'loai' => ['nullable', Rule::in(self::LOAI)],
            'trangthai' => ['nullable', Rule::in(['success', 'failed'])],
        ], [
            'tu_ngay.date' => 'Từ ngày không hợp lệ.',
            'den_ngay.date' => 'Đến ngày không hợp lệ.',
            'den_ngay.after_or_equal' => 'Đến ngày phải sau hoặc bằng từ ngày.',
            'loai.in' => 'Loại yêu cầu không hợp lệ.',
            'trangthai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $ailogs = Ailog::query()
            ->with('nguoidung')
            ->when($boLoc['q'] !== '', function (Builder $query) use ($boLoc): void {
                $like = '%'.$boLoc['q'].'%';

                $query->whereHas('nguoidung', function (Builder $q) use ($like): void {
                    $q->where('hoten', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->when($boLoc['loai'] !== '', fn (Builder $query) => $query->where('loai', $boLoc['loai']))
            ->when($boLoc['trangthai'] !== '', fn (Builder $query) => $query->where('trangthai', $boLoc['trangthai']))
            ->when($duLieu['tu_ngay'] ?? null, fn (Builder $query, $ngay) => $query->whereDate('ngaytao', '>=', $ngay))
            ->when($duLieu['den_ngay'] ?? null, fn (Builder $query, $ngay) => $query->whereDate('ngaytao', '<=', $ngay))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.ai-logs.index', [
            'ailogs' => $ailogs,
            'boLoc' => $boLoc,
            'loaiNhan' => self::LOAI_NHAN,
            'trangThaiNhan' => [
                'success' => 'Thành công',
                'failed' => 'Thất bại',
            ],
            'coBoLoc' => collect($boLoc)->filter(fn ($giaTri) => $giaTri !== '')->isNotEmpty(),
        ]);
    }

    /**
     * Xem chi tiết một lượt gọi AI (đầu vào, đầu ra, trạng thái, mã HTTP).
     */
    public function show(int $id): View
    {
        $log = Ailog::with('nguoidung')->findOrFail($id);

        return view('admin.ai-logs.show', [
            'log' => $log,
            'loaiNhan' => self::LOAI_NHAN,
        ]);
    }
}
