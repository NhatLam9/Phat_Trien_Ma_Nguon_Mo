<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\CauhoiDoc;
use App\Models\CauhoiNghe;
use App\Models\Lanthi;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MockTestResultController extends Controller
{
    private const TEN_THANH_PHAN = [
        'literacy' => 'Literacy',
        'comprehension' => 'Comprehension',
        'conversation' => 'Conversation',
        'production' => 'Production',
    ];

    private const NHAN_TRANG_THAI = [
        'correct' => 'Đúng',
        'partial' => 'Đúng một phần',
        'incorrect' => 'Sai',
        'skipped' => 'Bỏ trống',
        'pending' => 'Chờ chấm',
        'graded' => 'Đã chấm',
    ];

    public function show(Request $request, int $id)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = Lanthi::query()
            ->whereKey($id)
            ->where('nguoidung_id', $nguoiDungId)
            ->firstOrFail();

        if ($lanThi->trangthai !== 'completed') {
            return redirect()->route('mock-test.show', ['id' => $id]);
        }

        $deThi = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->first();

        abort_if(! $deThi, 404, 'Không tìm thấy đề thi.');

        $dsCauHoi = $this->layCauHoi($id);

        foreach ($dsCauHoi as $cau) {
            $cau->trangthai = $this->xacDinhTrangThai($cau);
            $cau->nhan_trangthai = self::NHAN_TRANG_THAI[$cau->trangthai];
            $cau->chi_tiet_interactive = null;
            $cau->dap_an_interactive_hop_le = false;
        }

        $this->boSungLoiGiaiInteractive($dsCauHoi);

        $soCauTrongDe = (int) DB::table('dethicauhoi')
            ->where('dethi_id', $lanThi->dethi_id)
            ->count();

        $tongSoCau = max(
            $soCauTrongDe,
            $dsCauHoi->count(),
            (int) ($dsCauHoi->max('thutu') ?? 0)
        );

        $soCauChoCham = $dsCauHoi
            ->where('trangthai', 'pending')
            ->count();

        $ketQua = [
            'da_cham_xong' => $soCauChoCham === 0,
            'so_cau_dung' => $dsCauHoi
                ->where('trangthai', 'correct')
                ->count(),
            'so_cau_dung_mot_phan' => $dsCauHoi
                ->where('trangthai', 'partial')
                ->count(),
            'so_cau_sai' => $dsCauHoi
                ->where('trangthai', 'incorrect')
                ->count(),
            'so_cau_da_cham_khac' => $dsCauHoi
                ->where('trangthai', 'graded')
                ->count(),
            'so_cau_bo_trong' => max(
                0,
                $tongSoCau - $dsCauHoi->where('datraloi', 1)->count()
            ),
            'so_cau_cho_cham' => $soCauChoCham,
        ];

        $phanTichKyNang = collect(self::TEN_THANH_PHAN)
            ->map(function ($ten, $cot) use ($lanThi) {
                $diem = $lanThi->getAttribute($cot);

                return [
                    'ten' => $ten,
                    'diem' => $diem !== null ? (int) $diem : null,
                ];
            })
            ->values();

        $chungChi = $lanThi->chungChi()->first();

        $coTheCapChungChi = $ketQua['da_cham_xong']
            && $this->coDuDiemHopLe($lanThi);

        $phieuTraLoi = $this->taoPhieuTraLoi($dsCauHoi, $tongSoCau);

        $cauSo = $request->integer('cau', 1);

        abort_if(
            $cauSo < 1 || ($tongSoCau > 0 && $cauSo > $tongSoCau),
            404
        );

        $xemTatCa = $request->boolean('all');

        $dsHienThi = $xemTatCa
            ? $dsCauHoi
            : $dsCauHoi->where('thutu', $cauSo)->values();

        $cauHoiIds = $dsCauHoi
            ->pluck('cauhoi_id')
            ->unique()
            ->all();

        $luaChonTheoCau = DB::table('luachon')
            ->whereIn('cauhoi_id', $cauHoiIds)
            ->orderBy('thutu')
            ->orderBy('id')
            ->get(['id', 'cauhoi_id', 'noidung', 'ladung'])
            ->groupBy('cauhoi_id');

        $dapAnChuanTheoCau = DB::table('dap_an')
            ->whereIn('cauhoi_id', $cauHoiIds)
            ->where('ladung', true)
            ->orderBy('thutu')
            ->get(['cauhoi_id', 'noidung'])
            ->groupBy('cauhoi_id');

        $soGiayLamBai = ($lanThi->batdau && $lanThi->ketthuc)
            ? max(
                0,
                (int) $lanThi->batdau->diffInSeconds($lanThi->ketthuc)
            )
            : 0;

        return view('mock-test.result', compact(
            'lanThi',
            'deThi',
            'ketQua',
            'phieuTraLoi',
            'phanTichKyNang',
            'coTheCapChungChi',
            'chungChi',
            'dsHienThi',
            'luaChonTheoCau',
            'dapAnChuanTheoCau',
            'tongSoCau',
            'cauSo',
            'xemTatCa',
            'soGiayLamBai'
        ));
    }

    private function layCauHoi(int $lanThiId): Collection
    {
        return DB::table('lanthicauhoi as lc')
            ->join('cauhoi as ch', 'ch.id', '=', 'lc.cauhoi_id')
            ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
            ->leftJoin('traloi as tl', 'tl.lanthicauhoi_id', '=', 'lc.id')
            ->where('lc.lanthi_id', $lanThiId)
            ->orderBy('lc.thutu')
            ->get([
                'lc.id as lanthicauhoi_id',
                'lc.cauhoi_id',
                'lc.thutu',
                'lc.datraloi',
                'lc.diemtoida',
                'ch.noidung as cauhoi_noidung',
                'ch.giaithich',
                'ch.anh',
                'ch.audio as cauhoi_audio',
                'loai.ten as tenloai',
                'loai.ma as maloai',
                'loai.cachcham',
                'tl.id as traloi_id',
                'tl.luachon_id',
                'tl.noidung as traloi_noidung',
                'tl.audio as traloi_audio',
                'tl.ladung',
                'tl.diem',
                'tl.trangthai_ai',
                'tl.nhanxet_ai',
            ]);
    }

    private function xacDinhTrangThai(object $cau): string
    {
        $choCham = $cau->diem === null
            || in_array($cau->trangthai_ai, ['pending', 'failed'], true);

        return match (true) {
            ! (bool) $cau->datraloi => 'skipped',
            $choCham => 'pending',
            (float) $cau->diem > 0
                && (float) $cau->diem < (float) $cau->diemtoida => 'partial',
            $cau->ladung === null => 'graded',
            (bool) $cau->ladung => 'correct',
            default => 'incorrect',
        };
    }

    private function coDuDiemHopLe(Lanthi $lanThi): bool
    {
        $cacCot = [
            'tongdiem',
            ...array_keys(self::TEN_THANH_PHAN),
        ];

        foreach ($cacCot as $cot) {
            $diem = $lanThi->getAttribute($cot);

            if (
                $diem === null
                || ! is_numeric($diem)
                || (float) $diem !== (float) (int) $diem
                || (int) $diem < 10
                || (int) $diem > 160
                || (int) $diem % 5 !== 0
            ) {
                return false;
            }
        }

        return true;
    }

    private function taoPhieuTraLoi(
        Collection $dsCauHoi,
        int $tongSoCau
    ): Collection {
        $cauTheoThuTu = $dsCauHoi->keyBy('thutu');
        $phieuTraLoi = collect();

        for ($thuTu = 1; $thuTu <= $tongSoCau; $thuTu++) {
            $cau = $cauTheoThuTu->get($thuTu);

            $phieuTraLoi->push([
                'thutu' => $thuTu,
                'trangthai' => $cau?->trangthai ?? 'unissued',
                'nhan_trangthai' => $cau?->nhan_trangthai ?? 'Chưa được cấp',
                'co_cau_hoi' => $cau !== null,
            ]);
        }

        return $phieuTraLoi;
    }

    private function boSungLoiGiaiInteractive(Collection $dsCauHoi): void
    {
        $cacLoai = [
            'interactive_reading' => [CauhoiDoc::class, 'luachonDoc'],
            'interactive_listening' => [CauhoiNghe::class, 'luachonNghe'],
        ];

        foreach ($cacLoai as $maLoai => [$model, $quanHe]) {
            $cacCauCha = $dsCauHoi->where('maloai', $maLoai);

            if ($cacCauCha->isEmpty()) {
                continue;
            }

            $cacCauCon = $model::query()
                ->whereIn(
                    'cauhoi_id',
                    $cacCauCha->pluck('cauhoi_id')->unique()->all()
                )
                ->with($quanHe)
                ->orderBy('thutu')
                ->orderBy('id')
                ->get()
                ->groupBy('cauhoi_id');

            foreach ($cacCauCha as $cauCha) {
                $payload = json_decode(
                    $cauCha->traloi_noidung ?? '',
                    true
                );

                $hopLe = is_array($payload)
                    && ($payload['version'] ?? null) === 1
                    && is_array($payload['answers'] ?? null);

                $answers = $hopLe ? $payload['answers'] : [];

                $cauCha->dap_an_interactive_hop_le = $hopLe;
                $cauCha->chi_tiet_interactive = collect();

                foreach (
                    $cacCauCon->get($cauCha->cauhoi_id, collect()) as $con
                ) {
                    $luaChon = $con->getRelation($quanHe);
                    $giaTri = $answers[$con->id] ?? '';

                    $giaTri = is_scalar($giaTri)
                        ? trim((string) $giaTri)
                        : '';

                    if ($luaChon->isNotEmpty()) {
                        $daChon = $luaChon->first(
                            fn ($lua) => (string) $lua->id === $giaTri
                        );

                        $dapAnChuan = $luaChon
                            ->filter(fn ($lua) => (bool) $lua->ladung)
                            ->pluck('noidung')
                            ->implode(' / ');

                        $traLoi = $daChon?->noidung
                            ?? ($giaTri === ''
                                ? 'Bỏ trống'
                                : 'Lựa chọn không còn tồn tại');
                    } else {
                        $dapAnChuan = trim((string) $con->dapandung);
                        $traLoi = $giaTri !== '' ? $giaTri : 'Bỏ trống';
                    }

                    $cauCha->chi_tiet_interactive->push([
                        'noidung' => $con->noidung,
                        'traloi' => $traLoi,
                        'dapandung' => $dapAnChuan,
                    ]);
                }
            }
        }
    }
}
