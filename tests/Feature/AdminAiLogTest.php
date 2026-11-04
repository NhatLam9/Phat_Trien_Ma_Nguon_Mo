<?php

namespace Tests\Feature;

use App\Models\Ailog;
use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kiểm tra màn hình Nhật ký AI (AD06) - TV3.
 *
 * Màn hình chỉ đọc: xem lượt gọi, lọc theo người dùng/loại/ngày.
 */
class AdminAiLogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Admin AI',
            'email' => 'admin-ailog@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);
    }

    private function nguoiDung(string $email = 'nguoidung@example.com', string $hoten = 'Người dùng AI'): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => $hoten,
            'email' => $email,
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);
    }

    private function nhatKy(Nguoidung $nguoidung, string $loai = 'writing', string $dauVao = 'Bài viết thử'): Ailog
    {
        return Ailog::create([
            'nguoidung_id' => $nguoidung->id,
            'loai' => $loai,
            'dauvao' => $dauVao,
            'daura' => 'Kết quả chấm thử',
            'model' => 'gpt-test',
            'token_vao' => 100,
            'token_ra' => 50,
        ]);
    }

    public function test_admin_xem_duoc_nhat_ky_ai(): void
    {
        $nguoidung = $this->nguoiDung('hien-thi-ai@example.com', 'Trần Thị B');

        $this->nhatKy($nguoidung, 'writing', 'Đoạn văn kiểm thử đặc biệt');

        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index'))
            ->assertOk()
            ->assertSee('hien-thi-ai@example.com')
            ->assertSee('Trần Thị B')
            ->assertSee('gpt-test')
            ->assertSee('Đoạn văn kiểm thử đặc biệt');
    }

    public function test_loc_nhat_ky_theo_loai(): void
    {
        $nguoidung = $this->nguoiDung('loc-loai@example.com');

        $this->nhatKy($nguoidung, 'writing', 'Nội dung writing');
        $this->nhatKy($nguoidung, 'speaking', 'Nội dung speaking');

        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index', ['loai' => 'speaking']))
            ->assertOk()
            ->assertSee('Nội dung speaking')
            ->assertDontSee('Nội dung writing');
    }

    public function test_loc_nhat_ky_theo_nguoi_dung(): void
    {
        $canTim = $this->nguoiDung('can-tim-ai@example.com', 'Người Cần Tìm');
        $khac = $this->nguoiDung('khong-lien-quan-ai@example.com', 'Người Khác');

        $this->nhatKy($canTim, 'writing', 'Log của người cần tìm');
        $this->nhatKy($khac, 'writing', 'Log của người khác');

        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index', ['q' => 'Cần Tìm']))
            ->assertOk()
            ->assertSee('Log của người cần tìm')
            ->assertDontSee('Log của người khác');
    }

    public function test_loc_nhat_ky_theo_khoang_ngay(): void
    {
        $nguoidung = $this->nguoiDung('loc-ngay@example.com');

        $logCu = Ailog::create([
            'nguoidung_id' => $nguoidung->id,
            'loai' => 'grammar',
            'dauvao' => 'Log cũ',
            'daura' => null,
            'model' => 'gpt-test',
            'token_vao' => 10,
            'token_ra' => 5,
        ]);

        // Eloquent tự ghi đè ngaytao khi tạo, nên chỉnh thẳng bằng query builder.
        Ailog::whereKey($logCu->id)->update(['ngaytao' => now()->subDays(10)]);

        $this->nhatKy($nguoidung, 'grammar', 'Log hôm nay');

        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index', [
                'tu_ngay' => now()->subDay()->toDateString(),
                'den_ngay' => now()->addDay()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Log hôm nay')
            ->assertDontSee('Log cũ');
    }

    public function test_chan_loai_yeu_cau_khong_hop_le(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index', ['loai' => 'khong-hop-le']))
            ->assertSessionHasErrors('loai');
    }

    public function test_chan_khoang_ngay_nguoc(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index', [
                'tu_ngay' => '2026-11-10',
                'den_ngay' => '2026-11-01',
            ]))
            ->assertSessionHasErrors('den_ngay');
    }

    public function test_loc_nhat_ky_theo_trang_thai(): void
    {
        $nguoidung = $this->nguoiDung('loc-trangthai@example.com');

        $logThanhCong = $this->nhatKy($nguoidung, 'writing', 'Log thành công');
        $logThatBai = $this->nhatKy($nguoidung, 'writing', 'Log thất bại');

        Ailog::whereKey($logThatBai->id)->update([
            'trangthai' => 'failed',
            'http_code' => 500,
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.ai-logs.index', ['trangthai' => 'failed']))
            ->assertOk()
            ->assertSee('Log thất bại')
            ->assertDontSee('Log thành công')
            ->assertSee('HTTP 500');

        $this->actingAs($admin)
            ->get(route('admin.ai-logs.index', ['trangthai' => 'success']))
            ->assertOk()
            ->assertSee('Log thành công')
            ->assertDontSee('Log thất bại');

        $this->assertNotNull($logThanhCong->id);
    }

    public function test_chan_trang_thai_khong_hop_le(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.index', ['trangthai' => 'khong-hop-le']))
            ->assertSessionHasErrors('trangthai');
    }

    public function test_xem_chi_tiet_nhat_ky_ai(): void
    {
        $log = $this->nhatKy($this->nguoiDung('chi-tiet-ai@example.com'), 'speaking', 'Bản ghi âm thử');

        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.show', $log->id))
            ->assertOk()
            ->assertSee('Bản ghi âm thử')
            ->assertSee('Kết quả chấm thử')
            ->assertSee('gpt-test');
    }

    public function test_xem_chi_tiet_nhat_ky_khong_ton_tai(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.ai-logs.show', 999999))
            ->assertNotFound();
    }

    public function test_hoc_vien_khong_vao_duoc_nhat_ky_ai(): void
    {
        $hocVien = $this->nguoiDung('hocvien-ailog@example.com');

        $this->actingAs($hocVien)
            ->get(route('admin.ai-logs.index'))
            ->assertForbidden();
    }
}
