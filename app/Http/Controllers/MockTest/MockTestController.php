<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\CauhoiDoc;
use App\Models\CauhoiNghe;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Traloi;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MockTestController extends Controller
{
    public function index(Request $request, TestService $service)
    {
        $deThi = DB::table('dethi')
            ->where('trangthai', 'published')
            ->orderBy('capdo')
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
                    (int) $lanThiDangMo->id,
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

    public function start(Request $request, TestService $service)
    {
        $data = $request->validate([
            'dethi_id' => ['required', 'integer', 'exists:dethi,id'],
            'xac_nhan_bat_dau' => ['nullable', 'in:1'],
        ]);

        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $deThi = DB::table('dethi')
            ->where('id', (int) $data['dethi_id'])
            ->where('trangthai', 'published')
            ->first();

        abort_if(! $deThi, 404, 'Đề thi không tồn tại hoặc chưa xuất bản.');

        // Chọn đề chỉ mở trang chuẩn bị, chưa tạo lượt thi.
        if (($data['xac_nhan_bat_dau'] ?? null) !== '1') {
            $lanThiDangMo = Lanthi::query()
                ->where('nguoidung_id', $nguoiDungId)
                ->where('trangthai', 'in_progress')
                ->first();

            if ($lanThiDangMo) {
                $lanThiDangMo = $service->kiemTraHetHan(
                    (int) $lanThiDangMo->id,
                    $nguoiDungId
                );

                if ($lanThiDangMo->trangthai === 'in_progress') {
                    return redirect()->route('mock-test.show', [
                        'id' => $lanThiDangMo->id,
                    ]);
                }
            }

            return view('mock-test.prepare', compact('deThi'));
        }

        // Bắt đầu tính giờ và cấp câu đầu tiên trong cùng transaction.
        $lanThi = DB::transaction(function () use (
            $service,
            $nguoiDungId,
            $data
        ) {
            $lanThi = $service->batDauThi(
                $nguoiDungId,
                (int) $data['dethi_id']
            );

            $service->capCauHoiTiepTheo(
                (int) $lanThi->id,
                $nguoiDungId
            );

            return $lanThi;
        }, 3);

        return redirect()
            ->route('mock-test.show', ['id' => $lanThi->id])
            ->with('success', 'Bài thi đang diễn ra.');
    }

    public function show(Request $request, int $id, TestService $service)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();
        $lanThi = $service->kiemTraHetHan($id, $nguoiDungId);

        if ($lanThi->trangthai === 'completed') {
            return redirect()->route('mock-test.result', ['id' => $id]);
        }

        $deThi = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->first();

        abort_if(! $deThi, 404, 'Không tìm thấy đề thi.');

        $hanThi = $service->layHanThi($lanThi);

        $soGiayConLai = $lanThi->trangthai === 'in_progress'
            ? max(0, (int) ceil(now()->diffInSeconds($hanThi, false)))
            : 0;

        $biHetGio = DB::table('vipham')
            ->where('lanthi_id', $id)
            ->where('loai', 'timeout')
            ->exists();

        $dsCauDaCap = DB::table('lanthicauhoi')
            ->where('lanthi_id', $id)
            ->orderBy('thutu')
            ->get(['id', 'cauhoi_id', 'thutu', 'datraloi']);

        $soCauDaCap = $dsCauDaCap->count();

        $soCauTrongDe = (int) DB::table('dethicauhoi')
            ->where('dethi_id', $lanThi->dethi_id)
            ->count();

        $tongSoCau = max(
            $soCauTrongDe,
            (int) ($dsCauDaCap->max('thutu') ?? 0)
        );

        $cauDuocCap = null;
        $cauHoi = null;
        $luaChon = collect();
        $cauHoiCon = collect();
        $luaChonCon = collect();
        $cauTruoc = null;
        $cauSau = null;

        $cauChuaTraLoi = $dsCauDaCap
            ->filter(fn ($cau) => ! (bool) $cau->datraloi)
            ->values();

        if ($lanThi->trangthai === 'in_progress') {
            $cauYeuCau = $request->query('cau');

            if ($cauYeuCau !== null) {
                abort_unless(
                    is_string($cauYeuCau)
                        && ctype_digit($cauYeuCau)
                        && (int) $cauYeuCau > 0,
                    404
                );
            }

            $cauDuocCap = $cauYeuCau !== null
                ? $cauChuaTraLoi->firstWhere('id', (int) $cauYeuCau)
                : $cauChuaTraLoi->last();

            if ($cauYeuCau !== null && ! $cauDuocCap) {
                abort(404, 'Câu hỏi không tồn tại hoặc đã được nộp.');
            }

            if ($cauDuocCap) {
                $cauHoi = DB::table('cauhoi as ch')
                    ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
                    ->where('ch.id', $cauDuocCap->cauhoi_id)
                    ->first([
                        'ch.id',
                        'ch.noidung',
                        'ch.anh',
                        'ch.audio',
                        'ch.thoigian',
                        'loai.ten as tenloai',
                        'loai.ma as maloai',
                        'loai.kynang',
                    ]);

                abort_if(! $cauHoi, 404, 'Không tìm thấy câu hỏi.');

                $luaChon = DB::table('luachon')
                    ->where('cauhoi_id', $cauHoi->id)
                    ->orderBy('thutu')
                    ->orderBy('id')
                    ->get(['id', 'noidung']);

                [$cauHoiCon, $luaChonCon] = $this->layCauHoiCon(
                    (int) $cauHoi->id,
                    $cauHoi->maloai
                );

                $cauTruoc = $cauChuaTraLoi
                    ->filter(fn ($cau) => (int) $cau->thutu < (int) $cauDuocCap->thutu
                    )
                    ->last();

                $cauSau = $cauChuaTraLoi->first(
                    fn ($cau) => (int) $cau->thutu > (int) $cauDuocCap->thutu
                );
            }
        }

        $cauTheoThuTu = $dsCauDaCap->keyBy('thutu');
        $phieuTraLoi = collect();

        for ($thuTu = 1; $thuTu <= $tongSoCau; $thuTu++) {
            $cau = $cauTheoThuTu->get($thuTu);

            $trangThai = match (true) {
                $cau === null => 'empty',
                (bool) $cau->datraloi => 'answered',
                $cauDuocCap !== null
                    && (int) $cau->id === (int) $cauDuocCap->id => 'current',
                default => 'seen',
            };

            $phieuTraLoi->push([
                'thutu' => $thuTu,
                'status' => $trangThai,
                'lanthicauhoi_id' => $cau && ! (bool) $cau->datraloi
                    ? (int) $cau->id
                    : null,
            ]);
        }

        $soCauHopLeConLai = $lanThi->trangthai === 'in_progress'
            ? DB::table('dethicauhoi as dc')
                ->join('cauhoi as ch', 'ch.id', '=', 'dc.cauhoi_id')
                ->where('dc.dethi_id', $lanThi->dethi_id)
                ->where('ch.trangthai', 'published')
                ->whereNotExists(function ($query) use ($id) {
                    $query->selectRaw('1')
                        ->from('lanthicauhoi as lc')
                        ->whereColumn('lc.cauhoi_id', 'dc.cauhoi_id')
                        ->where('lc.lanthi_id', $id);
                })
                ->count()
            : 0;

        return view('mock-test.question', compact(
            'lanThi',
            'deThi',
            'cauDuocCap',
            'cauHoi',
            'luaChon',
            'cauHoiCon',
            'luaChonCon',
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

    public function skip(Request $request, int $id, TestService $service)
    {
        $data = $this->kiemTraDapAn($request);
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = $service->kiemTraHetHan($id, $nguoiDungId);

        if ($lanThi->trangthai !== 'in_progress') {
            return redirect()->route('mock-test.show', ['id' => $id]);
        }

        $cauHienTai = Lanthicauhoi::query()
            ->whereKey((int) $data['lanthicauhoi_id'])
            ->where('lanthi_id', $id)
            ->where('datraloi', false)
            ->firstOrFail();

        if ($this->coDapAn($data)) {
            $traLoi = $this->luuDapAn(
                $service,
                $id,
                $nguoiDungId,
                $data
            );

            if ($traLoi === null) {
                return redirect()->route('mock-test.show', ['id' => $id]);
            }
        }

        $cauTiepTheo = $service->capCauHoiTiepTheo(
            $id,
            $nguoiDungId,
            true,
            (int) $cauHienTai->id
        );

        if (! $cauTiepTheo) {
            return redirect()
                ->route('mock-test.show', ['id' => $id])
                ->with('success', 'Không còn câu hỏi mới. Bạn có thể nộp bài.');
        }

        return redirect()->route('mock-test.show', [
            'id' => $id,
            'cau' => $cauTiepTheo->id,
        ]);
    }

    public function submit(Request $request, int $id, TestService $service)
    {
        $data = $this->kiemTraDapAn($request);

        $traLoi = $this->luuDapAn(
            $service,
            $id,
            (int) $request->user()->getAuthIdentifier(),
            $data
        );

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with(
                'success',
                $traLoi
                    ? 'Đã lưu câu trả lời.'
                    : 'Bài thi đã hết thời gian.'
            );
    }

    public function finish(Request $request, int $id, TestService $service)
    {
        $data = $request->validate([
            'lanthicauhoi_id' => ['nullable', 'integer'],
            'luachon_id' => ['nullable', 'integer'],
            'noidung' => ['nullable', 'string', 'max:10000'],
            'audio' => ['nullable', 'file', 'mimes:mp3,wav,m4a,ogg,webm', 'max:10240'],
            'xac_nhan_bo_trong' => ['nullable', 'in:1'],
        ]);

        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = DB::transaction(function () use (
            $data,
            $id,
            $nguoiDungId,
            $service
        ) {
            $lanThi = Lanthi::query()
                ->whereKey($id)
                ->where('nguoidung_id', $nguoiDungId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lanThi->trangthai === 'completed') {
                return $lanThi;
            }

            if ($this->coDapAn($data)) {
                if (empty($data['lanthicauhoi_id'])) {
                    throw ValidationException::withMessages([
                        'lanthicauhoi_id' => 'Không xác định được câu hỏi cần nộp.',
                    ]);
                }

                $this->luuDapAn($service, $id, $nguoiDungId, $data);
            }

            return $service->chotBaiThi(
                $id,
                $nguoiDungId,
                ($data['xac_nhan_bo_trong'] ?? null) === '1'
            );
        }, 3);

        return redirect()->route('mock-test.result', [
            'id' => $lanThi->id,
        ]);
    }

    public function abandon(
        Request $request,
        int $id,
        TestService $service
    ) {
        $lanThi = $service->boDoBaiThi(
            $id,
            (int) $request->user()->getAuthIdentifier()
        );

        $thongBao = $lanThi->trangthai === 'completed'
            ? 'Bài thi đã hết thời gian quy định.'
            : 'Bạn đã bỏ dở bài thi. Kết quả không được tính điểm.';

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with('success', $thongBao);
    }

    private function kiemTraDapAn(Request $request): array
    {
        return $request->validate([
            'lanthicauhoi_id' => ['required', 'integer'],
            'luachon_id' => ['nullable', 'integer'],
            'noidung' => ['nullable', 'string', 'max:10000'],
            'audio' => ['nullable', 'file', 'mimes:mp3,wav,m4a,ogg,webm', 'max:10240'],
        ]);
    }

    private function coDapAn(array $data): bool
    {
        return isset($data['luachon_id'])
            || trim($data['noidung'] ?? '') !== ''
            || isset($data['audio']);
    }

    private function luuDapAn(
        TestService $service,
        int $lanThiId,
        int $nguoiDungId,
        array $data
    ): ?Traloi {
        return $service->nopCauTraLoi(
            $lanThiId,
            $nguoiDungId,
            (int) $data['lanthicauhoi_id'],
            isset($data['luachon_id']) ? (int) $data['luachon_id'] : null,
            $data['noidung'] ?? null,
            $data['audio'] ?? null
        );
    }

    private function layCauHoiCon(int $cauHoiId, string $maLoai): array
    {
        $cauHoiCon = collect();
        $luaChonCon = collect();

        if (! in_array($maLoai, [
            'interactive_reading',
            'interactive_listening',
        ], true)) {
            return [$cauHoiCon, $luaChonCon];
        }

        $laDoc = $maLoai === 'interactive_reading';
        $quanHe = $laDoc ? 'luachonDoc' : 'luachonNghe';
        $query = $laDoc ? CauhoiDoc::query() : CauhoiNghe::query();

        $cacCauCon = $query
            ->where('cauhoi_id', $cauHoiId)
            ->with($quanHe)
            ->orderBy('thutu')
            ->orderBy('id')
            ->get();

        foreach ($cacCauCon as $con) {
            $cauHoiCon->push((object) [
                'id' => (int) $con->id,
                'noidung' => $con->noidung,
            ]);

            $luaChonCon->put(
                $con->id,
                $con->getRelation($quanHe)->map(fn ($lua) => (object) [
                    'id' => (int) $lua->id,
                    'noidung' => $lua->noidung,
                ])
            );
        }

        return [$cauHoiCon, $luaChonCon];
    }
}
