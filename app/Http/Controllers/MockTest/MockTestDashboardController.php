<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestDashboardController extends Controller
{
    public function index(Request $request)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $thongKe = DB::table('luyentap')
            ->where('nguoidung_id', $nguoiDungId)
            ->selectRaw('COUNT(*) AS tong_cau')
            ->selectRaw('COUNT(ladung) AS da_cham')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN ladung = 1 THEN 1 ELSE 0 END), 0) AS dung'
            )
            ->selectRaw('COALESCE(SUM(thoigianlam), 0) AS thoi_gian')
            ->first();

        $tongCauLuyen = (int) $thongKe->tong_cau;
        $tongCauDaCham = (int) $thongKe->da_cham;
        $tongCauDung = (int) $thongKe->dung;
        $tongThoiGian = (int) $thongKe->thoi_gian;

        $tyLeDung = $tongCauDaCham > 0
            ? round($tongCauDung / $tongCauDaCham * 100, 1)
            : null;

        $tienDoTheoDang = DB::table('luyentap as lp')
            ->join('cauhoi as ch', 'ch.id', '=', 'lp.cauhoi_id')
            ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
            ->where('lp.nguoidung_id', $nguoiDungId)
            ->groupBy('loai.id', 'loai.ma', 'loai.ten')
            ->orderBy('loai.ten')
            ->select([
                'loai.id',
                'loai.ma',
                'loai.ten',
            ])
            ->selectRaw('COUNT(lp.id) AS tongso')
            ->selectRaw('COUNT(lp.ladung) AS dacham')
            ->selectRaw(
                'SUM(CASE WHEN lp.ladung = 1 THEN 1 ELSE 0 END) AS sodung'
            )
            ->get()
            ->map(function ($dangBai) {
                $dangBai->ty_le_dung = (int) $dangBai->dacham > 0
                    ? round(
                        (int) $dangBai->sodung / (int) $dangBai->dacham * 100,
                        1
                    )
                    : null;

                return $dangBai;
            });

        $goiYLuyenTap = $tienDoTheoDang
            ->filter(fn ($dangBai) =>
                $dangBai->ty_le_dung !== null
                && $dangBai->ty_le_dung < 70
            )
            ->sortBy('ty_le_dung')
            ->take(3)
            ->values();

        $chartDots = $this->layDiemBieuDo($nguoiDungId);

        $chartPolyline = implode(' ', array_map(
            fn ($dot) => $dot['x'] . ',' . $dot['y'],
            $chartDots
        ));

        [$chuoiHienTai, $chuoiDaiNhat] = $this->layChuoiNgayHoc(
            $nguoiDungId
        );

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

    private function layDiemBieuDo(int $nguoiDungId): array
    {
        $lanThis = DB::table('lanthi')
            ->where('nguoidung_id', $nguoiDungId)
            ->where('trangthai', 'completed')
            ->whereNotNull('ketthuc')
            ->whereBetween('tongdiem', [10, 160])
            ->orderByDesc('ketthuc')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'ketthuc', 'tongdiem'])
            ->reverse()
            ->values();

        $chartDots = [];
        $soDiem = $lanThis->count();

        foreach ($lanThis as $index => $lanThi) {
            $diem = (int) $lanThi->tongdiem;

            $chartDots[] = [
                'id' => (int) $lanThi->id,
                'ngay' => Carbon::parse($lanThi->ketthuc)->format('d/m H:i'),
                'diem' => $diem,
                'x' => round(
                    $soDiem === 1
                        ? 300
                        : 50 + $index * 500 / ($soDiem - 1),
                    1
                ),
                'y' => round(190 - ($diem - 10) / 150 * 140, 1),
            ];
        }

        return $chartDots;
    }

    private function layChuoiNgayHoc(int $nguoiDungId): array
    {
        $ngayLuyenTap = DB::table('luyentap')
            ->where('nguoidung_id', $nguoiDungId)
            ->whereNotNull('ngaylam')
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
        $chuoiTam = 0;
        $ngayTruoc = null;

        foreach ($ngayHoatDong as $ngay) {
            $lienTiep = $ngayTruoc !== null
                && Carbon::parse($ngay)->subDay()->toDateString() === $ngayTruoc;

            $chuoiTam = $lienTiep ? $chuoiTam + 1 : 1;
            $chuoiDaiNhat = max($chuoiDaiNhat, $chuoiTam);
            $ngayTruoc = $ngay;
        }

        $chuoiHienTai = 0;
        $ngayGanNhat = $ngayHoatDong->last();

        if (in_array($ngayGanNhat, [
            now()->toDateString(),
            now()->subDay()->toDateString(),
        ], true)) {
            $tapNgay = array_fill_keys($ngayHoatDong->all(), true);
            $ngayDangDem = Carbon::parse($ngayGanNhat);

            while (isset($tapNgay[$ngayDangDem->toDateString()])) {
                $chuoiHienTai++;
                $ngayDangDem->subDay();
            }
        }

        return [$chuoiHienTai, $chuoiDaiNhat];
    }
}