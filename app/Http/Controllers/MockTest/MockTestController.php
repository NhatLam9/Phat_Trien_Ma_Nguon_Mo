<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\Lanthi;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MockTestController extends Controller
{
    /**
     * MT01 - Danh sach de thi.
     * MT04 - Kiem tra phien dang mo da het gio hay chua.
     */
    public function index(Request $request, TestService $service)
    {
        $deThi = DB::table('dethi')
            ->where('trangthai', 'published')
            ->orderBy('id')
            ->get();

        $lanThiDangMo = null;

        if ($request->user()) {
            $nguoiDungId = (int) $request->user()
                ->getAuthIdentifier();

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

    /**
     * MT01 - Bat dau hoac tiep tuc bai thi.
     */
    public function start(Request $request, TestService $service)
    {
        $validated = $request->validate([
            'dethi_id' => [
                'required',
                'integer',
                'exists:dethi,id',
            ],
        ]);

        $nguoiDungId = (int) $request->user()
            ->getAuthIdentifier();

        $lanThi = $service->batDauThi(
            $nguoiDungId,
            (int) $validated['dethi_id']
        );

        return redirect()
            ->route('mock-test.show', ['id' => $lanThi->id])
            ->with('success', 'Da bat dau hoac tiep tuc bai thi.');
    }

    /**
     * MT02 - Hien thi cau hoi.
     * MT04 - Kiem tra timeout truoc khi hien thi.
     */
    public function show(Request $request, int $id, TestService $service) {
        $nguoiDungId = (int) $request->user()
            ->getAuthIdentifier();

        // Service khoa ban ghi, kiem tra quyen va timeout.
        $lanThi = $service->kiemTraHetHan($id, $nguoiDungId);

        $deThi = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->first();

        abort_if(!$deThi, 404, 'Khong tim thay de thi.');

        // MT04 - Thoi diem het han.
        $hanThi = $service->layHanThi($lanThi);

        // So giay con lai khi phien dang hoat dong.
        $soGiayConLai = $lanThi->trangthai === 'in_progress'
            ? max(0, (int) ceil(
                now()->diffInSeconds($hanThi, false)
            ))
            : 0;

        // Phan biet het gio voi bo do thu cong.
        $biHetGio = DB::table('vipham')
            ->where('lanthi_id', $lanThi->id)
            ->where('loai', 'timeout')
            ->exists();

        $cauDuocCap = null;
        $cauHoi = null;
        $luaChon = collect();

        // Khong hien thi cau hoi neu phien da dong.
        if ($lanThi->trangthai === 'in_progress') {
        $cauYeuCau = $request->query('cau');

        $query = DB::table('lanthicauhoi')
            ->where('lanthi_id', $lanThi->id)
            ->where('datraloi', false);

        if ($cauYeuCau !== null) {
            abort_unless(
                is_string($cauYeuCau) &&
                ctype_digit($cauYeuCau) &&
                (int) $cauYeuCau > 0,
                404
            );

            $query->where('id', (int) $cauYeuCau);
        }

        $cauDuocCap = $query->orderByDesc('thutu')->first();

        if ($cauYeuCau !== null && !$cauDuocCap) {
            abort(404, 'Cau hoi khong ton tai hoac da duoc nop.');
        }

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

                $luaChon = DB::table('luachon')
                    ->where('cauhoi_id', $cauHoi->id)
                    ->orderBy('thutu')
                    ->orderBy('id')
                    ->get(['id', 'noidung']);
            }
        }

        $soCauDaCap = DB::table('lanthicauhoi')
            ->where('lanthi_id', $lanThi->id)
            ->count();

        $soCauTrongDe = DB::table('dethicauhoi')
            ->where('dethi_id', $lanThi->dethi_id)
            ->count();

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
                ->whereNotIn(
                    'ch.id',
                    function ($query) use ($lanThi) {
                        $query->select('cauhoi_id')
                            ->from('lanthicauhoi')
                            ->where('lanthi_id', $lanThi->id);
                    }
                )
                ->count();
        }

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
            'cauChuaTraLoi'
        ));
    }

    /**
     * MT02 - Cap cau hoi tiep theo.
     * MT04 - TestService se chan neu het gio.
     */
    public function capCauHoi(Request $request, int $id, TestService $service) {
        $service->capCauHoiTiepTheo(
            $id,
            (int) $request->user()->getAuthIdentifier()
        );

        return redirect()->route('mock-test.show', [
            'id' => $id,
        ]);
    }

    /* MT02 - Bo qua cau hoi hien tai. */
    public function skip(Request $request, int $id, TestService $service) {
    $data = $request->validate([
        'lanthicauhoi_id' => ['required', 'integer'],
    ]);

    $cauTiepTheo = $service->capCauHoiTiepTheo(
        $id,
        (int) $request->user()->getAuthIdentifier(),
        true,
        (int) $data['lanthicauhoi_id']
    );

    if (!$cauTiepTheo) {
        return redirect()->route('mock-test.show', ['id' => $id]);
    }

    return redirect()->route('mock-test.show', [
        'id' => $id,
        'cau' => $cauTiepTheo->id,
    ]);
}

    /**
     * MT03 - Nop cau tra loi.
     * POST -> Redirect -> GET.
     */
    public function submit(Request $request, int $id, TestService $service) {
        $validated = $request->validate([
            'lanthicauhoi_id' => [
                'required',
                'integer',
            ],
            'luachon_id' => [
                'nullable',
                'integer',
            ],
            'noidung' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);

        $traLoi = $service->nopCauTraLoi(
            $id,
            (int) $request->user()->getAuthIdentifier(),
            (int) $validated['lanthicauhoi_id'],
            isset($validated['luachon_id'])
                ? (int) $validated['luachon_id']
                : null,
            $validated['noidung'] ?? null
        );

        if ($traLoi === null) {
            return redirect()
                ->route('mock-test.show', ['id' => $id])
                ->with(
                    'success',
                    'Bai thi da het thoi gian. '
                    . 'Cau tra loi khong duoc ghi nhan.'
                );
        }

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with(
                'success',
                'Da luu cau tra loi thanh cong.'
            );
    }

    public function finish(Request $request, int $id, TestService $service) {
        $request->validate(['xac_nhan_bo_trong' => ['nullable', 'in:1'],]);

        $lanThi = $service->chotBaiThi(
            $id,
            (int) $request->user()->getAuthIdentifier(),
            $request->input('xac_nhan_bo_trong') === '1'
        );

        return redirect()
            ->route('mock-test.show', ['id' => $lanThi->id]);
    }

    /**
     * MT08 - Bo do bai thi.
     * MT04 - Phan biet timeout voi bo do thu cong.
     */
    public function abandon(Request $request, int $id, TestService $service) {
        $lanThi = $service->boDoBaiThi($id, (int) $request->user()->getAuthIdentifier());

        $biHetGio = DB::table('vipham')
            ->where('lanthi_id', $lanThi->id)
            ->where('loai', 'timeout')
            ->exists();

        $thongBao = $biHetGio
            ? 'Bai thi da het thoi gian quy dinh.'
            : 'Ban da bo do bai thi. Ket qua se khong duoc tinh diem.';

        return redirect()
            ->route('mock-test.show', ['id' => $id])
            ->with('success', $thongBao);
    }
}
