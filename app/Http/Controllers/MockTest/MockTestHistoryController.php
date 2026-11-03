<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestHistoryController extends Controller
{
    public function index(Request $request)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $luotTruoc = DB::table('lanthi as truoc')
            ->select('truoc.id')
            ->whereColumn('truoc.nguoidung_id', 'lt.nguoidung_id')
            ->where('truoc.trangthai', 'completed')
            ->where(function ($query) {
                $query
                    ->whereColumn('truoc.ketthuc', '<', 'lt.ketthuc')
                    ->orWhere(function ($query) {
                        $query
                            ->whereColumn('truoc.ketthuc', '=', 'lt.ketthuc')
                            ->whereColumn('truoc.id', '<', 'lt.id');
                    });
            })
            ->orderByDesc('truoc.ketthuc')
            ->orderByDesc('truoc.id')
            ->limit(1);

        $lichSu = DB::table('lanthi as lt')
            ->leftJoin('dethi as dt', 'dt.id', '=', 'lt.dethi_id')
            ->where('lt.nguoidung_id', $nguoiDungId)
            ->select([
                'lt.id',
                'lt.dethi_id',
                'lt.trangthai',
                'lt.batdau',
                'lt.ketthuc',
                'lt.tongdiem',
                'lt.literacy',
                'lt.comprehension',
                'lt.conversation',
                'lt.production',
                'dt.ten as ten_de_thi',
            ])
            ->selectSub($luotTruoc, 'luot_truoc_id');

        $lanThis = DB::query()
            ->fromSub($lichSu, 'ls')
            ->leftJoin('lanthi as truoc', 'truoc.id', '=', 'ls.luot_truoc_id')
            ->select([
                'ls.*',
                'truoc.tongdiem as diem_luot_truoc',
            ])
            ->orderByRaw('COALESCE(ls.ketthuc, ls.batdau) DESC')
            ->orderByDesc('ls.id')
            ->paginate(10)
            ->withQueryString();

        return view('mock-test.history', compact('lanThis'));
    }
}