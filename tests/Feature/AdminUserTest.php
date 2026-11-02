<?php

namespace Tests\Feature;

use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kiểm tra màn hình Người dùng (AD04) - TV3.
 *
 * Admin được khóa/mở khóa và đổi vai trò, nhưng không thể tự khóa
 * hoặc tự hạ quyền chính mình.
 */
class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Quản trị viên',
            'email' => 'admin-users@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);
    }

    private function hocVien(string $email = 'hocvien@example.com'): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Nguyễn Văn A',
            'email' => $email,
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);
    }

    public function test_admin_xem_duoc_danh_sach_nguoi_dung(): void
    {
        $hocVien = $this->hocVien('hien-thi@example.com');

        $this->actingAs($this->admin())
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('hien-thi@example.com')
            ->assertSee('Nguyễn Văn A');
    }

    public function test_loc_nguoi_dung_theo_tu_khoa_vai_tro_va_trang_thai(): void
    {
        $hocVien = $this->hocVien('tim-thay@example.com');
        $khac = $this->hocVien('khong-lien-quan@example.com');
        $khac->update(['trangthai' => 'locked']);

        $this->actingAs($this->admin());

        $this->get(route('admin.users.index', ['q' => 'tim-thay']))
            ->assertOk()
            ->assertSee('tim-thay@example.com')
            ->assertDontSee('khong-lien-quan@example.com');

        $this->get(route('admin.users.index', ['trangthai' => 'locked']))
            ->assertOk()
            ->assertSee('khong-lien-quan@example.com')
            ->assertDontSee('tim-thay@example.com');

        $this->get(route('admin.users.index', ['vaitro' => 'admin']))
            ->assertOk()
            ->assertSee('admin-users@example.com')
            ->assertDontSee('tim-thay@example.com');
    }

    public function test_khoa_va_mo_khoa_tai_khoan(): void
    {
        $hocVien = $this->hocVien();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $hocVien), ['trangthai' => 'locked'])
            ->assertSessionHas('success');

        $this->assertSame('locked', $hocVien->refresh()->trangthai);

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $hocVien), ['trangthai' => 'active'])
            ->assertSessionHas('success');

        $this->assertSame('active', $hocVien->refresh()->trangthai);
    }

    public function test_admin_khong_tu_khoa_duoc_chinh_minh(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.status', $admin), ['trangthai' => 'locked'])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertSame('active', $admin->refresh()->trangthai);
    }

    public function test_doi_vai_tro_cho_nguoi_dung(): void
    {
        $hocVien = $this->hocVien();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $hocVien), ['vaitro' => 'admin'])
            ->assertSessionHas('success');

        $this->assertSame('admin', $hocVien->refresh()->vaitro);

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $hocVien), ['vaitro' => 'user'])
            ->assertSessionHas('success');

        $this->assertSame('user', $hocVien->refresh()->vaitro);
    }

    public function test_admin_khong_tu_ha_quyen_duoc(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.role', $admin), ['vaitro' => 'user'])
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->refresh()->vaitro);
    }

    public function test_chan_gia_tri_vai_tro_khong_hop_le(): void
    {
        $hocVien = $this->hocVien();

        $this->actingAs($this->admin())
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.role', $hocVien), ['vaitro' => 'super-admin'])
            ->assertSessionHasErrors('vaitro');

        $this->assertSame('user', $hocVien->refresh()->vaitro);
    }

    public function test_hoc_vien_khong_vao_duoc_quan_ly_nguoi_dung(): void
    {
        $hocVien = $this->hocVien();

        $this->actingAs($hocVien)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($hocVien)
            ->patch(route('admin.users.status', $hocVien), ['trangthai' => 'locked'])
            ->assertForbidden();
    }
}
