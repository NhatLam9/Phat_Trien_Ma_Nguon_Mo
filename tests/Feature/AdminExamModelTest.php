<?php

namespace Tests\Feature;

use App\Models\Ailog;
use App\Models\Aisudung;
use App\Models\Cauhoi;
use App\Models\Dethi;
use App\Models\Dethicauhoi;
use App\Models\Loaicauhoi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Kiểm tra model đề thi và nhật ký AI của TV3 (Phi Phong).
 */
class AdminExamModelTest extends TestCase
{
    use DatabaseTransactions;

    private function taoNguoiDung(string $email = 'admin@example.com'): int
    {
        return DB::table('nguoidung')->insertGetId([
            'hoten' => 'Quản trị viên',
            'email' => $email,
            'vaitro' => 'admin',
        ]);
    }

    public function test_de_thi_gan_cau_hoi_theo_thu_tu(): void
    {
        $nguoiDungId = $this->taoNguoiDung();
        $loai = Loaicauhoi::create([
            'ma' => 'dethi_test',
            'ten' => 'Loại kiểm tra đề',
            'kynang' => 'reading',
        ]);

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi trong đề',
            'trangthai' => 'published',
        ]);

        $dethi = Dethi::create([
            'ten' => 'Đề thi thử số 1',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 3600,
            'nguoitao' => $nguoiDungId,
        ]);

        Dethicauhoi::create([
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'diem' => 2,
        ]);

        $dethi->refresh();

        $this->assertSame('draft', $dethi->trangthai);
        $this->assertCount(1, $dethi->cauhoi);
        $this->assertSame(1, $dethi->cauhoi->first()->pivot->thutu);
        $this->assertNotNull($dethi->ngaytao);

        // Phụ thuộc model Nguoidung của TV1: chỉ kiểm tra khi model đã tồn tại.
        if (class_exists(\App\Models\Nguoidung::class)) {
            $this->assertSame($nguoiDungId, $dethi->nguoitao->id);
        }
    }

    public function test_nhat_ky_ai_chi_luu_ngay_tao(): void
    {
        $nguoiDungId = $this->taoNguoiDung('ai@example.com');

        $log = Ailog::create([
            'nguoidung_id' => $nguoiDungId,
            'loai' => 'writing',
            'dauvao' => 'Bài viết của học viên',
            'daura' => 'Nhận xét của AI',
            'model' => 'gemini-1.5-flash',
            'token_vao' => 120,
            'token_ra' => 80,
        ]);

        $this->assertNotNull($log->ngaytao);
        $this->assertSame(120, $log->token_vao);
        $this->assertDatabaseHas('ailog', [
            'id' => $log->id,
            'nguoidung_id' => $nguoiDungId,
        ]);

        // Phụ thuộc model Nguoidung của TV1: chỉ kiểm tra khi model đã tồn tại.
        if (class_exists(\App\Models\Nguoidung::class)) {
            $this->assertSame($nguoiDungId, $log->nguoidung->id);
        }
    }

    public function test_luot_su_dung_ai_theo_ngay(): void
    {
        $nguoiDungId = $this->taoNguoiDung('limit@example.com');

        $luot = Aisudung::create([
            'nguoidung_id' => $nguoiDungId,
            'ngay' => '2026-10-20',
            'solan' => 3,
        ]);

        $this->assertSame('2026-10-20', $luot->ngay->format('Y-m-d'));
        $this->assertSame(3, $luot->solan);

        // Phụ thuộc model Nguoidung của TV1: chỉ kiểm tra khi model đã tồn tại.
        if (class_exists(\App\Models\Nguoidung::class)) {
            $this->assertSame($nguoiDungId, $luot->nguoidung->id);
        }
    }
}
