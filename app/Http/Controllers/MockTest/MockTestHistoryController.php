<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestHistoryController extends Controller
{
    public function index(Request $request, ScoringService $scoringService)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThis = DB::table('lanthi as lt')
            ->leftJoin('dethi as dt', 'dt.id', '=', 'lt.dethi_id')
            ->where('lt.nguoidung_id', $nguoiDungId)
            ->orderByRaw('COALESCE(lt.ketthuc, lt.batdau) DESC')
            ->orderByDesc('lt.id')
            ->select([
                'lt.id',
                'lt.trangthai',
                'lt.batdau',
                'lt.ketthuc',
                'lt.tongdiem',
                'dt.ten as ten_de_thi',
            ])
            ->paginate(10)
            ->withQueryString();

        $lanThis->getCollection()->transform(function ($lanThi) use (
            $scoringService,
            $nguoiDungId
        ) {
            $lanThi->ket_qua_tho = null;
            $lanThi->co_luot_truoc = false;
            $lanThi->chenh_lech_ty_le = null;

            if ($lanThi->trangthai === 'completed') {
                $lanThi->ket_qua_tho = $scoringService->tongHopKetQua(
                    (int) $lanThi->id
                );

                $luotTruoc = DB::table('lanthi')
                    ->where('nguoidung_id', $nguoiDungId)
                    ->where('trangthai', 'completed')
                    ->where(function ($query) use ($lanThi) {
                        $query->where('ketthuc', '<', $lanThi->ketthuc)
                            ->orWhere(function ($query) use ($lanThi) {
                                $query->where('ketthuc', $lanThi->ketthuc)
                                    ->where('id', '<', $lanThi->id);
                            });
                    })
                    ->orderByDesc('ketthuc')
                    ->orderByDesc('id')
                    ->first(['id']);

                if ($luotTruoc) {
                    $lanThi->co_luot_truoc = true;
                    $ketQuaTruoc = $scoringService->tongHopKetQua(
                        (int) $luotTruoc->id
                    );
                    $tyLeHienTai = $lanThi->ket_qua_tho['ty_le_phan_tram'];
                    $tyLeTruoc = $ketQuaTruoc['ty_le_phan_tram'];

                    if ($tyLeHienTai !== null && $tyLeTruoc !== null) {
                        $lanThi->chenh_lech_ty_le = round(
                            $tyLeHienTai - $tyLeTruoc,
                            1
                        );
                    }
                }
            }

            return $lanThi;
        });

        return view('mock-test.history', compact('lanThis'));
    }
}
