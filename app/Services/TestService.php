<?php

namespace App\Services;

use App\Models\Lanthi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TestService
{
    public function batDauThi(int $nguoiDungId, int $deThiId): Lanthi
    {
        return DB::transaction(function () use ($nguoiDungId, $deThiId) {

            // Khoa nguoi dung de tranh tao lan thi dong thoi
            $nguoiDung = DB::table('nguoidung')
                ->where('id', $nguoiDungId)
                ->lockForUpdate()
                ->first();

            if (!$nguoiDung || $nguoiDung->trangthai !== 'active') {
                throw ValidationException::withMessages([
                    'nguoidung' => 'Tai khoan khong ton tai hoac da bi khoa.',
                ]);
            }

            // Kiem tra de thi
            $deThi = DB::table('dethi')
                ->where('id', $deThiId)
                ->where('trangthai', 'published')
                ->first();

            if (!$deThi) {
                throw ValidationException::withMessages([
                    'dethi_id' => 'De thi khong ton tai hoac chua duoc xuat ban.',
                ]);
            }

            // Tim lan thi chua hoan thanh
            $lanThiDangMo = Lanthi::query()
                ->where('nguoidung_id', $nguoiDungId)
                ->where('trangthai', 'in_progress')
                ->first();

            if ($lanThiDangMo) {

                // Cung de thi: tiep tuc phien cu
                if ((int) $lanThiDangMo->dethi_id === $deThiId) {
                    return $lanThiDangMo;
                }

                // Khac de thi: yeu cau xu ly phien cu
                throw ValidationException::withMessages([
                    'dethi_id' =>
                        'Ban dang co mot bai thi chua hoan thanh. '
                        . 'Hay tiep tuc hoac ket thuc bai thi do.',
                ]);
            }

            // Tao lan thi moi
            return Lanthi::create([
                'nguoidung_id' => $nguoiDungId,
                'dethi_id' => $deThiId,
                'dokho_hientai' => 5.00,
                'batdau' => now(),
                'trangthai' => 'in_progress',
            ]);

        }, 3);
    }
}
