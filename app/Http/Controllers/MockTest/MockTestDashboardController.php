<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Services\ScoringService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestDashboardController extends Controller
{
    public function index(Request $request, ScoringService $scoringService)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $luyenTap = DB::table('luyentap')
            ->where('nguoidung_id', $nguoiDungId);

        $tongCauLuyen = (clone $luyenTap)->count();
        $tongCauDaCham = (clone $luyenTap)->whereNotNull('ladung')->count();
        $tongCauDung = (clone $luyenTap)->where('ladung', 1)->count();
        $tongThoiGian = (int) (clone $luyenTap)->sum('thoigianlam');
        $tyLeDung = $tongCauDaCham > 0
            ? round($tongCauDung / $tongCauDaCham * 100, 1)
            : null;

        $tienDoTheoDang = DB::table('luyentap as lp')
            ->join('cauhoi as ch', 'ch.id', '=', 'lp.cauhoi_id')
            ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
            ->where('lp.nguoidung_id', $nguoiDungId)
            ->groupBy('loai.id', 'loai.ma', 'loai.ten')
            ->orderBy('loai.ten')
            ->get([
                'loai.id',
                'loai.ma',
                'loai.ten',
                DB::raw('COUNT(lp.id) AS tongso'),
                DB::raw('COUNT(lp.ladung) AS dacham'),
                DB::raw('SUM(CASE WHEN lp.ladung = 1 THEN 1 ELSE 0 END) AS sodung'),
                DB::raw('AVG(lp.diem) AS diemtrungbinh'),
            ])
            ->map(function ($dangBai) {
                $dangBai->ty_le_dung = (int) $dangBai->dacham > 0
                    ? round((int) $dangBai->sodung / (int) $dangBai->dacham * 100, 1)
                    : null;

                return $dangBai;
            });

        $goiYLuyenTap = $tienDoTheoDang
            ->filter(fn ($dangBai) =>
                $dangBai->ty_le_dung !== null &&
                $dangBai->ty_le_dung < 70
            )
            ->sortBy('ty_le_dung')
            ->take(3)
            ->values();

        $lanThiHoanTat = DB::table('lanthi')
            ->where('nguoidung_id', $nguoiDungId)
            ->where('trangthai', 'completed')
            ->whereNotNull('ketthuc')
            ->orderBy('ketthuc')
            ->orderBy('id')
            ->get(['id', 'ketthuc']);

        $diemTheoLanThi = $lanThiHoanTat
            ->map(function ($lanThi) use ($scoringService) {
                $ketQua = $scoringService->tongHopKetQua((int) $lanThi->id);

                if ($ketQua['ty_le_phan_tram'] === null) {
                    return null;
                }

                return [
                    'ngay' => Carbon::parse($lanThi->ketthuc)->format('d/m H:i'),
                    'ty_le' => (float) $ketQua['ty_le_phan_tram'],
                    'id' => (int) $lanThi->id,
                ];
            })
            ->filter()
            ->slice(-10)
            ->values();

        $chartDots = [];
        $soDiem = $diemTheoLanThi->count();
        foreach ($diemTheoLanThi as $index => $diem) {
            $x = $soDiem === 1
                ? 300
                : 50 + ($index * 500 / ($soDiem - 1));
            $y = 190 - ($diem['ty_le'] * 1.4);

            $chartDots[] = [
                'x' => round($x, 1),
                'y' => round($y, 1),
                'ngay' => $diem['ngay'],
                'ty_le' => round($diem['ty_le'], 1),
                'id' => $diem['id'],
            ];
        }

        $chartPolyline = implode(' ', array_map(
            fn ($diem) => $diem['x'] . ',' . $diem['y'],
            $chartDots
        ));

        $ngayLuyenTap = DB::table('luyentap')
            ->where('nguoidung_id', $nguoiDungId)
            ->selectRaw('DATE(ngaylam) AS ngayhoatdong')
            ->distinct()
            ->pluck('ngayhoatdong');

        $ngayThi = DB::table('lanthi')
            ->where('nguoidung_id', $nguoiDungId)
            ->where('trangthai', 'completed')
            ->whereNotNull('ketthuc')
            ->selectRaw('DATE(ketthuc) AS ngayhoatdong')
            ->distinct()
            ->pluck('ngayhoatdong');

        $ngayHoatDong = $ngayLuyenTap
            ->merge($ngayThi)
            ->filter()
            ->map(fn ($ngay) => Carbon::parse($ngay)->toDateString())
            ->unique()
            ->sort()
            ->values();

        $chuoiDaiNhat = 0;
        $chuoiHienTai = 0;
        $chuoiTam = 0;
        $ngayTruoc = null;

        foreach ($ngayHoatDong as $ngay) {
            $ngayCarbon = Carbon::parse($ngay);
            $chuoiTam = $ngayTruoc !== null &&
                $ngayCarbon->copy()->subDay()->toDateString() === $ngayTruoc
                ? $chuoiTam + 1
                : 1;
            $chuoiDaiNhat = max($chuoiDaiNhat, $chuoiTam);
            $ngayTruoc = $ngay;
        }

        $ngayGanNhat = $ngayHoatDong->last();
        $homNay = now()->toDateString();
        $homQua = now()->subDay()->toDateString();

        if (in_array($ngayGanNhat, [$homNay, $homQua], true)) {
            $ngayDangDem = Carbon::parse($ngayGanNhat);

            while ($ngayHoatDong->contains($ngayDangDem->toDateString())) {
                $chuoiHienTai++;
                $ngayDangDem->subDay();
            }
        }

        return view('mock-test.dashboard', compact(
            'tongCauLuyen',
            'tongCauDaCham',
            'tongCauDung',
            'tongThoiGian',
            'tyLeDung',
            'tienDoTheoDang',
            'goiYLuyenTap',
            'chartDots',
            'chartPolyline',
            'chuoiHienTai',
            'chuoiDaiNhat'
        ));
    }
}
