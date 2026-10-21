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
    public function show(Request $request, int $id)
    {
        $lanThi = Lanthi::query()->findOrFail($id);

        abort_unless(
            (int) $lanThi->nguoidung_id ===
            (int) $request->user()->getAuthIdentifier(),
            403
        );

        $deThi = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->first();

        return view('mock-test.question', compact(
            'lanThi',
            'deThi'
        ));
    }
}
