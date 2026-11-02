<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\Danhmuc;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kiểm tra màn hình Dạng bài (AD02) và Chủ đề (AD03) - TV3.
 *
 * Dạng bài là dữ liệu nền cho import JSON: mã (`ma`) phải giữ nguyên,
 * chỉ cho sửa tên hiển thị, cách chấm, thời gian và trạng thái bật/tắt.
 */
class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Admin Danh mục',
            'email' => 'admin-catalog@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);
    }

    private function loai(string $ma = 'read_and_select'): Loaicauhoi
    {
        return Loaicauhoi::create([
            'ma' => $ma,
            'ten' => 'Đọc và chọn',
            'kynang' => 'reading',
            'cachcham' => 'auto',
            'thoigian' => 60,
            'mota' => null,
            'is_active' => true,
        ]);
    }

    public function test_admin_xem_duoc_danh_sach_dang_bai(): void
    {
        $this->loai();

        $this->actingAs($this->admin())
            ->get(route('admin.question-types.index'))
            ->assertOk()
            ->assertSee('read_and_select')
            ->assertSee('Đọc và chọn');
    }

    public function test_cap_nhat_duoc_cau_hinh_dang_bai_va_giu_nguyen_ma(): void
    {
        $loai = $this->loai();

        $response = $this->actingAs($this->admin())
            ->patch(route('admin.question-types.update', $loai), [
                'ma' => 'ma-bi-doi',
                'ten' => 'Tên mới',
                'cachcham' => 'ai',
                'thoigian' => 120,
                'is_active' => '1',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $loai->refresh();

        $this->assertSame('read_and_select', $loai->ma);
        $this->assertSame('Tên mới', $loai->ten);
        $this->assertSame('ai', $loai->cachcham);
        $this->assertSame(120, $loai->thoigian);
        $this->assertTrue($loai->is_active);
    }

    public function test_tat_dang_bai_khi_khong_gui_is_active(): void
    {
        $loai = $this->loai();

        $this->actingAs($this->admin())
            ->patch(route('admin.question-types.update', $loai), [
                'ten' => 'Đọc và chọn',
                'cachcham' => 'auto',
                'thoigian' => 60,
            ])
            ->assertSessionHas('success');

        $this->assertFalse($loai->refresh()->is_active);
    }

    public function test_chan_cach_cham_khong_hop_le(): void
    {
        $loai = $this->loai();

        $this->actingAs($this->admin())
            ->from(route('admin.question-types.index'))
            ->patch(route('admin.question-types.update', $loai), [
                'ten' => 'Đọc và chọn',
                'cachcham' => 'khong-hop-le',
            ])
            ->assertRedirect(route('admin.question-types.index'))
            ->assertSessionHasErrors('cachcham');

        $this->assertSame('auto', $loai->refresh()->cachcham);
    }

    public function test_danh_sach_chu_de_hien_so_cau_hoi(): void
    {
        $danhmuc = Danhmuc::create(['ten' => 'Daily Life', 'mota' => 'Đời sống']);

        Cauhoi::create([
            'loai_id' => $this->loai()->id,
            'danhmuc_id' => $danhmuc->id,
            'noidung' => 'Câu hỏi chủ đề ___ .',
            'dokho' => 3,
            'diem' => 1,
            'trangthai' => Cauhoi::TRANGTHAI_DRAFT,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Daily Life')
            ->assertSee('Đời sống');
    }

    public function test_them_chu_de_moi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), [
                'ten' => 'Science',
                'mota' => 'Khoa học',
            ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('danhmuc', ['ten' => 'Science', 'mota' => 'Khoa học']);
    }

    public function test_chan_ten_chu_de_trung(): void
    {
        Danhmuc::create(['ten' => 'Travel']);

        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), ['ten' => 'Travel'])
            ->assertSessionHasErrors('ten');

        $this->assertSame(1, Danhmuc::where('ten', 'Travel')->count());
    }

    public function test_sua_duoc_chu_de(): void
    {
        $danhmuc = Danhmuc::create(['ten' => 'Travel']);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $danhmuc))
            ->assertOk()
            ->assertSee('Travel');

        $this->actingAs($admin)
            ->patch(route('admin.categories.update', $danhmuc), [
                'ten' => 'Travel & Culture',
                'mota' => 'Du lịch',
            ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('danhmuc', [
            'id' => $danhmuc->id,
            'ten' => 'Travel & Culture',
        ]);
    }

    public function test_xoa_duoc_chu_de_chua_co_cau_hoi(): void
    {
        $danhmuc = Danhmuc::create(['ten' => 'Empty']);

        $this->actingAs($this->admin())
            ->delete(route('admin.categories.destroy', $danhmuc))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('danhmuc', ['id' => $danhmuc->id]);
    }

    public function test_khong_xoa_duoc_chu_de_dang_co_cau_hoi(): void
    {
        $danhmuc = Danhmuc::create(['ten' => 'In Use']);

        Cauhoi::create([
            'loai_id' => $this->loai()->id,
            'danhmuc_id' => $danhmuc->id,
            'noidung' => 'Câu hỏi ___ .',
            'dokho' => 3,
            'diem' => 1,
            'trangthai' => Cauhoi::TRANGTHAI_DRAFT,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.categories.destroy', $danhmuc))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('danhmuc', ['id' => $danhmuc->id]);
    }

    public function test_hoc_vien_khong_vao_duoc_man_hinh_danh_muc(): void
    {
        $hocVien = Nguoidung::create([
            'hoten' => 'Học viên',
            'email' => 'hocvien-catalog@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->actingAs($hocVien)
            ->get(route('admin.question-types.index'))
            ->assertForbidden();

        $this->actingAs($hocVien)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }
}
