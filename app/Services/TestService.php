<?php

    namespace App\Services;

    use App\Models\Lanthi;
    use App\Models\Lanthicauhoi;
    use App\Models\Traloi;
    use App\Models\Vipham;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Validation\ValidationException;

    class TestService
    {
        /* MT01 - Bat dau hoac tiep tuc bai thi. */
        public function batDauThi(int $nguoiDungId, int $deThiId): Lanthi {
            return DB::transaction(function () use ($nguoiDungId, $deThiId) {

                // 1. Khoa nguoi dung de tranh tao nhieu lan thi dong thoi.
                $nguoiDung = DB::table('nguoidung')
                    ->where('id', $nguoiDungId)
                    ->lockForUpdate()
                    ->first();
                if (!$nguoiDung || $nguoiDung->trangthai !== 'active') {
                    throw ValidationException::withMessages(['nguoidung' => 'Tai khoan khong ton tai hoac da bi khoa.']);
                }

                // 2. Kiem tra de thi da duoc xuat ban.
                $deThi = DB::table('dethi')
                    ->where('id', $deThiId)
                    ->where('trangthai', 'published')
                    ->first();
                if (!$deThi) {
                    throw ValidationException::withMessages(['dethi_id' => 'De thi khong ton tai hoac chua duoc xuat ban.']);
                }
                if ((int) $deThi->thoigian <= 0) {
                    throw ValidationException::withMessages([
                        'dethi_id' =>
                            'De thi chua co thoi gian hop le.',
                    ]);
                }

                // 3. Tim lan thi dang dien ra.
                $lanThiDangMo = Lanthi::query()
                    ->where('nguoidung_id', $nguoiDungId)
                    ->where('trangthai', 'in_progress')
                    ->lockForUpdate()
                    ->first();


                // 4. Neu van con phien dang thi.
                if ($lanThiDangMo) {
                    // Cung de thi: tiep tuc phien cu.
                    if ((int) $lanThiDangMo->dethi_id === $deThiId) {
                        return $lanThiDangMo;
                    }
                    throw ValidationException::withMessages([
                        'dethi_id' =>
                            'Ban dang co mot bai thi chua hoan thanh. '
                            . 'Hay tiep tuc hoac bo do bai thi do.',
                    ]);
                }

                // 5. Tao lan thi moi.
                return Lanthi::create([
                    'nguoidung_id' => $nguoiDungId,
                    'dethi_id' => $deThiId,
                    'dokho_hientai' => 5.00,
                    'batdau' => now(),
                    'trangthai' => 'in_progress',
                ]);
            }, 3);
        }

        /* MT02 - Cap cau hoi tiep theo. */
        public function capCauHoiTiepTheo(
            int $lanThiId, int $nguoiDungId,
            bool $boQua = false, ?int $cauHienTaiId = null
        ): ?Lanthicauhoi {
            return DB::transaction(function () use ($lanThiId, $nguoiDungId, $boQua, $cauHienTaiId) {

                // 1. Khoa lan thi tranh cap cau trung.
                $lanThi = Lanthi::query()
                    ->whereKey($lanThiId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Kiem tra quyen so huu.
                abort_unless(
                    (int) $lanThi->nguoidung_id ===
                    $nguoiDungId,
                    403,
                    'Ban khong co quyen truy cap lan thi nay.'
                );


                // 4. Chan cap cau hoi cho phien da dong.
                if ($lanThi->trangthai !== 'in_progress') {
                    throw ValidationException::withMessages([
                        'lanthi' => 'Lan thi khong con hoat dong.',
                    ]);
                }
                // Kiem tra cau hoi duoc yeu cau bo qua.
                $cauHienTai = null;

                if ($boQua) {
                    $cauHienTai = Lanthicauhoi::query()
                        ->whereKey($cauHienTaiId)
                        ->where('lanthi_id', $lanThiId)
                        ->first();

                    if (!$cauHienTai) {
                        throw ValidationException::withMessages([
                            'cauhoi' => 'Cau hoi khong hop le hoac da duoc nop.',
                        ]);
                    }
                }
                // Ưu tiên câu chưa trả lời đã được cấp ở phía sau.
                if ($boQua && $cauHienTai) {
                    $cauDaCapTiepTheo = Lanthicauhoi::query()
                        ->where('lanthi_id', $lanThiId)
                        ->where('datraloi', false)
                        ->where('thutu', '>', $cauHienTai->thutu)
                        ->orderBy('thutu')
                        ->first();

                    if ($cauDaCapTiepTheo) {
                        return $cauDaCapTiepTheo;
                    }
                }
                // 5. Lay cau chua tra loi neu khong yeu cau bo qua.
                if (!$boQua) {
                    $cauDangLam = Lanthicauhoi::query()
                        ->where('lanthi_id', $lanThiId)
                        ->where('datraloi', false)
                        ->orderByDesc('thutu')
                        ->first();

                    if ($cauDangLam) {
                        return $cauDangLam;
                    }
                }

                // 6. Lay thong tin de thi.
                $deThi = DB::table('dethi')
                    ->where('id', $lanThi->dethi_id)
                    ->first();
                if (!$deThi) {
                    throw ValidationException::withMessages([
                        'dethi' => 'Khong tim thay de thi.',
                    ]);
                }

                // 7. Lay nhung cau hoi thuoc de, da published va chua tung duoc cap.
                $ungVien = DB::table('dethicauhoi as dc')
                    ->join(
                        'cauhoi as ch',
                        'ch.id',
                        '=',
                        'dc.cauhoi_id'
                    )
                    ->where(
                        'dc.dethi_id',
                        $lanThi->dethi_id
                    )
                    ->where('ch.trangthai', 'published')
                    ->whereNotIn(
                        'ch.id',
                        function ($query) use ($lanThiId) {
                            $query->select('cauhoi_id')
                                ->from('lanthicauhoi')
                                ->where('lanthi_id', $lanThiId);
                        }
                    )
                    ->select(
                        'dc.cauhoi_id',
                        'dc.thutu',
                        'dc.diem',
                        'ch.dokho'
                    );

                // 8. Chon cau hoi theo kieu de.
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

                // Không còn câu phía sau thì dừng, không quay lại câu đầu.
                if (!$cauHoi) {
                    return null;
                }

                // 10. Xac dinh thu tu cau trong lan thi.
                $thuTu = (
                    (int) Lanthicauhoi::query()
                        ->where('lanthi_id', $lanThiId)
                        ->max('thutu')
                ) + 1;

                // 11. Luu cau hoi duoc cap.
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

        /* MT04 - Lay thoi diem het han. */


        /* MT04 - Xu ly bai thi het gio. */


        /* MT04 - Kiem tra va dong lan thi het han. */


        /* MT08 - Bo do bai thi. */


        /* MT03 - Nop cau tra loi. */


        /* MT05 - Chot bai thi, cho phep bo trong cau tra loi. */

    }
