<?php

namespace App\Services;

use App\Models\Lanthi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Lanthicauhoi;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    // MT02: Cap cau hoi tiep theo
    public function capCauHoiTiepTheo(
        int $lanThiId,
        int $nguoiDungId
    ): ?Lanthicauhoi {

        return DB::transaction(function () use (
            $lanThiId,
            $nguoiDungId
        ) {

            // 1. Khoa lan thi de tranh cap cau hoi trung
            $lanThi = Lanthi::query()
                ->whereKey($lanThiId)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Kiem tra quyen so huu
            abort_unless(
                (int) $lanThi->nguoidung_id === $nguoiDungId,
                403
            );

            // 3. Kiem tra trang thai lan thi
            if ($lanThi->trangthai !== 'in_progress') {
                throw ValidationException::withMessages([
                    'lanthi' => 'Lan thi khong con hoat dong.',
                ]);
            }

            // 4. Neu co cau chua tra loi, tra ve cau do
            $cauDangLam = Lanthicauhoi::query()
                ->where('lanthi_id', $lanThiId)
                ->where('datraloi', 0)
                ->orderBy('thutu')
                ->first();

            if ($cauDangLam) {
                return $cauDangLam;
            }

            // 5. Lay thong tin de thi
            $deThi = DB::table('dethi')
                ->where('id', $lanThi->dethi_id)
                ->first();

            if (!$deThi) {
                throw ValidationException::withMessages([
                    'dethi' => 'Khong tim thay de thi.',
                ]);
            }

            // 6. Loc cac cau hoi chua duoc cap
            $ungVien = DB::table('dethicauhoi as dc')
                ->join(
                    'cauhoi as ch',
                    'ch.id',
                    '=',
                    'dc.cauhoi_id'
                )
                ->where('dc.dethi_id', $lanThi->dethi_id)
                ->where('ch.trangthai', 'published')
                ->whereNotIn('ch.id', function ($query) use ($lanThiId) {
                    $query->select('cauhoi_id')
                        ->from('lanthicauhoi')
                        ->where('lanthi_id', $lanThiId);
                })
                ->select(
                    'dc.cauhoi_id',
                    'dc.thutu',
                    'dc.diem',
                    'ch.dokho'
                );

            // 7. Chon cau hoi theo kieu de
            if ($deThi->kieu === 'fixed') {

                $cauHoi = $ungVien
                    ->orderBy('dc.thutu')
                    ->first();

            } elseif ($deThi->kieu === 'adaptive') {

                $cauHoi = $ungVien
                    ->orderByRaw(
                        'ABS(ch.dokho - ?) ASC',
                        [(float) $lanThi->dokho_hientai]
                    )
                    ->orderBy('dc.thutu')
                    ->first();

            } else {

                throw ValidationException::withMessages([
                    'dethi' => 'Kieu de thi khong hop le.',
                ]);
            }

            // 8. Het cau hoi
            if (!$cauHoi) {
                return null;
            }

            // 9. Xac dinh thu tu cau hoi trong lan thi
            $thuTu = (int) Lanthicauhoi::query()
                ->where('lanthi_id', $lanThiId)
                ->max('thutu') + 1;

            // 10. Luu cau hoi vao lan thi
            return Lanthicauhoi::create([
                'lanthi_id' => $lanThiId,
                'cauhoi_id' => $cauHoi->cauhoi_id,
                'thutu' => $thuTu,
                'dokho_lucra' => $cauHoi->dokho,
                'diemtoida' => $cauHoi->diem,
                'thoigian_cap' => now(),
                'datraloi' => false,
            ]);

        }, 3);
    }

}
