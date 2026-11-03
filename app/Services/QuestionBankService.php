<?php

namespace App\Services;

use App\Models\Cauhoi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * Nghiep vu ngan hang cau hoi (TV3).
 *
 * - Kiem tra dieu kien truoc khi xuat ban (published).
 * - Quan ly chuyen trang thai draft -> review -> published -> archived.
 * - Chan xoa cau hoi dang nam trong de thi.
 */
class QuestionBankService
{
    /**
     * Cac buoc chuyen trang thai duoc phep.
     *
     * @var array<string, array<int, string>>
     */
    public const TRANSITIONS = [
        Cauhoi::TRANGTHAI_DRAFT => [Cauhoi::TRANGTHAI_REVIEW, Cauhoi::TRANGTHAI_ARCHIVED],
        Cauhoi::TRANGTHAI_REVIEW => [Cauhoi::TRANGTHAI_PUBLISHED, Cauhoi::TRANGTHAI_DRAFT, Cauhoi::TRANGTHAI_ARCHIVED],
        Cauhoi::TRANGTHAI_PUBLISHED => [Cauhoi::TRANGTHAI_ARCHIVED],
        Cauhoi::TRANGTHAI_ARCHIVED => [Cauhoi::TRANGTHAI_DRAFT],
    ];

    /**
     * Danh sach trang thai co the chuyen toi tu trang thai hien tai.
     *
     * @return array<int, string>
     */
    public function trangThaiChoPhep(Cauhoi $cauhoi): array
    {
        return self::TRANSITIONS[$cauhoi->trangthai] ?? [];
    }

    public function coTheChuyen(Cauhoi $cauhoi, string $trangthaiMoi): bool
    {
        return in_array($trangthaiMoi, $this->trangThaiChoPhep($cauhoi), true);
    }

    /**
     * Kiem tra cau hoi co du dieu kien xuat ban hay khong.
     *
     * @return array<int, string> Danh sach loi; rong nghia la hop le.
     */
    public function loiKhiXuatBan(Cauhoi $cauhoi): array
    {
        $cauhoi->loadMissing(['loai', 'luachon', 'cauhoiDoc.luachonDoc', 'cauhoiNghe.luachonNghe']);

        $loi = [];
        $loaiMa = $cauhoi->loai?->ma;

        if ($cauhoi->loai === null) {
            $loi[] = 'Câu hỏi chưa gắn loại câu hỏi.';

            return $loi;
        }

        if (trim((string) $cauhoi->noidung) === '') {
            $loi[] = 'Nội dung câu hỏi không được rỗng.';
        }

        foreach ([['anh', 'ảnh'], ['audio', 'audio']] as [$cot, $nhan]) {
            $duongDan = $cauhoi->{$cot};

            if ($duongDan !== null && $duongDan !== '' && ! File::exists(public_path($duongDan))) {
                $loi[] = "Tệp {$nhan} \"{$duongDan}\" không tồn tại trong thư mục public/.";
            }
        }

        if ($cauhoi->laTracNghiemTuDong()) {
            $soLuachon = $cauhoi->luachon->count();
            $soDapAnDung = $cauhoi->soDapAnDung();

            if ($soLuachon < 2) {
                $loi[] = 'Câu trắc nghiệm cần ít nhất 2 lựa chọn.';
            }

            if ($soDapAnDung !== 1) {
                $loi[] = "Câu trắc nghiệm phải có đúng 1 đáp án đúng (hiện có {$soDapAnDung}).";
            }
        }

        if ($loaiMa === 'interactive_reading') {
            if ($cauhoi->cauhoiDoc->isEmpty()) {
                $loi[] = 'Câu Interactive Reading cần ít nhất 1 câu hỏi con.';
            }

            foreach ($cauhoi->cauhoiDoc as $con) {
                $loi = array_merge($loi, $this->loiCuaCauCon(
                    $con,
                    "Câu con #{$con->thutu}",
                    $con->luachonDoc
                ));
            }
        }

        if ($loaiMa === 'interactive_listening') {
            if ($cauhoi->audio === null || $cauhoi->audio === '') {
                $loi[] = 'Câu Interactive Listening cần có tệp audio.';
            }

            if ($cauhoi->cauhoiNghe->isEmpty()) {
                $loi[] = 'Câu Interactive Listening cần ít nhất 1 câu hỏi con.';
            }

            foreach ($cauhoi->cauhoiNghe as $con) {
                $loi = array_merge($loi, $this->loiCuaCauCon(
                    $con,
                    "Câu nghe con #{$con->thutu}",
                    $con->luachonNghe
                ));
            }
        }

        if ($loaiMa === 'write_about_photo' && ($cauhoi->anh === null || $cauhoi->anh === '')) {
            $loi[] = 'Câu Write about Photo cần có ảnh minh họa.';
        }

        return array_values(array_unique($loi));
    }

    /**
     * Chuyen trang thai mot cau hoi.
     *
     * @return array{ok: bool, message: string, errors: array<int, string>}
     */
    public function chuyenTrangThai(Cauhoi $cauhoi, string $trangThaiMoi): array
    {
        if (! in_array($trangThaiMoi, Cauhoi::TRANGTHAI, true)) {
            return [
                'ok' => false,
                'message' => 'Trạng thái không hợp lệ.',
                'errors' => ['Trạng thái không hợp lệ.'],
            ];
        }

        if ($cauhoi->trangthai === $trangThaiMoi) {
            return [
                'ok' => true,
                'message' => 'Câu hỏi đã ở trạng thái này.',
                'errors' => [],
            ];
        }

        if (! $this->coTheChuyen($cauhoi, $trangThaiMoi)) {
            $choPhep = $this->trangThaiChoPhep($cauhoi);

            return [
                'ok' => false,
                'message' => sprintf(
                    'Không thể chuyển từ "%s" sang "%s". Cho phép: %s.',
                    $cauhoi->nhanTrangThai(),
                    Cauhoi::TRANGTHAI_NHAN[$trangThaiMoi] ?? $trangThaiMoi,
                    $choPhep === []
                        ? 'không có'
                        : implode(', ', array_map(
                            fn (string $tt): string => Cauhoi::TRANGTHAI_NHAN[$tt] ?? $tt,
                            $choPhep
                        ))
                ),
                'errors' => [],
            ];
        }

        if ($trangThaiMoi === Cauhoi::TRANGTHAI_PUBLISHED) {
            $loi = $this->loiKhiXuatBan($cauhoi);

            if ($loi !== []) {
                return [
                    'ok' => false,
                    'message' => 'Câu hỏi chưa đủ điều kiện xuất bản.',
                    'errors' => $loi,
                ];
            }
        }

        $cauhoi->update(['trangthai' => $trangThaiMoi]);

        return [
            'ok' => true,
            'message' => sprintf(
                'Đã chuyển câu hỏi %s sang "%s".',
                $cauhoi->ma,
                $cauhoi->nhanTrangThai()
            ),
            'errors' => [],
        ];
    }

    /**
     * Chuyen trang thai hang loat. Cau hoi khong hop le bi bo qua va ghi ly do.
     *
     * @param  array<int, int>  $ids
     * @return array{updated: int, skipped: array<int, array{ma: string, ly_do: string}>}
     */
    public function chuyenTrangThaiHangLoat(array $ids, string $trangThaiMoi): array
    {
        $updated = 0;
        $skipped = [];

        $cauhois = Cauhoi::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();

        foreach ($cauhois as $cauhoi) {
            $ketQua = $this->chuyenTrangThai($cauhoi, $trangThaiMoi);

            if ($ketQua['ok']) {
                $updated++;

                continue;
            }

            $skipped[] = [
                'ma' => (string) $cauhoi->ma,
                'ly_do' => $ketQua['errors'][0] ?? $ketQua['message'],
            ];
        }

        return [
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    /**
     * Xoa cau hoi. Chi cho phep khi cau hoi chua duoc dung trong de thi.
     *
     * @return array{ok: bool, message: string}
     */
    public function xoa(Cauhoi $cauhoi): array
    {
        $soDe = $cauhoi->dethicauhoi()->count();

        if ($soDe > 0) {
            return [
                'ok' => false,
                'message' => "Không thể xóa: câu hỏi {$cauhoi->ma} đang nằm trong {$soDe} đề thi. Hãy gỡ khỏi đề trước.",
            ];
        }

        if ($cauhoi->trangthai === Cauhoi::TRANGTHAI_PUBLISHED) {
            return [
                'ok' => false,
                'message' => 'Câu hỏi đã xuất bản. Hãy chuyển sang "Lưu trữ" thay vì xóa.',
            ];
        }

        $ma = $cauhoi->ma;
        $cauhoi->delete();

        return [
            'ok' => true,
            'message' => "Đã xóa câu hỏi {$ma}.",
        ];
    }

    /**
     * Thong ke nhanh theo trang thai cho trang danh sach.
     *
     * @return array<string, int>
     */
    public function thongKeTrangThai(): array
    {
        $dem = Cauhoi::query()
            ->selectRaw('trangthai, count(*) as tong')
            ->groupBy('trangthai')
            ->pluck('tong', 'trangthai');

        $ketQua = [];

        foreach (Cauhoi::TRANGTHAI as $trangthai) {
            $ketQua[$trangthai] = (int) ($dem[$trangthai] ?? 0);
        }

        return $ketQua;
    }

    /**
     * @param  Collection<int, mixed>  $luachon
     * @return array<int, string>
     */
    private function loiCuaCauCon(object $con, string $nhan, $luachon): array
    {
        $loi = [];

        $soDung = $luachon->where('ladung', true)->count();
        $coLuaChon = $luachon->isNotEmpty();

        if ($coLuaChon && $soDung !== 1) {
            $loi[] = "{$nhan} có lựa chọn nhưng không đúng 1 đáp án đúng.";
        }

        if (! $coLuaChon && ($con->dapandung === null || trim((string) $con->dapandung) === '')) {
            $loi[] = "{$nhan} không có lựa chọn nên phải có đáp án đúng.";
        }

        return $loi;
    }
}
