<?php

namespace App\Http\Controllers\MockTest;

use App\Http\Controllers\Controller;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MockTestController extends Controller
{
    // MT01: Danh sách đề thi.
    public function index(Request $request, TestService $service)
    {
        $deThi = DB::table('dethi')
            ->where('trangthai', 'published')
            ->orderBy('id')
            ->get();

        $lanThiDangMo = null;

        if ($request->user()) {
            $nguoiDungId = (int) $request->user()->getAuthIdentifier();

            $lanThiDangMo = Lanthi::query()
                ->where('nguoidung_id', $nguoiDungId)
                ->where('trangthai', 'in_progress')
                ->first();

        }

        return view('mock-test.start', compact(
            'deThi',
            'lanThiDangMo'
        ));
    }

    // MT01: Bắt đầu hoặc tiếp tục bài thi.
    public function start(Request $request, TestService $service)
    {
        $validated = $request->validate([
            'dethi_id' => ['required', 'integer', 'exists:dethi,id'],
        ]);

        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $lanThi = $service->batDauThi(
            $nguoiDungId,
            (int) $validated['dethi_id']
        );

        return redirect()
            ->route('mock-test.show', ['id' => $lanThi->id])
            ->with('success', 'Đã bắt đầu hoặc tiếp tục bài thi.');
    }

    // MT02 / MT04: Hiển thị câu hỏi và kiểm tra thời gian.
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

    // MT02: Cấp câu hỏi đầu tiên hoặc tiếp theo.


    // MT02 / MT03: Lưu đáp án và chuyển sang câu sau.


    // MT03: Nộp câu trả lời độc lập (giữ tương thích).


    // MT05: Nộp bài, đồng thời lưu đáp án câu cuối.


    // MT08: Bỏ dở bài thi.

}
