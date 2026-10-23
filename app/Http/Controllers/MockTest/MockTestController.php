<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\Lanthi;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestController extends Controller
{
    // Danh sach cac de thi da xuat ban
    public function index(Request $request)
    {
        $deThi = DB::table('dethi')
            ->where('trangthai', 'published')
            ->orderBy('id')
            ->get();

        $lanThiDangMo = null;

        if ($request->user()) {
            $lanThiDangMo = Lanthi::query()
                ->where(
                    'nguoidung_id',
                    $request->user()->getAuthIdentifier()
                )
                ->where('trangthai', 'in_progress')
                ->first();
        }

        return view('mock-test.start', compact(
            'deThi',
            'lanThiDangMo'
        ));
    }

    // Bat dau hoac tiep tuc lan thi
    public function start(Request $request, TestService $service)
    {
        $validated = $request->validate([
            'dethi_id' => [
                'required',
                'integer',
                'exists:dethi,id',
            ],
        ]);

        $lanThi = $service->batDauThi(
            (int) $request->user()->getAuthIdentifier(),
            (int) $validated['dethi_id']
        );

        return redirect()->route('mock-test.show', [
            'id' => $lanThi->id,
        ]);
    }

    // Chi chu so huu moi duoc xem lan thi

    // MT02: Hien thi cau hoi cua lan thi
    public function show(Request $request, int $id)
    {
        $lanThi = Lanthi::findOrFail($id);

        // Chi chu so huu moi duoc xem lan thi
        abort_unless(
            (int) $lanThi->nguoidung_id ===
            (int) $request->user()->getAuthIdentifier(),
            403
        );

        $deThi = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->first();

        // Tim cau hoi dang cho tra loi
        $cauDuocCap = DB::table('lanthicauhoi')
            ->where('lanthi_id', $lanThi->id)
            ->where('datraloi', 0)
            ->orderBy('thutu')
            ->first();

        $cauHoi = null;
        $luaChon = collect();

        if ($cauDuocCap) {
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

            // Khong truy van cot ladung
            $luaChon = DB::table('luachon')
                ->where('cauhoi_id', $cauHoi->id)
                ->orderBy('thutu')
                ->orderBy('id')
                ->get(['id', 'noidung']);
        }

        $soCauDaCap = DB::table('lanthicauhoi')
            ->where('lanthi_id', $lanThi->id)
            ->count();

        $soCauTrongDe = DB::table('dethicauhoi')
            ->where('dethi_id', $lanThi->dethi_id)
            ->count();

        // Dem so cau hop le con lai
        $soCauHopLeConLai = DB::table('dethicauhoi as dc')
            ->join('cauhoi as ch', 'ch.id', '=', 'dc.cauhoi_id')
            ->where('dc.dethi_id', $lanThi->dethi_id)
            ->where('ch.trangthai', 'published')
            ->whereNotIn('ch.id', function ($query) use ($lanThi) {
                $query->select('cauhoi_id')
                    ->from('lanthicauhoi')
                    ->where('lanthi_id', $lanThi->id);
            })
            ->count();

        return view('mock-test.question', compact(
            'lanThi',
            'deThi',
            'cauDuocCap',
            'cauHoi',
            'luaChon',
            'soCauDaCap',
            'soCauTrongDe',
            'soCauHopLeConLai'
        ));
    }

    // MT02: Cap cau hoi tiep theo
    public function capCauHoi(
        Request $request,
        int $id,
        TestService $service
    ) {
        $service->capCauHoiTiepTheo(
            $id,
            (int) $request->user()->getAuthIdentifier()
        );

        return redirect()->route('mock-test.show', [
            'id' => $id,
        ]);
    }

    // MT08 - Xu ly yeu cau bo do bai thi
    public function abandon(
        Request $request,
        int $id,
        TestService $service
    ) {
        $service->boDoBaiThi(
            $id,
            (int) $request->user()->getAuthIdentifier()
        );

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with(
                'success',
                'Ban da bo do bai thi. Ket qua se khong duoc tinh diem.'
            );
    }

}
