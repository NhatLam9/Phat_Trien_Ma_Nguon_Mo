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
                // MT04: Dong phien cu neu da het gio.
                if ($lanThiDangMo) {
                    if ($this->xuLyHetGio($lanThiDangMo)) {
                        $lanThiDangMo = null;
                    }
                }

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

                // 3. MT04: Xu ly neu da het gio.
                if ($this->xuLyHetGio($lanThi)) {
                    return null;
                }

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
                        ->where('datraloi', false)
                        ->first();

                    if (!$cauHienTai) {
                        throw ValidationException::withMessages([
                            'cauhoi' => 'Cau hoi khong hop le hoac da duoc nop.',
                        ]);
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

                // 9. Het cau moi: quay lai cau bo trong.
                if (!$cauHoi) {
                    if (!$boQua) {
                        return null;
                    }

                    $cauTiepTheo = Lanthicauhoi::query()
                        ->where('lanthi_id', $lanThiId)
                        ->where('datraloi', false)
                        ->where('thutu', '>', $cauHienTai->thutu)
                        ->orderBy('thutu')
                        ->first();

                    return $cauTiepTheo ?: Lanthicauhoi::query()
                        ->where('lanthi_id', $lanThiId)
                        ->where('datraloi', false)
                        ->orderBy('thutu')
                        ->first();
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
        public function layHanThi(Lanthi $lanThi): Carbon
    {
            $deThi = DB::table('dethi')
                ->where('id', $lanThi->dethi_id)
                ->first();
            if (
                !$deThi ||
                (int) $deThi->thoigian <= 0
            ) {
                throw ValidationException::withMessages([
                    'dethi' =>
                        'De thi chua co thoi gian hop le.',
                ]);
            }
            return Carbon::parse(
                $lanThi->batdau,
                config('app.timezone')
            )->addSeconds((int) $deThi->thoigian);
        }

        /* MT04 - Xu ly bai thi het gio. */
        private function xuLyHetGio(Lanthi $lanThi): bool
        {
            // Chi xu ly phien dang thi.
            if ($lanThi->trangthai !== 'in_progress') {
                return false;
            }
            $hanThi = $this->layHanThi($lanThi);
            // Chua toi han.
            if (now()->lt($hanThi)) {
                return false;
            }
            // Da het gio.
            // Ghi dung moc het han, khong phai thoi diem nguoi dung truy cap lai.
            // MT04: Tu dong nop bai khi het gio.
            $lanThi->trangthai = 'completed';
            $lanThi->ketthuc = $hanThi;
            $lanThi->save();
            // Ghi nhat ky timeout.
            Vipham::create([
                'lanthi_id' => $lanThi->id,
                'loai' => 'timeout',
                'nguon' => 'auto',
                'mota' =>
                    'Bai thi tu dong ket thuc do het thoi gian.',
                'thoigian' => now(),
            ]);
            return true;
        }

        /* MT04 - Kiem tra va dong lan thi het han. */
        public function kiemTraHetHan(
            int $lanThiId,
            int $nguoiDungId
        ): Lanthi {
            return DB::transaction(function () use (
                $lanThiId,
                $nguoiDungId
            ) {
                // Khoa ban ghi de tranh xu ly trung.
                $lanThi = Lanthi::query()
                    ->whereKey($lanThiId)
                    ->lockForUpdate()
                    ->firstOrFail();
                // Kiem tra quyen.
                abort_unless(
                    (int) $lanThi->nguoidung_id ===
                    $nguoiDungId,
                    403,
                    'Ban khong co quyen xem lan thi nay.'
                );
                // Xu ly neu qua han.
                $this->xuLyHetGio($lanThi);
                return $lanThi->refresh();
            }, 3);
        }

        /* MT08 - Bo do bai thi. */
        public function boDoBaiThi(
            int $lanThiId,
            int $nguoiDungId
        ): Lanthi {
            return DB::transaction(function () use (
                $lanThiId,
                $nguoiDungId
            ) {

                // 1. Khoa lan thi.
                $lanThi = Lanthi::query()
                    ->whereKey($lanThiId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Kiem tra quyen so huu.
                abort_unless(
                    (int) $lanThi->nguoidung_id ===
                    $nguoiDungId,
                    403,
                    'Ban khong co quyen bo do lan thi nay.'
                );

                // 3. MT04: Neu da het gio,
                // ghi nhan timeout thay vi bo do thu cong.
                if ($this->xuLyHetGio($lanThi)) {
                    return $lanThi;
                }

                // 4. Neu da bo do, khong cap nhat lai.
                if ($lanThi->trangthai === 'abandoned') {
                    return $lanThi;
                }

                // 5. Khong duoc bo do bai da completed.
                if ($lanThi->trangthai !== 'in_progress') {
                    throw ValidationException::withMessages([
                        'lanthi' =>
                            'Bai thi khong con o trang thai dang thi.',
                    ]);
                }

                // 6. Bo do thu cong.
                $lanThi->trangthai = 'abandoned';
                $lanThi->ketthuc = now();
                $lanThi->save();
                return $lanThi;
            }, 3);
        }

        /* MT03 - Nop cau tra loi. */
        public function nopCauTraLoi(
            int $lanThiId,
            int $nguoiDungId,
            int $lanThiCauHoiId,
            ?int $luaChonId,
            ?string $noiDung
        ): ?Traloi {
            return DB::transaction(function () use (
                $lanThiId,
                $nguoiDungId,
                $lanThiCauHoiId,
                $luaChonId,
                $noiDung
            ) {
                // 1. Khoa lan thi va kiem tra quyen.
                $lanThi = Lanthi::query()
                    ->whereKey($lanThiId)
                    ->lockForUpdate()
                    ->firstOrFail();
                abort_unless(
                    (int) $lanThi->nguoidung_id === $nguoiDungId,
                    403,
                    'Ban khong co quyen nop bai thi nay.'
                );

                // 2. MT04 - Kiem tra timeout.
                if ($this->xuLyHetGio($lanThi)) {
                    return null;
                }
                if ($lanThi->trangthai !== 'in_progress') {
                    throw ValidationException::withMessages([
                        'lanthi' => 'Lan thi khong con hoat dong.',
                    ]);
                }

                // 3. Khoa cau hoi duoc cap.
                $lanThiCauHoi = Lanthicauhoi::query()
                    ->whereKey($lanThiCauHoiId)
                    ->where('lanthi_id', $lanThiId)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 4. Chong nop trung.
                if (
                    $lanThiCauHoi->datraloi ||
                    Traloi::query()
                        ->where(
                            'lanthicauhoi_id',
                            $lanThiCauHoi->id
                        )
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'traloi' => 'Cau hoi nay da duoc nop truoc do.',
                    ]);
                }

                // 5. Phan biet trac nghiem va tu luan.
                $coLuaChon = DB::table('luachon')
                    ->where('cauhoi_id', $lanThiCauHoi->cauhoi_id)
                    ->exists();
                $noiDung = trim($noiDung ?? '');
                $laDung = null;
                $diem = null;
                if ($coLuaChon) {
                    // Cau trac nghiem bat buoc chon mot dap an.
                    if ($luaChonId === null || $noiDung !== '') {
                        throw ValidationException::withMessages([
                            'luachon_id' =>
                                'Vui long chon mot dap an hop le.',
                        ]);
                    }
                    // Khong tin luachon_id do client gui len.
                    // Chi lay lua chon thuoc dung cau hoi.
                    $luaChon = DB::table('luachon')
                        ->where('id', $luaChonId)
                        ->where(
                            'cauhoi_id',
                            $lanThiCauHoi->cauhoi_id
                        )
                        ->first();
                    if (!$luaChon) {
                        throw ValidationException::withMessages([
                            'luachon_id' =>
                                'Dap an khong thuoc cau hoi nay.',
                        ]);
                    }
                    $laDung = (bool) $luaChon->ladung;
                    $diem = $laDung
                        ? (float) $lanThiCauHoi->diemtoida
                        : 0.0;
                } else {
                    // Cau tu luan: luu noi dung, chua cham diem.
                    if ($luaChonId !== null || $noiDung === '') {
                        throw ValidationException::withMessages([
                            'noidung' =>
                                'Vui long nhap noi dung tra loi.',
                        ]);
                    }
                }

                // 6. Luu dap an.
                $traLoi = Traloi::create([
                    'lanthicauhoi_id' => $lanThiCauHoi->id,
                    'luachon_id' => $coLuaChon
                        ? $luaChonId
                        : null,
                    'noidung' => $coLuaChon
                        ? null
                        : $noiDung,
                    'ladung' => $laDung,
                    'diem' => $diem,
                    'thoigian' => now(),
                ]);

                // 7. Danh dau cau hoi da duoc tra loi.
                $lanThiCauHoi->datraloi = true;
                $lanThiCauHoi->thoigian_tra_loi = now();
                $lanThiCauHoi->save();

                // 8. Cap nhat do kho cho de Adaptive,
                // chi khi da cham duoc trac nghiem.
                $deThi = DB::table('dethi')
                    ->where('id', $lanThi->dethi_id)
                    ->first();
                if (
                    $deThi &&
                    $deThi->kieu === 'adaptive' &&
                    $laDung !== null
                ) {
                    $doKho = (float) $lanThi->dokho_hientai;
                    $doKho += $laDung ? 1.0 : -1.0;
                    $lanThi->dokho_hientai = max(
                        1.0,
                        min(10.0, $doKho)
                    );
                    $lanThi->save();
                }
                return $traLoi;
            }, 3);
        }

        /* MT05 - Chot bai thi, cho phep bo trong cau tra loi. */
        public function chotBaiThi(
            int $lanThiId,
            int $nguoiDungId,
            bool $xacNhanBoTrong = false
        ): Lanthi {
            return DB::transaction(function () use (
                $lanThiId,
                $nguoiDungId,
                $xacNhanBoTrong
            ) {
                // Khoa phien thi
                $lanThi = Lanthi::query()
                    ->whereKey($lanThiId)
                    ->lockForUpdate()
                    ->firstOrFail();
                abort_unless(
                    (int) $lanThi->nguoidung_id === $nguoiDungId,
                    403,
                    'Ban khong co quyen chot bai thi nay.'
                );
                if ($lanThi->trangthai === 'completed') {
                    return $lanThi;
                }
                // MT04 - Xu ly timeout
                if ($this->xuLyHetGio($lanThi)) {
                    return $lanThi;
                }
                if ($lanThi->trangthai !== 'in_progress') {
                    throw ValidationException::withMessages([
                        'lanthi' => 'Bai thi khong con hoat dong.',
                    ]);
                }
                // Lay cac cau da cap nhung chua tra loi
                $cauBoTrong = Lanthicauhoi::query()
                    ->where('lanthi_id', $lanThiId)
                    ->where('datraloi', false)
                    ->orderBy('thutu')
                    ->pluck('thutu')
                    ->all();
                // Bat buoc xac nhan neu con cau bo trong
                $soCauChuaCap = DB::table('dethicauhoi as dc')
                ->join('cauhoi as ch', 'ch.id', '=', 'dc.cauhoi_id')
                ->where('dc.dethi_id', $lanThi->dethi_id)
                ->where('ch.trangthai', 'published')
                ->whereNotIn('ch.id', function ($query) use ($lanThiId) {
                    $query->select('cauhoi_id')
                        ->from('lanthicauhoi')
                        ->where('lanthi_id', $lanThiId);
                })
                ->count();
                if ((!empty($cauBoTrong) || $soCauChuaCap > 0) && !$xacNhanBoTrong) {
                    throw ValidationException::withMessages([
                        'lanthi' => 'Ban can xac nhan cac cau bo trong.',
                    ]);
                }
                // Khong chot neu cau da nop van chua cham diem
                $coCauChuaCham = DB::table('lanthicauhoi as lc')
                    ->leftJoin(
                        'traloi as tl',
                        'tl.lanthicauhoi_id',
                        '=',
                        'lc.id'
                    )
                    ->where('lc.lanthi_id', $lanThiId)
                    ->where('lc.datraloi', true)
                    ->where(function ($query) {
                        $query->whereNull('tl.id')
                            ->orWhereNull('tl.diem');
                    })
                    ->exists();
                if ($coCauChuaCham) {
                    throw ValidationException::withMessages([
                        'lanthi' => 'Con cau tra loi chua duoc cham diem.',
                    ]);
                }
                // Hoan thanh phien thi
                $lanThi->trangthai = 'completed';
                $lanThi->ketthuc = now();
                $lanThi->save();
                return $lanThi->refresh();
            }, 3);
        }
    }
