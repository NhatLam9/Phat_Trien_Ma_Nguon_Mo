<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Chungchi;
use App\Models\Lanthi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CertificateController extends Controller
{
    public function issue(Request $request, int $id)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $chungChi = DB::transaction(function () use ($id, $nguoiDungId) {
            $lanThi = Lanthi::query()
                ->whereKey($id)
                ->where('nguoidung_id', $nguoiDungId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lanThi->trangthai !== 'completed') {
                throw ValidationException::withMessages([
                    'certificate' => 'Chỉ có thể cấp chứng chỉ cho lượt thi đã hoàn tất.',
                ]);
            }

            // Cấp lặp lại cùng một lượt thi thì trả lại chứng chỉ cũ.
            $chungChiDaCap = Chungchi::query()
                ->where('lanthi_id', $lanThi->id)
                ->first();

            if ($chungChiDaCap) {
                return $chungChiDaCap;
            }

            $conChoCham = DB::table('lanthicauhoi as lc')
                ->leftJoin('traloi as tl', 'tl.lanthicauhoi_id', '=', 'lc.id')
                ->where('lc.lanthi_id', $lanThi->id)
                ->where('lc.datraloi', true)
                ->where(function ($query) {
                    $query->whereNull('tl.diem')
                        ->orWhereIn('tl.trangthai_ai', ['pending', 'failed']);
                })
                ->exists();

            if ($conChoCham) {
                throw ValidationException::withMessages([
                    'certificate' => 'Chưa thể cấp chứng chỉ: còn câu trả lời chờ chấm hoặc chấm lỗi.',
                ]);
            }

            $cacCotDiem = [
                'tongdiem',
                'literacy',
                'comprehension',
                'conversation',
                'production',
            ];
            $diem = [];

            foreach ($cacCotDiem as $cot) {
                $giaTri = $lanThi->{$cot};

                // Không tự quy đổi điểm thô sang thang DET 10–160.
                if (! is_numeric($giaTri)
                    || (float) $giaTri !== (float) (int) $giaTri
                    || (int) $giaTri < 10
                    || (int) $giaTri > 160) {
                    throw ValidationException::withMessages([
                        'certificate' => 'Chưa thể cấp chứng chỉ: lượt thi cần có điểm tổng và đủ bốn điểm kỹ năng DET hợp lệ.',
                    ]);
                }

                $diem[$cot] = (int) $giaTri;
            }

            return Chungchi::create([
                'nguoidung_id' => $nguoiDungId,
                'lanthi_id' => $lanThi->id,
                'ma' => 'DET-'.now()->format('Ymd').'-'.Str::upper(Str::random(10)),
                ...$diem,
                // Quy tắc ánh xạ cấp độ chưa được dự án xác nhận.
                'trinhdo' => null,
                'ngaycap' => now(),
            ]);
        }, 3);

        return redirect()->route('certificate.show', ['id' => $chungChi->id]);
    }

    public function show(Request $request, int $id)
    {
        $nguoiDungId = (int) $request->user()->getAuthIdentifier();

        $chungChi = Chungchi::query()
            ->whereKey($id)
            ->where('nguoidung_id', $nguoiDungId)
            ->firstOrFail();

        return view('certificate.show', compact('chungChi'));
    }
}
