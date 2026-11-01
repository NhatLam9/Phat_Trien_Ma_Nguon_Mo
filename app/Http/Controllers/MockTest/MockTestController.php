<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MockTestController extends Controller
{
    // MT01: Danh sách đề thi.
    public function index(Request $request, TestService $service)
    {
        $deThi = DB::table('dethi')
            ->where('trangthai', 'published')
            ->orderBy('id')
            ->get();

        $lanThiDangMo = null;

        if ($request->user()) {
            $nguoiDungId = (int) $request->user()->getAuthIdentifier();

            $lanThiDangMo = Lanthi::query()
                ->where('nguoidung_id', $nguoiDungId)
                ->where('trangthai', 'in_progress')
                ->first();

            if ($lanThiDangMo) {
                $lanThiDangMo = $service->kiemTraHetHan(
                    $lanThiDangMo->id,
                    $nguoiDungId
                );

                if ($lanThiDangMo->trangthai !== 'in_progress') {
                    $lanThiDangMo = null;
                }
            }
        }

        return view('mock-test.start', compact(
            'deThi',
            'lanThiDangMo'
        ));
    }

    // MT01: Bắt đầu hoặc tiếp tục bài thi.
    public function start(Request $request, TestService $service)
    {
        $validated = $request->validate([
            'dethi_id' => ['required', 'integer', 'exists:dethi,id'],
        ]);

        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = $service->batDauThi(
            $nguoiDungId,
            (int) $validated['dethi_id']
        );

        return redirect()
            ->route('mock-test.show', ['id' => $lanThi->id])
            ->with('success', 'Đã bắt đầu hoặc tiếp tục bài thi.');
    }

    // MT02 / MT04: Hiển thị câu hỏi và kiểm tra thời gian.
    public function show(Request $request, int $id, TestService $service)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = $service->kiemTraHetHan($id, $nguoiDungId);
        if ($lanThi->trangthai === 'completed') {
            return redirect()->route('mock-test.result', ['id' => $lanThi->id]);
        }

        $deThi = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->first();

        abort_if(!$deThi, 404, 'Không tìm thấy đề thi.');

        $hanThi = $service->layHanThi($lanThi);

        $soGiayConLai = $lanThi->trangthai === 'in_progress'
            ? max(0, (int) ceil(now()->diffInSeconds($hanThi, false)))
            : 0;

        $biHetGio = DB::table('vipham')
            ->where('lanthi_id', $lanThi->id)
            ->where('loai', 'timeout')
            ->exists();

        // Danh sách câu hỏi đã cấp.
        $dsCauDaCap = DB::table('lanthicauhoi')
            ->where('lanthi_id', $lanThi->id)
            ->orderBy('thutu')
            ->get([
                'id',
                'cauhoi_id',
                'thutu',
                'datraloi',
            ]);

        $soCauDaCap = $dsCauDaCap->count();

        // Số câu hỏi thực tế trong đề.
        $soCauTrongDe = DB::table('dethicauhoi')
            ->where('dethi_id', $lanThi->dethi_id)
            ->count();

        $tongSoCau = $soCauTrongDe;

        $cauDuocCap = null;
        $cauHoi = null;
        $luaChon = collect();
        $cauTruoc = null;
        $cauSau = null;

        if ($lanThi->trangthai === 'in_progress') {
            $cauYeuCau = $request->query('cau');

            // Kiểm tra ID câu hỏi trên URL.
            if ($cauYeuCau !== null) {
                abort_unless(
                    is_string($cauYeuCau)
                    && ctype_digit($cauYeuCau)
                    && (int) $cauYeuCau > 0,
                    404
                );
            }

            // Các câu chưa nộp đáp án.
            $dsChuaTraLoi = $dsCauDaCap
                ->filter(fn ($cau) => (int) $cau->datraloi === 0);

            // Chọn câu được yêu cầu hoặc câu chưa làm mới nhất.
            $cauDuocCap = $cauYeuCau !== null
                ? $dsChuaTraLoi->firstWhere('id', (int) $cauYeuCau)
                : $dsChuaTraLoi->last();

            if ($cauYeuCau !== null && !$cauDuocCap) {
                abort(404, 'Câu hỏi không tồn tại hoặc đã được nộp.');
            }

            if ($cauDuocCap) {
                // Lấy câu hỏi từ ngân hàng câu hỏi.
                $cauHoi = DB::table('cauhoi')
                    ->join(
                        'loaicauhoi',
                        'loaicauhoi.id',
                        '=',
                        'cauhoi.loai_id'
                    )
                    ->where('cauhoi.id', $cauDuocCap->cauhoi_id)
                    ->select(
                        'cauhoi.id',
                        'cauhoi.noidung',
                        'cauhoi.anh',
                        'cauhoi.audio',
                        'cauhoi.thoigian',
                        'loaicauhoi.ten as tenloai',
                        'loaicauhoi.kynang'
                    )
                    ->firstOrFail();

                // Lấy đáp án trắc nghiệm.
                $luaChon = DB::table('luachon')
                    ->where('cauhoi_id', $cauHoi->id)
                    ->orderBy('thutu')
                    ->orderBy('id')
                    ->get(['id', 'noidung']);

                // Câu trước: chỉ mở các câu chưa nộp.
                $cauTruoc = $dsChuaTraLoi
                    ->filter(fn ($cau) =>
                        (int) $cau->thutu < (int) $cauDuocCap->thutu
                    )
                    ->last();

                // Câu sau đã được cấp nhưng chưa nộp.
                $cauSau = $dsChuaTraLoi
                    ->first(fn ($cau) =>
                        (int) $cau->thutu > (int) $cauDuocCap->thutu
                    );
            }
        }

        // Phiếu trả lời.
        $phieuTraLoi = collect();

        for ($i = 1; $i <= $tongSoCau; $i++) {
            $cau = $dsCauDaCap->firstWhere('thutu', $i);

            if (!$cau) {
                $trangThai = 'empty';
            } elseif ((int) $cau->datraloi === 1) {
                $trangThai = 'answered';
            } elseif (
                $cauDuocCap
                && (int) $cau->id === (int) $cauDuocCap->id
            ) {
                $trangThai = 'current';
            } else {
                $trangThai = 'seen';
            }

            $phieuTraLoi->push([
                'thutu' => $i,
                'status' => $trangThai,
                'lanthicauhoi_id' => $cau
                    && (int) $cau->datraloi === 0
                    ? (int) $cau->id
                    : null,
            ]);
        }

        // Đếm câu chưa được cấp.
        $soCauHopLeConLai = 0;

        if ($lanThi->trangthai === 'in_progress') {
            $soCauHopLeConLai = DB::table('dethicauhoi as dc')
                ->join(
                    'cauhoi as ch',
                    'ch.id',
                    '=',
                    'dc.cauhoi_id'
                )
                ->where('dc.dethi_id', $lanThi->dethi_id)
                ->where('ch.trangthai', 'published')
                ->whereNotIn('ch.id', function ($query) use ($lanThi) {
                    $query->select('cauhoi_id')
                        ->from('lanthicauhoi')
                        ->where('lanthi_id', $lanThi->id);
                })
                ->count();
        }

        // Các câu đã cấp nhưng chưa trả lời.
        $cauChuaTraLoi = DB::table('lanthicauhoi')
            ->where('lanthi_id', $lanThi->id)
            ->where('datraloi', false)
            ->orderBy('thutu')
            ->get(['id', 'thutu']);

        return view('mock-test.question', compact(
            'lanThi',
            'deThi',
            'cauDuocCap',
            'cauHoi',
            'luaChon',
            'soCauDaCap',
            'soCauTrongDe',
            'soCauHopLeConLai',
            'hanThi',
            'soGiayConLai',
            'biHetGio',
            'cauChuaTraLoi',
            'dsCauDaCap',
            'tongSoCau',
            'phieuTraLoi',
            'cauTruoc',
            'cauSau'
        ));
    }

    // MT02: Cấp câu hỏi đầu tiên hoặc tiếp theo.
    public function capCauHoi(
        Request $request,
        int $id,
        TestService $service
    ) {
        $service->capCauHoiTiepTheo(
            $id,
            (int) $request->user()->getAuthIdentifier()
        );

        return redirect()->route('mock-test.show', ['id' => $id]);
    }

    // MT02 / MT03: Lưu đáp án và chuyển sang câu sau.
    public function skip(
        Request $request,
        int $id,
        TestService $service
    ) {
        $data = $request->validate([
            'lanthicauhoi_id' => ['required', 'integer'],
            'luachon_id' => ['nullable', 'integer'],
            'noidung' => ['nullable', 'string', 'max:10000'],
        ]);

        $nguoiDungId = (int) $request->user()->getAuthIdentifier();
        $cauHienTaiId = (int) $data['lanthicauhoi_id'];

        // Xác minh phiên thi và quyền sở hữu.
        $lanThi = $service->kiemTraHetHan($id, $nguoiDungId);

        if ($lanThi->trangthai !== 'in_progress') {
            return redirect()->route('mock-test.show', ['id' => $id]);
        }

        // Chỉ thao tác trên câu chưa được nộp của phiên này.
        $cauHienTai = Lanthicauhoi::query()
            ->whereKey($cauHienTaiId)
            ->where('lanthi_id', $lanThi->id)
            ->where('datraloi', false)
            ->firstOrFail();

        $tongSoCau = DB::table('dethicauhoi')
            ->where('dethi_id', $lanThi->dethi_id)
            ->count();

        // Không cho nút Câu sau quay vòng tại câu cuối.
        // Câu cuối phải sử dụng nút Nộp bài.
        if ((int) $cauHienTai->thutu >= $tongSoCau) {
            return redirect()
                ->route('mock-test.show', [
                    'id' => $id,
                    'cau' => $cauHienTaiId,
                ])
                ->withErrors([
                    'lanthi' => 'Đây là câu cuối. Vui lòng nhấn Nộp bài.',
                ]);
        }

        $luaChonId = isset($data['luachon_id'])
            ? (int) $data['luachon_id']
            : null;

        $noiDung = trim($data['noidung'] ?? '');

        // Có câu trả lời: lưu trước khi chuyển câu.
        if ($luaChonId !== null || $noiDung !== '') {
            $traLoi = $service->nopCauTraLoi(
                $id,
                $nguoiDungId,
                $cauHienTaiId,
                $luaChonId,
                $noiDung
            );

            if ($traLoi === null) {
                return redirect()->route('mock-test.show', ['id' => $id]);
            }
        }

        // Cấp câu tiếp theo hoặc chuyển đến câu đã cấp phía sau.
        $cauTiepTheo = $service->capCauHoiTiepTheo(
            $id,
            $nguoiDungId,
            true,
            $cauHienTaiId
        );

        if (!$cauTiepTheo) {
            return redirect()
                ->route('mock-test.show', ['id' => $id])
                ->with(
                    'success',
                    'Không còn câu hỏi mới. Bạn có thể nộp bài thi.'
                );
        }

        return redirect()->route('mock-test.show', [
            'id' => $id,
            'cau' => $cauTiepTheo->id,
        ]);
    }

    // MT03: Nộp câu trả lời độc lập (giữ tương thích).
    public function submit(
        Request $request,
        int $id,
        TestService $service
    ) {
        $data = $request->validate([
            'lanthicauhoi_id' => ['required', 'integer'],
            'luachon_id' => ['nullable', 'integer'],
            'noidung' => ['nullable', 'string', 'max:10000'],
        ]);

        $traLoi = $service->nopCauTraLoi(
            $id,
            (int) $request->user()->getAuthIdentifier(),
            (int) $data['lanthicauhoi_id'],
            isset($data['luachon_id'])
                ? (int) $data['luachon_id']
                : null,
            $data['noidung'] ?? null
        );

        if ($traLoi === null) {
            return redirect()
                ->route('mock-test.show', ['id' => $id])
                ->with(
                    'success',
                    'Bài thi đã hết thời gian. Câu trả lời không được ghi nhận.'
                );
        }

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with('success', 'Đã lưu câu trả lời thành công.');
    }

    // MT05: Nộp bài, đồng thời lưu đáp án câu cuối.
    public function finish(
        Request $request,
        int $id,
        TestService $service
    ) {
        $data = $request->validate([
            'lanthicauhoi_id' => ['nullable', 'integer'],
            'luachon_id' => ['nullable', 'integer'],
            'noidung' => ['nullable', 'string', 'max:10000'],
            'xac_nhan_bo_trong' => ['nullable', 'in:1'],
        ]);

        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $luaChonId = isset($data['luachon_id'])
            ? (int) $data['luachon_id']
            : null;

        $noiDung = trim($data['noidung'] ?? '');

        // Nếu có đáp án câu cuối, lưu trước khi chốt.
        if ($luaChonId !== null || $noiDung !== '') {
            if (empty($data['lanthicauhoi_id'])) {
                throw ValidationException::withMessages([
                    'lanthicauhoi_id' =>
                        'Không xác định được câu hỏi cần nộp.',
                ]);
            }

            $traLoi = $service->nopCauTraLoi(
                $id,
                $nguoiDungId,
                (int) $data['lanthicauhoi_id'],
                $luaChonId,
                $noiDung
            );

            if ($traLoi === null) {
                return redirect()
                    ->route('mock-test.show', ['id' => $id]);
            }
        }

        $lanThi = $service->chotBaiThi(
            $id,
            $nguoiDungId,
            ($data['xac_nhan_bo_trong'] ?? null) === '1'
        );

            return redirect()->route('mock-test.result', ['id' => $lanThi->id]);
    }

    // MT08: Bỏ dở bài thi.
    public function abandon(
        Request $request,
        int $id,
        TestService $service
    ) {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = $service->boDoBaiThi($id, $nguoiDungId);

        $biHetGio = DB::table('vipham')
            ->where('lanthi_id', $lanThi->id)
            ->where('loai', 'timeout')
            ->exists();

        $thongBao = $biHetGio
            ? 'Bài thi đã hết thời gian quy định.'
            : 'Bạn đã bỏ dở bài thi. Kết quả sẽ không được tính điểm.';

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with('success', $thongBao);
    }
}
