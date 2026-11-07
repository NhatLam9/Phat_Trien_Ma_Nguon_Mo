<?php

namespace App\Services;

use App\Exceptions\AiLimitExceededException;
use App\Jobs\ProcessAIGradingJob;
use App\Models\CauhoiDoc;
use App\Models\CauhoiNghe;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Traloi;
use App\Models\Vipham;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class TestService
{
    private const NHOM_KY_NANG = [
        'literacy' => ['reading', 'writing'],
        'comprehension' => ['reading', 'listening'],
        'conversation' => ['listening', 'speaking'],
        'production' => ['writing', 'speaking'],
    ];

    private const LOAI_INTERACTIVE = [
        'interactive_reading',
        'interactive_listening',
    ];

    public function batDauThi(int $nguoiDungId, int $deThiId): Lanthi
    {
        return DB::transaction(function () use ($nguoiDungId, $deThiId) {
            $nguoiDung = DB::table('nguoidung')
                ->where('id', $nguoiDungId)
                ->lockForUpdate()
                ->first();

            if (! $nguoiDung || $nguoiDung->trangthai !== 'active') {
                $this->baoLoi(
                    'nguoidung',
                    'Tài khoản không tồn tại hoặc đã bị khóa.'
                );
            }

            $deThi = DB::table('dethi')
                ->where('id', $deThiId)
                ->where('trangthai', 'published')
                ->lockForUpdate()
                ->first();

            if (! $deThi || (int) $deThi->thoigian <= 0) {
                $this->baoLoi(
                    'dethi_id',
                    'Đề thi chưa được xuất bản hoặc thời gian không hợp lệ.'
                );
            }

            $lanThiDangMo = Lanthi::query()
                ->where('nguoidung_id', $nguoiDungId)
                ->where('trangthai', 'in_progress')
                ->lockForUpdate()
                ->first();

            if ($lanThiDangMo && $this->xuLyHetGio($lanThiDangMo)) {
                $lanThiDangMo = null;
            }

            if ($lanThiDangMo) {
                if ((int) $lanThiDangMo->dethi_id === $deThiId) {
                    return $lanThiDangMo;
                }

                $this->baoLoi(
                    'dethi_id',
                    'Bạn đang có bài thi chưa hoàn thành. '
                        .'Hãy tiếp tục hoặc bỏ dở bài thi đó.'
                );
            }

            $soCauAi = DB::table('dethicauhoi as dc')
                ->join('cauhoi as ch', 'ch.id', '=', 'dc.cauhoi_id')
                ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
                ->where('dc.dethi_id', $deThiId)
                ->where('ch.trangthai', 'published')
                ->where('loai.cachcham', 'ai')
                ->count();

            try {
                if ($soCauAi > 0) {
                    app(AIService::class)->checkAndDeductLimitBulk($nguoiDungId, $soCauAi);
                }
            } catch (AiLimitExceededException $e) {
                $this->baoLoi('ai_limit', $e->getMessage());
            }

            return Lanthi::create([
                'nguoidung_id' => $nguoiDungId,
                'dethi_id' => $deThiId,
                'dokho_hientai' => 5.00,
                'ai_ngay_tru_luot' => $soCauAi > 0 ? today() : null,
                'ai_luot_du_phong' => $soCauAi,
                'batdau' => now(),
                'trangthai' => 'in_progress',
            ]);
        }, 3);
    }

    public function capCauHoiTiepTheo(
        int $lanThiId,
        int $nguoiDungId,
        bool $boQua = false,
        ?int $cauHienTaiId = null
    ): ?Lanthicauhoi {
        return DB::transaction(function () use (
            $lanThiId,
            $nguoiDungId,
            $boQua,
            $cauHienTaiId
        ) {
            $lanThi = $this->khoaLanThi($lanThiId, $nguoiDungId);

            if ($this->xuLyHetGio($lanThi)) {
                return null;
            }

            $this->kiemTraDangThi($lanThi);

            if ($boQua) {
                $cauHienTai = Lanthicauhoi::query()
                    ->whereKey($cauHienTaiId)
                    ->where('lanthi_id', $lanThiId)
                    ->first();

                if (! $cauHienTai) {
                    $this->baoLoi(
                        'cauhoi',
                        'Câu hỏi không thuộc lần thi này.'
                    );
                }

                $cauTiepTheo = Lanthicauhoi::query()
                    ->where('lanthi_id', $lanThiId)
                    ->where('datraloi', false)
                    ->where('thutu', '>', $cauHienTai->thutu)
                    ->orderBy('thutu')
                    ->first();

                if ($cauTiepTheo) {
                    return $cauTiepTheo;
                }
            } else {
                $cauDangLam = Lanthicauhoi::query()
                    ->where('lanthi_id', $lanThiId)
                    ->where('datraloi', false)
                    ->orderByDesc('thutu')
                    ->first();

                if ($cauDangLam) {
                    return $cauDangLam;
                }
            }

            $deThi = DB::table('dethi')
                ->where('id', $lanThi->dethi_id)
                ->first();

            if (! $deThi) {
                $this->baoLoi('dethi', 'Không tìm thấy đề thi.');
            }

            $ungVien = $this->cauHoiChuaCap($lanThi)
                ->select([
                    'dc.cauhoi_id',
                    'dc.diem',
                    'ch.dokho',
                ]);

            if ($deThi->kieu === 'fixed') {
                $ungVien->orderBy('dc.thutu');
            } elseif ($deThi->kieu === 'adaptive') {
                $ungVien
                    ->orderByRaw(
                        'ABS(ch.dokho - ?) ASC',
                        [(float) $lanThi->dokho_hientai]
                    )
                    ->orderBy('dc.thutu');
            } else {
                $this->baoLoi('dethi', 'Kiểu đề thi không hợp lệ.');
            }

            $cauHoi = $ungVien->first();

            if (! $cauHoi) {
                return null;
            }

            $thuTu = (int) Lanthicauhoi::query()
                ->where('lanthi_id', $lanThiId)
                ->max('thutu') + 1;

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

    public function nopCauTraLoi(
        int $lanThiId,
        int $nguoiDungId,
        int $lanThiCauHoiId,
        ?int $luaChonId,
        ?string $noiDung,
        ?UploadedFile $audio = null
    ): ?Traloi {
        return DB::transaction(function () use (
            $lanThiId,
            $nguoiDungId,
            $lanThiCauHoiId,
            $luaChonId,
            $noiDung,
            $audio
        ) {
            $lanThi = $this->khoaLanThi($lanThiId, $nguoiDungId);

            if ($this->xuLyHetGio($lanThi)) {
                return null;
            }

            $this->kiemTraDangThi($lanThi);

            $cau = Lanthicauhoi::query()
                ->whereKey($lanThiCauHoiId)
                ->where('lanthi_id', $lanThiId)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $cau->datraloi
                || Traloi::query()
                    ->where('lanthicauhoi_id', $cau->id)
                    ->exists()
            ) {
                $this->baoLoi(
                    'traloi',
                    'Câu hỏi này đã được nộp trước đó.'
                );
            }

            $loai = DB::table('cauhoi as ch')
                ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
                ->where('ch.id', $cau->cauhoi_id)
                ->first(['loai.ma', 'loai.cachcham', 'loai.kynang']);

            if (! $loai) {
                $this->baoLoi('cauhoi', 'Không tìm thấy loại câu hỏi.');
            }

            $noiDung = trim($noiDung ?? '');
            $laDung = null;
            $diem = null;
            $trangThaiAi = null;

            $laInteractive = in_array(
                $loai->ma,
                self::LOAI_INTERACTIVE,
                true
            );

            $coLuaChon = ! $laInteractive
                && DB::table('luachon')
                    ->where('cauhoi_id', $cau->cauhoi_id)
                    ->exists();

            if ($laInteractive) {
                if ($luaChonId !== null) {
                    $this->baoLoi(
                        'traloi',
                        'Đáp án Interactive không hợp lệ.'
                    );
                }

                $ketQua = $this->chamInteractive(
                    (int) $cau->cauhoi_id,
                    $loai->ma,
                    $noiDung,
                    (float) $cau->diemtoida
                );

                $diem = $ketQua['diem'];
                $laDung = $ketQua['ladung'];
                $noiDung = $ketQua['noidung'];
            } elseif ($coLuaChon) {
                if ($luaChonId === null || $noiDung !== '') {
                    $this->baoLoi(
                        'luachon_id',
                        'Vui lòng chọn một đáp án hợp lệ.'
                    );
                }

                $luaChon = DB::table('luachon')
                    ->where('id', $luaChonId)
                    ->where('cauhoi_id', $cau->cauhoi_id)
                    ->first();

                if (! $luaChon) {
                    $this->baoLoi(
                        'luachon_id',
                        'Đáp án không thuộc câu hỏi này.'
                    );
                }

                $laDung = (bool) $luaChon->ladung;
                $diem = $laDung ? (float) $cau->diemtoida : 0.0;
            } else {
                if ($luaChonId !== null || ($noiDung === '' && ! $audio)) {
                    $this->baoLoi(
                        'noidung',
                        'Vui lòng nhập nội dung trả lời.'
                    );
                }

                if ($loai->cachcham === 'auto') {
                    $coDapAnChuan = DB::table('dap_an')
                        ->where('cauhoi_id', $cau->cauhoi_id)
                        ->where('ladung', 1)
                        ->exists();

                    if (! $coDapAnChuan) {
                        $this->baoLoi(
                            'cauhoi',
                            'Câu hỏi tự động chưa có đáp án chuẩn. '
                                .'Vui lòng báo người quản lý đề.'
                        );
                    }

                    $ketQua = app(ScoringService::class)->calculateResult(
                        ['id' => $cau->cauhoi_id],
                        ['noidung' => $noiDung],
                        (float) $cau->diemtoida
                    );

                    $laDung = $ketQua['is_correct'];
                    $diem = $ketQua['diem'];
                } elseif ($loai->cachcham === 'ai') {
                    if ($loai->kynang === 'speaking' && ! $audio) {
                        $this->baoLoi('audio', 'Vui lòng tải lên file ghi âm câu trả lời.');
                    }
                    if ($loai->kynang !== 'speaking' && mb_strlen($noiDung) > 3000) {
                        $this->baoLoi('noidung', 'Bài viết không được vượt quá 3000 ký tự.');
                    }
                    $trangThaiAi = 'pending';
                }
            }

            $ngayTruLuot = null;
            if ($trangThaiAi === 'pending') {
                if ((int) $lanThi->ai_luot_du_phong > 0) {
                    $ngayTruLuot = $lanThi->ai_ngay_tru_luot;
                    $lanThi->ai_luot_du_phong--;
                    $lanThi->save();
                } else {
                    // Phiên cũ tạo trước migration: chỉ trừ khi nộp câu AI.
                    try {
                        app(AIService::class)->checkAndDeductLimit($nguoiDungId);
                    } catch (AiLimitExceededException $e) {
                        $this->baoLoi('ai_limit', $e->getMessage());
                    }
                    $ngayTruLuot = today();
                }
            }
            if ($audio && ($trangThaiAi !== 'pending' || $loai->kynang !== 'speaking')) {
                $this->baoLoi('audio', 'Câu hỏi này không nhận file ghi âm.');
            }

            $audioPath = $audio?->store('audio', 'local');
            // Dọn file nếu transaction ngoài (finish/skip) bị rollback.
            if ($audioPath) {
                app()->terminating(function () use ($audioPath) {
                    if (! Traloi::query()->where('audio', $audioPath)->exists()) {
                        Storage::disk('local')->delete($audioPath);
                    }
                });
            }

            $traLoi = Traloi::create([
                'lanthicauhoi_id' => $cau->id,
                'luachon_id' => $coLuaChon ? $luaChonId : null,
                'noidung' => $coLuaChon ? null : $noiDung,
                'ladung' => $laDung,
                'diem' => $diem,
                'trangthai_ai' => $trangThaiAi,
                'audio' => $audioPath,
                'luot_cham' => 1,
                'ngay_tru_luot' => $ngayTruLuot,
                'thoidiem_cham' => $trangThaiAi ? now() : null,
                'thoigian' => now(),
            ]);

            $cau->datraloi = true;
            $cau->thoigian_tra_loi = now();
            $cau->save();

            $kieuDe = DB::table('dethi')
                ->where('id', $lanThi->dethi_id)
                ->value('kieu');

            if ($kieuDe === 'adaptive' && $laDung !== null) {
                $doKho = (float) $lanThi->dokho_hientai;
                $doKho += $laDung ? 1.0 : -1.0;

                $lanThi->setAttribute(
                    'dokho_hientai',
                    max(1.0, min(10.0, $doKho))
                );

                $lanThi->save();
            }

            if ($trangThaiAi === 'pending') {
                DB::afterCommit(function () use ($traLoi, $nguoiDungId, $ngayTruLuot) {
                    try {
                        ProcessAIGradingJob::dispatch($traLoi->id, $nguoiDungId, $ngayTruLuot, 1);
                    } catch (Throwable $e) {
                        report($e);
                        app(AIService::class)->markFailedAndRefund(
                            $traLoi->id, $nguoiDungId, $ngayTruLuot, 1,
                            'Không gửi được bài tới hàng đợi chấm. Vui lòng thử chấm lại.'
                        );
                    }
                });
            }

            return $traLoi;
        }, 3);
    }

    public function layHanThi(Lanthi $lanThi): Carbon
    {
        $thoiGian = DB::table('dethi')
            ->where('id', $lanThi->dethi_id)
            ->value('thoigian');

        if ((int) $thoiGian <= 0) {
            $this->baoLoi('dethi', 'Đề thi chưa có thời gian hợp lệ.');
        }

        return Carbon::parse(
            $lanThi->batdau,
            config('app.timezone')
        )->addSeconds((int) $thoiGian);
    }

    public function kiemTraHetHan(
        int $lanThiId,
        int $nguoiDungId
    ): Lanthi {
        return DB::transaction(function () use ($lanThiId, $nguoiDungId) {
            $lanThi = $this->khoaLanThi($lanThiId, $nguoiDungId);

            $this->xuLyHetGio($lanThi);

            return $lanThi->refresh();
        }, 3);
    }

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
            $lanThi = $this->khoaLanThi($lanThiId, $nguoiDungId);

            if ($lanThi->trangthai === 'completed') {
                return $lanThi;
            }

            if ($this->xuLyHetGio($lanThi)) {
                return $lanThi;
            }

            $this->kiemTraDangThi($lanThi);

            if (! $xacNhanBoTrong) {
                $coCauBoTrong = Lanthicauhoi::query()
                    ->where('lanthi_id', $lanThiId)
                    ->where('datraloi', false)
                    ->exists();

                if (
                    $coCauBoTrong
                    || $this->cauHoiChuaCap($lanThi)->exists()
                ) {
                    $this->baoLoi(
                        'lanthi',
                        'Bạn cần xác nhận các câu bỏ trống.'
                    );
                }
            }

            $this->ganDiemBaiThi($lanThi);

            $this->hoanLuotAiChuaDung($lanThi);
            $lanThi->trangthai = 'completed';
            $lanThi->setAttribute('ketthuc', now());
            $lanThi->save();
            $this->capChungNhanHoanThanh($lanThi);

            return $lanThi->refresh();
        }, 3);
    }

    public function tinhLaiDiem(
        int $lanThiId,
        int $nguoiDungId
    ): Lanthi {
        return DB::transaction(function () use ($lanThiId, $nguoiDungId) {
            $lanThi = $this->khoaLanThi($lanThiId, $nguoiDungId);

            if ($lanThi->trangthai !== 'completed') {
                $this->baoLoi(
                    'lanthi',
                    'Chỉ tính lại điểm cho bài thi đã hoàn thành.'
                );
            }

            $this->ganDiemBaiThi($lanThi);
            $lanThi->save();

            return $lanThi->refresh();
        }, 3);
    }

    public function boDoBaiThi(
        int $lanThiId,
        int $nguoiDungId
    ): Lanthi {
        return DB::transaction(function () use ($lanThiId, $nguoiDungId) {
            $lanThi = $this->khoaLanThi($lanThiId, $nguoiDungId);

            if ($this->xuLyHetGio($lanThi)) {
                return $lanThi;
            }

            if ($lanThi->trangthai === 'abandoned') {
                return $lanThi;
            }

            $this->kiemTraDangThi($lanThi);

            $this->hoanLuotAiChuaDung($lanThi);
            $lanThi->trangthai = 'abandoned';
            $lanThi->setAttribute('ketthuc', now());
            $lanThi->save();

            return $lanThi;
        }, 3);
    }

    private function khoaLanThi(
        int $lanThiId,
        int $nguoiDungId
    ): Lanthi {
        $lanThi = Lanthi::query()
            ->whereKey($lanThiId)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless(
            (int) $lanThi->nguoidung_id === $nguoiDungId,
            403,
            'Bạn không có quyền truy cập lần thi này.'
        );

        return $lanThi;
    }

    private function kiemTraDangThi(Lanthi $lanThi): void
    {
        if ($lanThi->trangthai !== 'in_progress') {
            $this->baoLoi(
                'lanthi',
                'Bài thi không còn ở trạng thái đang thi.'
            );
        }
    }

    private function cauHoiChuaCap(
        Lanthi $lanThi
    ): Builder {
        return DB::table('dethicauhoi as dc')
            ->join('cauhoi as ch', 'ch.id', '=', 'dc.cauhoi_id')
            ->where('dc.dethi_id', $lanThi->dethi_id)
            ->where('ch.trangthai', 'published')
            ->whereNotExists(function ($query) use ($lanThi) {
                $query->selectRaw('1')
                    ->from('lanthicauhoi as lc')
                    ->whereColumn('lc.cauhoi_id', 'dc.cauhoi_id')
                    ->where('lc.lanthi_id', $lanThi->id);
            });
    }

    private function xuLyHetGio(Lanthi $lanThi): bool
    {
        if ($lanThi->trangthai !== 'in_progress') {
            return false;
        }

        $hanThi = $this->layHanThi($lanThi);

        if (now()->lt($hanThi)) {
            return false;
        }

        $this->ganDiemBaiThi($lanThi);

        $this->hoanLuotAiChuaDung($lanThi);
        $lanThi->trangthai = 'completed';
        $lanThi->setAttribute('ketthuc', $hanThi);
        $lanThi->save();
        $this->capChungNhanHoanThanh($lanThi);

        Vipham::create([
            'lanthi_id' => $lanThi->id,
            'loai' => 'timeout',
            'nguon' => 'auto',
            'mota' => 'Bài thi tự động kết thúc do hết thời gian.',
            'thoigian' => now(),
        ]);

        return true;
    }

    private function capChungNhanHoanThanh(Lanthi $lanThi): void
    {
        $tenDe = DB::table('dethi')->where('id', $lanThi->dethi_id)
            ->whereNotNull('capdo')
            ->value('ten');

        if ($tenDe === null) {
            return;
        }

        $lanThi->chungChi()->firstOrCreate([], [
            'nguoidung_id' => $lanThi->nguoidung_id,
            'ma' => 'DET-'.now()->format('Ymd').'-'.Str::upper(Str::random(10)),
            'tieude' => 'Chứng nhận hoàn thành – '.$tenDe,
            'ngaycap' => now(),
        ]);
    }

    private function chamInteractive(
        int $cauHoiId,
        string $maLoai,
        string $noiDung,
        float $diemToiDa
    ): array {
        $payload = json_decode($noiDung, true);

        if (
            ! is_array($payload)
            || ($payload['version'] ?? null) !== 1
            || ! isset($payload['answers'])
            || ! is_array($payload['answers'])
        ) {
            $this->baoLoi(
                'noidung',
                'Vui lòng trả lời từng câu hỏi con.'
            );
        }

        $laDoc = $maLoai === 'interactive_reading';
        $quanHe = $laDoc ? 'luachonDoc' : 'luachonNghe';

        $query = $laDoc ? CauhoiDoc::query() : CauhoiNghe::query();

        $cacCauCon = $query
            ->where('cauhoi_id', $cauHoiId)
            ->with($quanHe)
            ->orderBy('thutu')
            ->orderBy('id')
            ->get();

        if ($cacCauCon->isEmpty()) {
            $this->baoLoi(
                'cauhoi',
                'Câu hỏi chưa có dữ liệu câu hỏi con.'
            );
        }

        $answers = $payload['answers'];
        $idHopLe = array_fill_keys(
            $cacCauCon->pluck('id')->map(
                fn ($id) => (string) $id
            )->all(),
            true
        );

        foreach ($answers as $id => $giaTri) {
            if (
                ! isset($idHopLe[(string) $id])
                || (! is_string($giaTri) && ! is_int($giaTri))
            ) {
                $this->baoLoi(
                    'noidung',
                    'Dữ liệu đáp án câu hỏi con không hợp lệ.'
                );
            }
        }

        $soDung = 0;
        $dapAnDaChuanHoa = [];

        foreach ($cacCauCon as $con) {
            $cacLuaChon = $con->getRelation($quanHe);
            $dapAn = trim((string) ($answers[$con->id] ?? ''));

            if (mb_strlen($dapAn, 'UTF-8') > 2000) {
                $this->baoLoi(
                    'noidung',
                    'Mỗi đáp án câu con không được vượt quá 2000 ký tự.'
                );
            }

            $dapAnDaChuanHoa[(string) $con->id] = $dapAn;

            if ($cacLuaChon->isNotEmpty()) {
                $coDapAnDung = $cacLuaChon->contains(
                    fn ($lua) => (bool) $lua->ladung
                );

                if (! $coDapAnDung) {
                    $this->baoLoi(
                        'cauhoi',
                        'Câu hỏi con chưa có đáp án chuẩn.'
                    );
                }

                if ($dapAn === '') {
                    continue;
                }

                if (! ctype_digit($dapAn)) {
                    $this->baoLoi(
                        'noidung',
                        'Lựa chọn câu hỏi con không hợp lệ.'
                    );
                }

                $luaChon = $cacLuaChon->first(
                    fn ($lua) => (string) $lua->id === $dapAn
                );

                if (! $luaChon) {
                    $this->baoLoi(
                        'noidung',
                        'Lựa chọn không thuộc câu hỏi con.'
                    );
                }

                $soDung += (bool) $luaChon->ladung ? 1 : 0;

                continue;
            }

            $dapAnChuan = trim((string) $con->dapandung);

            if ($dapAnChuan === '') {
                $this->baoLoi(
                    'cauhoi',
                    'Câu hỏi con chưa có đáp án tự động. '
                        .'Vui lòng báo người quản lý đề.'
                );
            }

            if (
                $dapAn !== ''
                && $this->chuanHoaDapAn($dapAn)
                    === $this->chuanHoaDapAn($dapAnChuan)
            ) {
                $soDung++;
            }
        }

        $tongSoCauCon = $cacCauCon->count();

        return [
            'diem' => round(
                $soDung / $tongSoCauCon * $diemToiDa,
                2
            ),
            'ladung' => $soDung === $tongSoCauCon,
            'noidung' => json_encode([
                'version' => 1,
                'answers' => (object) $dapAnDaChuanHoa,
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ];
    }

    private function chuanHoaDapAn(string $noiDung): string
    {
        $noiDung = preg_replace('/\s+/u', ' ', trim($noiDung));

        return mb_strtolower($noiDung ?? '', 'UTF-8');
    }

    private function ganDiemBaiThi(Lanthi $lanThi): void
    {
        $cauDaCap = DB::table('lanthicauhoi as lc')
            ->join('cauhoi as ch', 'ch.id', '=', 'lc.cauhoi_id')
            ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
            ->leftJoin('traloi as tl', 'tl.lanthicauhoi_id', '=', 'lc.id')
            ->where('lc.lanthi_id', $lanThi->id)
            ->get([
                'loai.kynang',
                'lc.diemtoida',
                'lc.datraloi',
                'tl.diem',
                'tl.trangthai_ai',
            ]);

        $cauChuaCap = $this->cauHoiChuaCap($lanThi)
            ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
            ->get([
                'loai.kynang',
                'dc.diem as diemtoida',
                DB::raw('0 as datraloi'),
                DB::raw('NULL as diem'),
                DB::raw('NULL as trangthai_ai'),
            ]);

        $tongHop = [];

        foreach (self::NHOM_KY_NANG as $cot => $kyNang) {
            $tongHop[$cot] = ['diem' => 0.0, 'toida' => 0.0];
        }

        foreach ($cauDaCap->concat($cauChuaCap) as $cau) {
            $daTraLoi = (bool) $cau->datraloi;
            $choCham = $daTraLoi && (
                $cau->diem === null
                || in_array(
                    $cau->trangthai_ai,
                    ['pending', 'failed'],
                    true
                )
            );

            if ($choCham) {
                continue;
            }

            $diemToiDa = max(0.0, (float) $cau->diemtoida);
            $diem = $daTraLoi
                ? max(0.0, min($diemToiDa, (float) $cau->diem))
                : 0.0;

            foreach (self::NHOM_KY_NANG as $cot => $kyNang) {
                if (in_array($cau->kynang, $kyNang, true)) {
                    $tongHop[$cot]['diem'] += $diem;
                    $tongHop[$cot]['toida'] += $diemToiDa;
                }
            }
        }

        $diemThanhPhan = [];

        foreach ($tongHop as $cot => $nhom) {
            $diem = $nhom['toida'] > 0
                ? $this->lamTronDiem(
                    $nhom['diem'] / $nhom['toida'] * 150 + 10
                )
                : null;

            $lanThi->setAttribute($cot, $diem);
            $diemThanhPhan[] = $diem;
        }

        $lanThi->setAttribute(
            'tongdiem',
            in_array(null, $diemThanhPhan, true)
                ? null
                : $this->lamTronDiem(array_sum($diemThanhPhan) / 4)
        );
    }

    private function lamTronDiem(float $diem): int
    {
        $diem = (int) (round(
            $diem / 5,
            0,
            PHP_ROUND_HALF_UP
        ) * 5);

        return max(10, min(160, $diem));
    }

    private function hoanLuotAiChuaDung(Lanthi $lanThi): void
    {
        $soLuot = (int) $lanThi->ai_luot_du_phong;
        $lanThi->ai_luot_du_phong = 0;
        $lanThi->save();
        for ($i = 0; $i < $soLuot; $i++) {
            app(AIService::class)->refundUsage((int) $lanThi->nguoidung_id, $lanThi->ai_ngay_tru_luot);
        }
    }

    public function ghiNhanKetQuaAi(int $traLoiId, float $diemDet, string $nhanXet, int $attempt): bool
    {
        if (! is_finite($diemDet) || $diemDet < 10 || $diemDet > 160) {
            $this->baoLoi('diem', 'Điểm AI phải nằm trong thang 10–160.');
        }

        return DB::transaction(function () use ($traLoiId, $diemDet, $nhanXet, $attempt) {
            $lanThiId = DB::table('traloi as tl')
                ->join('lanthicauhoi as lc', 'lc.id', '=', 'tl.lanthicauhoi_id')
                ->where('tl.id', $traLoiId)->value('lc.lanthi_id');
            $lanThi = Lanthi::query()->whereKey($lanThiId)->lockForUpdate()->firstOrFail();
            $traLoi = Traloi::query()->whereKey($traLoiId)->lockForUpdate()->firstOrFail();
            if ($traLoi->trangthai_ai !== 'pending' || (int) $traLoi->luot_cham !== $attempt) {
                return false;
            }
            if ($lanThi->trangthai === 'abandoned') {
                app(AIService::class)->markFailedAndRefund(
                    $traLoiId, (int) $lanThi->nguoidung_id,
                    $traLoi->ngay_tru_luot, $attempt, 'Bài thi đã bỏ dở, lượt AI đã được hoàn.'
                );

                return false;
            }
            $diemToiDa = (float) Lanthicauhoi::query()->whereKey($traLoi->lanthicauhoi_id)->value('diemtoida');
            $diemTho = round(($diemDet - 10) / 150 * $diemToiDa, 2);
            $this->ghiNhanDiemCham($traLoiId, $diemTho, $nhanXet, true, $diemDet);

            return true;
        }, 3);
    }

    private function baoLoi(string $truong, string $thongBao): never
    {
        throw ValidationException::withMessages([
            $truong => $thongBao,
        ]);
    }

    public function ghiNhanDiemCham(
        int $traLoiId,
        float $diem,
        string $nhanXet = '',
        bool $laAi = true,
        ?float $diemAiDet = null
    ): Lanthi {
        if (! is_finite($diem)) {
            $this->baoLoi('diem', 'Điểm chấm không hợp lệ.');
        }

        return DB::transaction(function () use (
            $traLoiId,
            $diem,
            $nhanXet,
            $laAi,
            $diemAiDet
        ) {
            $thongTin = DB::table('traloi as tl')
                ->join(
                    'lanthicauhoi as lc',
                    'lc.id',
                    '=',
                    'tl.lanthicauhoi_id'
                )
                ->where('tl.id', $traLoiId)
                ->first([
                    'lc.lanthi_id',
                    'lc.id as cau_id',
                ]);

            abort_if(! $thongTin, 404, 'Không tìm thấy câu trả lời.');

            $lanThi = Lanthi::query()
                ->whereKey($thongTin->lanthi_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lanThi->trangthai, [
                'in_progress',
                'completed',
            ], true)) {
                $this->baoLoi(
                    'lanthi',
                    'Không cập nhật điểm cho bài thi đã bỏ dở.'
                );
            }

            $cau = Lanthicauhoi::query()
                ->whereKey($thongTin->cau_id)
                ->where('lanthi_id', $lanThi->id)
                ->lockForUpdate()
                ->firstOrFail();

            $traLoi = Traloi::query()
                ->whereKey($traLoiId)
                ->where('lanthicauhoi_id', $cau->id)
                ->lockForUpdate()
                ->firstOrFail();

            $cachCham = DB::table('cauhoi as ch')
                ->join('loaicauhoi as loai', 'loai.id', '=', 'ch.loai_id')
                ->where('ch.id', $cau->cauhoi_id)
                ->value('loai.cachcham');

            if ($laAi && $cachCham !== 'ai') {
                $this->baoLoi(
                    'cauhoi',
                    'Câu hỏi này không được cấu hình chấm bằng AI.'
                );
            }

            $diemToiDa = (float) $cau->diemtoida;

            if (
                ! $cau->datraloi
                || $diemToiDa <= 0
                || $diem < 0
                || $diem > $diemToiDa
            ) {
                $this->baoLoi(
                    'diem',
                    'Điểm phải nằm trong khoảng 0 đến điểm tối đa của câu.'
                );
            }

            if ($lanThi->chungChi()->whereNull('tieude')->exists()) {
                $this->baoLoi('certificate', 'Không đổi điểm của lượt thi đã cấp chứng chỉ.');
            }

            $diem = round($diem, 2);

            $traLoi->setAttribute('diem', $diem);
            $traLoi->setAttribute('ladung', null);

            if ($laAi) {
                $traLoi->setAttribute('diem_ai', $diemAiDet ?? $diem);
                $traLoi->setAttribute('nhanxet_ai', $nhanXet);
                $traLoi->setAttribute('trangthai_ai', 'done');
            } else {
                $traLoi->setAttribute('trangthai_ai', null);
                $traLoi->setAttribute('diem_ai', null);
                $traLoi->setAttribute('nhanxet_ai', null);
            }

            $traLoi->save();

            if ($lanThi->trangthai === 'completed') {
                $this->ganDiemBaiThi($lanThi);
                $lanThi->save();
            }

            return $lanThi->refresh();
        }, 3);
    }
}
