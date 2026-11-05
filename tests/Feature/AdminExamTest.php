<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\Dethi;
use App\Models\Dethicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kiểm tra màn hình Đề thi (AD07) trong trang quản trị.
 */
class AdminExamTest extends TestCase
{
    use RefreshDatabase;

    private function nguoidung(string $email, string $vaitro): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => $vaitro === 'admin' ? 'Quản trị viên' : 'Học viên',
            'email' => $email,
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => $vaitro,
        ]);
    }

    public function test_admin_xem_duoc_danh_sach_de_thi(): void
    {
        $admin = $this->nguoidung('admin-exam@example.com', 'admin');
        Dethi::create([
            'ten' => 'Đề thi hiển thị trong quản trị',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 3600,
            'trangthai' => 'published',
            'nguoitao' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.exams.index'))
            ->assertOk()
            ->assertSee('Đề thi hiển thị trong quản trị')
            ->assertSee('Đã xuất bản');
    }

    public function test_admin_xem_duoc_chi_tiet_va_cau_truc_de_thi(): void
    {
        $admin = $this->nguoidung('admin-exam-detail@example.com', 'admin');
        $loai = Loaicauhoi::create([
            'ma' => 'exam_admin_test',
            'ten' => 'Dạng bài kiểm tra',
            'kynang' => 'reading',
        ]);
        $cauhoi = Cauhoi::create([
            'ma' => 'exam-admin-cau-01',
            'loai_id' => $loai->id,
            'noidung' => 'Nội dung câu hỏi trong đề quản trị',
            'trangthai' => 'published',
        ]);
        $dethi = Dethi::create([
            'ten' => 'Đề thi chi tiết',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 1800,
            'trangthai' => 'draft',
            'nguoitao' => $admin->id,
        ]);
        Dethicauhoi::create([
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'diem' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.exams.show', $dethi))
            ->assertOk()
            ->assertSee('Đề thi chi tiết')
            ->assertSee('exam-admin-cau-01')
            ->assertSee('Nội dung câu hỏi trong đề quản trị');
    }

    public function test_hoc_vien_khong_vao_duoc_quan_ly_de_thi(): void
    {
        $hocVien = $this->nguoidung('hocvien-exam@example.com', 'user');

        $this->actingAs($hocVien)
            ->get(route('admin.exams.index'))
            ->assertForbidden();
    }
}
