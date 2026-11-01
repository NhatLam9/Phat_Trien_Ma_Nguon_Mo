<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\Lanthi;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestResultController extends Controller
{
    public function show(Request $request, int $id, ScoringService $scoringService)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        // Chi chu nhan phien thi duoc xem dap an va ket qua.
        $lanThi = Lanthi::query()
            ->whereKey($id)
            ->where('nguoidung_id', $nguoiDungId)
            ->firstOrFail();

        if ($lanThi->trangthai !== 'completed') {
            return redirect()->route('mock-test.show', ['id' => $id]);
        }

        $deThi = DB::table('dethi')->where('id', $lanThi->dethi_id)->first();
        abort_if(!$deThi, 404, 'Khong tim thay de thi.');

        // Su dung ham tong hop MT05 da bo sung vao ScoringService.
        $ketQua = $scoringService->tongHopKetQua($id);

        $dsCauHoi = DB::table('lanthicauhoi as lc')
            ->join('cauhoi as ch', 'ch.id', '=', 'lc.cauhoi_id')
            ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
            ->leftJoin('traloi as tl', 'tl.lanthicauhoi_id', '=', 'lc.id')
            ->where('lc.lanthi_id', $id)
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
                'loai.kynang',
                'loai.cachcham',
                'tl.luachon_id',
                'tl.noidung as traloi_noidung',
                'tl.audio as traloi_audio',
                'tl.ladung',
                'tl.diem',
                'tl.nhanxet_ai',
            ]);

        foreach ($dsCauHoi as $cau) {
            $cau->trangthai = match (true) {
                (int) $cau->datraloi === 0 => 'skipped',
                $cau->diem === null => 'pending',
                $cau->ladung === null => 'graded',
                (int) $cau->ladung === 1 => 'correct',
                default => 'incorrect',
            };
        }

        $tongSoCau = (int) $ketQua['tong_so_cau'];
        $phieuTraLoi = collect();

        // Danh sach nay bao gom ca cau chua duoc cap khi bai het gio som.
        for ($i = 1; $i <= $tongSoCau; $i++) {
            $cau = $dsCauHoi->firstWhere('thutu', $i);
            $phieuTraLoi->push([
                'thutu' => $i,
                'trangthai' => $cau?->trangthai ?? 'unissued',
                'co_cau_hoi' => $cau !== null,
            ]);
        }

        $tenKyNang = [
            'reading' => 'Reading',
            'listening' => 'Listening',
            'writing' => 'Writing',
            'speaking' => 'Speaking',
        ];

        $phanTichKyNang = $dsCauHoi->groupBy('kynang')
            ->map(function ($ds, $ma) use ($tenKyNang) {
                $diemToiDa = (float) $ds->sum('diemtoida');
                $soChoCham = $ds->where('trangthai', 'pending')->count();
                $phanTram = ($soChoCham === 0 && $diemToiDa > 0)
                    ? round((float) $ds->sum('diem') / $diemToiDa * 100, 1)
                    : null;

                return [
                    'ten' => $tenKyNang[$ma] ?? ucfirst((string) $ma),
                    'so_cau' => $ds->count(),
                    'so_dung' => $ds->where('trangthai', 'correct')->count(),
                    'so_cho_cham' => $soChoCham,
                    'phan_tram' => $phanTram,
                ];
            })
            ->values();

        $cauSo = $request->integer('cau', 1);
        abort_if($cauSo < 1 || ($tongSoCau > 0 && $cauSo > $tongSoCau), 404);

        $xemTatCa = $request->boolean('all');
        $dsHienThi = $xemTatCa
            ? $dsCauHoi
            : $dsCauHoi->where('thutu', $cauSo)->values();

        $cauHoiIds = $dsCauHoi->pluck('cauhoi_id')->unique()->all();

        // Nap lua chon va dap an chuan trong 2 truy van de tranh N+1.
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
            ? max(0, (int) $lanThi->batdau->diffInSeconds($lanThi->ketthuc))
            : 0;

        return view('mock-test.result', compact(
            'lanThi',
            'deThi',
            'ketQua',
            'dsCauHoi',
            'phieuTraLoi',
            'phanTichKyNang',
            'dsHienThi',
            'luaChonTheoCau',
            'dapAnChuanTheoCau',
            'tongSoCau',
            'cauSo',
            'xemTatCa',
            'soGiayLamBai'
        ));
    }
}
