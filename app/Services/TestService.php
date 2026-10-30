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


        /* MT04 - Lay thoi diem het han. */


        /* MT04 - Xu ly bai thi het gio. */


        /* MT04 - Kiem tra va dong lan thi het han. */


        /* MT08 - Bo do bai thi. */


        /* MT03 - Nop cau tra loi. */


        /* MT05 - Chot bai thi, cho phep bo trong cau tra loi. */

    }
