<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cauhoi;
use App\Models\Loaicauhoi;
use App\Services\QuestionBankService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AD02 - Ngan hang cau hoi (TV3 - Phi Phong).
 *
 * Noi dung cau hoi duoc tao bang cach import JSON (QuestionImportController).
 * Man hinh nay lo phan duyet: xem danh sach, xem chi tiet, chuyen trang thai
 * draft -> review -> published -> archived, va xoa cau hoi nhap.
 */
class QuestionBankController extends Controller
{
    public function index(Request $request, QuestionBankService $service): View
    {
        $boLoc = [
            'q' => trim((string) $request->query('q', '')),
            'loai' => (string) $request->query('loai', ''),
            'kynang' => (string) $request->query('kynang', ''),
            'trangthai' => (string) $request->query('trangthai', ''),
            'dokho' => (string) $request->query('dokho', ''),
        ];

        $cauhois = Cauhoi::query()
            ->with(['loai', 'danhmuc'])
            ->withCount(['luachon', 'cauhoiDoc', 'cauhoiNghe', 'dethicauhoi'])
            ->timKiem($boLoc['q'])
            ->loaiCauHoi($boLoc['loai'])
            ->kyNang($boLoc['kynang'])
            ->trangThai($boLoc['trangthai'])
            ->doKho($boLoc['dokho'])
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.questions.index', [
            'cauhois' => $cauhois,
            'boLoc' => $boLoc,
            'loais' => Loaicauhoi::query()->orderBy('kynang')->orderBy('ten')->get(),
            'thongKe' => $service->thongKeTrangThai(),
            'coBoLoc' => collect($boLoc)->filter(fn ($giaTri) => $giaTri !== '')->isNotEmpty(),
        ]);
    }

    public function show(Cauhoi $question, QuestionBankService $service): View
    {
        $question->load([
            'loai',
            'danhmuc',
            'nguoiTao',
            'luachon',
            'cauhoiDoc.luachonDoc',
            'cauhoiNghe.luachonNghe',
            'dethicauhoi.dethi',
        ]);

        return view('admin.questions.show', [
            'cauhoi' => $question,
            'trangThaiChoPhep' => QuestionBankService::TRANSITIONS[$question->trangthai] ?? [],
            'loiXuatBan' => $service->loiKhiXuatBan($question),
        ]);
    }

    public function updateStatus(
        Request $request,
        Cauhoi $question,
        QuestionBankService $service
    ): RedirectResponse {
        $duLieu = $request->validate([
            'trangthai' => ['required', Rule::in(Cauhoi::TRANGTHAI)],
        ], [
            'trangthai.required' => 'Vui lòng chọn trạng thái mới.',
            'trangthai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $ketQua = $service->chuyenTrangThai($question, $duLieu['trangthai']);

        if (! $ketQua['ok']) {
            return back()
                ->with('error', $ketQua['message'])
                ->withErrors($ketQua['errors'] === [] ? [] : ['trangthai' => $ketQua['errors']]);
        }

        return back()->with('success', $ketQua['message']);
    }

    public function bulkStatus(Request $request, QuestionBankService $service): RedirectResponse
    {
        $duLieu = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:cauhoi,id'],
            'trangthai' => ['required', Rule::in(Cauhoi::TRANGTHAI)],
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một câu hỏi.',
            'ids.min' => 'Vui lòng chọn ít nhất một câu hỏi.',
            'ids.*.exists' => 'Có câu hỏi không tồn tại.',
            'trangthai.required' => 'Vui lòng chọn trạng thái mới.',
            'trangthai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $ketQua = $service->chuyenTrangThaiHangLoat(
            array_map('intval', $duLieu['ids']),
            $duLieu['trangthai']
        );

        $thongBao = "Đã cập nhật {$ketQua['updated']} câu hỏi.";

        if ($ketQua['skipped'] !== []) {
            $thongBao .= ' Bỏ qua '.count($ketQua['skipped']).' câu chưa đủ điều kiện.';
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', $thongBao)
            ->with('bulk_skipped', $ketQua['skipped']);
    }

    public function destroy(Cauhoi $question, QuestionBankService $service): RedirectResponse
    {
        $ketQua = $service->xoa($question);

        return redirect()
            ->route('admin.questions.index')
            ->with($ketQua['ok'] ? 'success' : 'error', $ketQua['message']);
    }
}
