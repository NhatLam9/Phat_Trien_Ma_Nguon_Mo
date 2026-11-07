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

        $this->actingAs($hocVien)
            ->get(route('admin.exams.create'))
            ->assertForbidden();

        $this->actingAs($hocVien)
            ->post(route('admin.exams.store'), [
                'ten' => 'Đề học viên tự tạo',
                'chedo' => 'full',
                'kieu' => 'fixed',
                'thoigian_phut' => 30,
                'trangthai' => 'draft',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('dethi', ['ten' => 'Đề học viên tự tạo']);
    }

    public function test_admin_xem_duoc_trang_tao_va_chinh_sua_de(): void
    {
        $admin = $this->nguoidung('admin-form-de@example.com', 'admin');
        $loai = Loaicauhoi::create([
            'ma' => 'exam_form_render',
            'ten' => 'Dạng bài hiển thị form',
            'kynang' => 'reading',
        ]);
        Cauhoi::create([
            'ma' => 'exam-form-cau-01',
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi hiển thị trong form thêm',
            'trangthai' => 'published',
        ]);
        $dethi = Dethi::create([
            'ten' => 'Đề hiển thị form chỉnh sửa',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 1200,
            'trangthai' => 'draft',
            'nguoitao' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.exams.create'))
            ->assertOk()
            ->assertSee('Tạo đề thi mới')
            ->assertSee('Thời gian làm bài (phút)');

        $this->actingAs($admin)
            ->get(route('admin.exams.edit', $dethi))
            ->assertOk()
            ->assertSee('Cấu trúc đề hiện tại')
            ->assertSee('Thêm câu hỏi từ ngân hàng')
            ->assertSee('Câu hỏi hiển thị trong form thêm');
    }

    public function test_admin_tao_duoc_de_thi_bang_giao_dien(): void
    {
        $admin = $this->nguoidung('admin-tao-de@example.com', 'admin');

        $response = $this->actingAs($admin)
            ->post(route('admin.exams.store'), [
                'ten' => 'Đề tạo bằng giao diện',
                'mota' => 'Mô tả đề tạo bằng giao diện',
                'chedo' => 'section',
                'kieu' => 'fixed',
                'kynang' => 'reading',
                'thoigian_phut' => 30,
                'trangthai' => 'draft',
            ]);

        $dethi = Dethi::query()->where('ten', 'Đề tạo bằng giao diện')->first();

        $this->assertNotNull($dethi);
        $response->assertRedirect(route('admin.exams.edit', $dethi));
        $this->assertSame(1800, $dethi->thoigian);
        $this->assertSame('reading', $dethi->kynang);
        $this->assertSame($admin->id, $dethi->nguoitao);
        $this->assertSame(0.0, (float) $dethi->tongdiem);
    }

    public function test_de_theo_ky_nang_bat_buoc_chon_ky_nang(): void
    {
        $admin = $this->nguoidung('admin-thieu-ky-nang@example.com', 'admin');

        $this->actingAs($admin)
            ->from(route('admin.exams.create'))
            ->post(route('admin.exams.store'), [
                'ten' => 'Đề thiếu kỹ năng',
                'chedo' => 'section',
                'kieu' => 'fixed',
                'kynang' => '',
                'thoigian_phut' => 45,
                'trangthai' => 'draft',
            ])
            ->assertSessionHasErrors('kynang');

        $this->assertDatabaseMissing('dethi', ['ten' => 'Đề thiếu kỹ năng']);
    }

    public function test_admin_them_cau_hoi_va_tinh_lai_tong_diem(): void
    {
        $admin = $this->nguoidung('admin-them-cau@example.com', 'admin');
        $loai = Loaicauhoi::create([
            'ma' => 'exam_add_question',
            'ten' => 'Dạng bài thêm câu',
            'kynang' => 'reading',
        ]);
        $cauHoiMot = Cauhoi::create([
            'ma' => 'exam-them-01',
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi thứ nhất',
            'diem' => 2,
            'trangthai' => 'published',
        ]);
        $cauHoiHai = Cauhoi::create([
            'ma' => 'exam-them-02',
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi thứ hai',
            'diem' => 3.5,
            'trangthai' => 'published',
        ]);
        $dethi = Dethi::create([
            'ten' => 'Đề để thêm câu hỏi',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 1800,
            'trangthai' => 'draft',
            'nguoitao' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.exams.questions.add', $dethi), [
                'cauhoi_ids' => [$cauHoiMot->id, $cauHoiHai->id],
            ])
            ->assertRedirect(route('admin.exams.edit', $dethi));

        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHoiMot->id,
            'thutu' => 1,
        ]);
        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHoiHai->id,
            'thutu' => 2,
        ]);
        $this->assertSame(5.5, (float) $dethi->fresh()->tongdiem);
    }

    public function test_admin_bo_cau_hoi_se_danh_so_lai_va_cap_nhat_tong_diem(): void
    {
        $admin = $this->nguoidung('admin-bo-cau@example.com', 'admin');
        $loai = Loaicauhoi::create([
            'ma' => 'exam_remove_question',
            'ten' => 'Dạng bài bỏ câu',
            'kynang' => 'reading',
        ]);
        $dethi = Dethi::create([
            'ten' => 'Đề để bỏ câu hỏi',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 1800,
            'trangthai' => 'draft',
            'nguoitao' => $admin->id,
        ]);

        $cauHois = collect([1, 2, 3])->map(function (int $soThuTu) use ($loai, $dethi) {
            $cauhoi = Cauhoi::create([
                'ma' => 'exam-bo-0'.$soThuTu,
                'loai_id' => $loai->id,
                'noidung' => 'Câu hỏi số '.$soThuTu,
                'diem' => $soThuTu,
                'trangthai' => 'published',
            ]);

            Dethicauhoi::create([
                'dethi_id' => $dethi->id,
                'cauhoi_id' => $cauhoi->id,
                'thutu' => $soThuTu,
                'diem' => $soThuTu,
            ]);

            return $cauhoi;
        });

        $this->actingAs($admin)
            ->delete(route('admin.exams.questions.remove', [$dethi, $cauHois[1]->id]))
            ->assertRedirect(route('admin.exams.edit', $dethi));

        $this->assertDatabaseMissing('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHois[1]->id,
        ]);
        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHois[0]->id,
            'thutu' => 1,
        ]);
        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHois[2]->id,
            'thutu' => 2,
        ]);
        $this->assertSame(4.0, (float) $dethi->fresh()->tongdiem);
    }

    public function test_admin_doi_duoc_thu_tu_cau_hoi(): void
    {
        $admin = $this->nguoidung('admin-doi-thu-tu@example.com', 'admin');
        $loai = Loaicauhoi::create([
            'ma' => 'exam_move_question',
            'ten' => 'Dạng bài đổi thứ tự',
            'kynang' => 'reading',
        ]);
        $dethi = Dethi::create([
            'ten' => 'Đề để đổi thứ tự',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 1800,
            'trangthai' => 'draft',
            'nguoitao' => $admin->id,
        ]);

        $cauHois = collect([1, 2, 3])->map(function (int $soThuTu) use ($loai, $dethi) {
            $cauhoi = Cauhoi::create([
                'ma' => 'exam-doi-0'.$soThuTu,
                'loai_id' => $loai->id,
                'noidung' => 'Câu hỏi đổi thứ tự '.$soThuTu,
                'trangthai' => 'published',
            ]);

            Dethicauhoi::create([
                'dethi_id' => $dethi->id,
                'cauhoi_id' => $cauhoi->id,
                'thutu' => $soThuTu,
                'diem' => 1,
            ]);

            return $cauhoi;
        });

        $this->actingAs($admin)
            ->patch(route('admin.exams.questions.move', [$dethi, $cauHois[0]->id]), [
                'huong' => 'down',
            ])
            ->assertRedirect(route('admin.exams.edit', $dethi));

        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHois[0]->id,
            'thutu' => 2,
        ]);
        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHois[1]->id,
            'thutu' => 1,
        ]);
        $this->assertDatabaseHas('dethicauhoi', [
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauHois[2]->id,
            'thutu' => 3,
        ]);
    }

    public function test_admin_cap_nhat_duoc_thong_tin_de_thi(): void
    {
        $admin = $this->nguoidung('admin-sua-de@example.com', 'admin');
        $dethi = Dethi::create([
            'ten' => 'Đề cần cập nhật',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 600,
            'trangthai' => 'draft',
            'nguoitao' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.exams.update', $dethi), [
                'ten' => 'Đề đã cập nhật',
                'mota' => 'Mô tả mới',
                'chedo' => 'section',
                'kieu' => 'adaptive',
                'kynang' => 'speaking',
                'thoigian_phut' => 15,
                'trangthai' => 'published',
            ])
            ->assertRedirect(route('admin.exams.edit', $dethi));

        $dethi->refresh();

        $this->assertSame('Đề đã cập nhật', $dethi->ten);
        $this->assertSame('section', $dethi->chedo);
        $this->assertSame('adaptive', $dethi->kieu);
        $this->assertSame('speaking', $dethi->kynang);
        $this->assertSame(900, $dethi->thoigian);
        $this->assertSame('published', $dethi->trangthai);
    }
}
