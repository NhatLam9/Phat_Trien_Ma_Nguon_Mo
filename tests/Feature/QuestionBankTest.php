<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\Dethi;
use App\Models\Dethicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Luachon;
use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kiểm tra module Ngân hàng câu hỏi (TV3 - Phi Phong).
 *
 * Phạm vi: danh sách + lọc, quy trình duyệt draft -> review -> published,
 * điều kiện xuất bản, duyệt hàng loạt, chặn xóa và khóa "ma" ổn định.
 */
class QuestionBankTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Admin Ngân hàng',
            'email' => 'admin-bank@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);
    }

    private function loai(string $ma = 'read_and_select', string $kynang = 'reading'): Loaicauhoi
    {
        return Loaicauhoi::firstOrCreate(
            ['ma' => $ma],
            [
                'ten' => ucfirst(str_replace('_', ' ', $ma)),
                'kynang' => $kynang,
                'cachcham' => 'auto',
                'thoigian' => 60,
                'mota' => null,
                'is_active' => true,
            ]
        );
    }

    /**
     * Tạo câu trắc nghiệm với số đáp án đúng tùy chọn.
     */
    private function cauHoi(
        string $trangthai = Cauhoi::TRANGTHAI_DRAFT,
        int $soDapAnDung = 1,
        string $noidung = 'Câu hỏi kiểm thử ___ .'
    ): Cauhoi {
        $loai = $this->loai();

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => $noidung,
            'dokho' => 3,
            'thoigian' => 60,
            'diem' => 1,
            'trangthai' => $trangthai,
        ]);

        foreach (['A', 'B', 'C'] as $thutu => $nhan) {
            Luachon::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => "Đáp án {$nhan}",
                'ladung' => $thutu < $soDapAnDung,
                'thutu' => $thutu + 1,
            ]);
        }

        return $cauhoi->refresh();
    }

    public function test_admin_xem_duoc_danh_sach_va_loc_theo_trang_thai(): void
    {
        $nhap = $this->cauHoi(Cauhoi::TRANGTHAI_DRAFT, 1, 'Câu bản nháp đặc biệt.');
        $duyet = $this->cauHoi(Cauhoi::TRANGTHAI_REVIEW, 1, 'Câu chờ duyệt đặc biệt.');

        $response = $this->actingAs($this->admin())
            ->get(route('admin.questions.index'));

        $response->assertOk();
        $response->assertSee($nhap->ma);
        $response->assertSee($duyet->ma);

        $this->get(route('admin.questions.index', ['trangthai' => 'review']))
            ->assertOk()
            ->assertSee($duyet->ma)
            ->assertDontSee($nhap->ma);

        $this->get(route('admin.questions.index', ['q' => 'bản nháp đặc biệt']))
            ->assertOk()
            ->assertSee($nhap->ma)
            ->assertDontSee($duyet->ma);
    }

    public function test_admin_xem_duoc_chi_tiet_cau_hoi(): void
    {
        $cauhoi = $this->cauHoi();

        $this->actingAs($this->admin())
            ->get(route('admin.questions.show', $cauhoi))
            ->assertOk()
            ->assertSee($cauhoi->ma)
            ->assertSee('Đáp án A');
    }

    public function test_khong_the_xuat_ban_khi_thieu_dap_an_dung(): void
    {
        $cauhoi = $this->cauHoi(Cauhoi::TRANGTHAI_REVIEW, 0);

        $response = $this->actingAs($this->admin())
            ->from(route('admin.questions.show', $cauhoi))
            ->patch(route('admin.questions.status', $cauhoi), [
                'trangthai' => Cauhoi::TRANGTHAI_PUBLISHED,
            ]);

        $response->assertRedirect(route('admin.questions.show', $cauhoi));
        $response->assertSessionHas('error');

        $this->assertSame(Cauhoi::TRANGTHAI_REVIEW, $cauhoi->refresh()->trangthai);
    }

    public function test_xuat_ban_thanh_cong_khi_du_dieu_kien(): void
    {
        $cauhoi = $this->cauHoi(Cauhoi::TRANGTHAI_REVIEW, 1);

        $response = $this->actingAs($this->admin())
            ->from(route('admin.questions.show', $cauhoi))
            ->patch(route('admin.questions.status', $cauhoi), [
                'trangthai' => Cauhoi::TRANGTHAI_PUBLISHED,
            ]);

        $response->assertRedirect(route('admin.questions.show', $cauhoi));
        $response->assertSessionHas('success');

        $this->assertSame(Cauhoi::TRANGTHAI_PUBLISHED, $cauhoi->refresh()->trangthai);
    }

    public function test_chan_nhay_buoc_trang_thai(): void
    {
        $cauhoi = $this->cauHoi(Cauhoi::TRANGTHAI_DRAFT, 1);

        $this->actingAs($this->admin())
            ->from(route('admin.questions.show', $cauhoi))
            ->patch(route('admin.questions.status', $cauhoi), [
                'trangthai' => Cauhoi::TRANGTHAI_PUBLISHED,
            ])
            ->assertSessionHas('error');

        $this->assertSame(Cauhoi::TRANGTHAI_DRAFT, $cauhoi->refresh()->trangthai);
    }

    public function test_duyet_hang_loat_bo_qua_cau_khong_hop_le(): void
    {
        $hopLe = $this->cauHoi(Cauhoi::TRANGTHAI_REVIEW, 1, 'Câu đủ điều kiện.');
        $khongHopLe = $this->cauHoi(Cauhoi::TRANGTHAI_DRAFT, 1, 'Câu nháp.');

        $response = $this->actingAs($this->admin())
            ->post(route('admin.questions.bulk-status'), [
                'ids' => [$hopLe->id, $khongHopLe->id],
                'trangthai' => Cauhoi::TRANGTHAI_PUBLISHED,
            ]);

        $response->assertRedirect(route('admin.questions.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('bulk_skipped', function (array $boQua) use ($khongHopLe): bool {
            return count($boQua) === 1 && $boQua[0]['ma'] === $khongHopLe->ma;
        });

        $this->assertSame(Cauhoi::TRANGTHAI_PUBLISHED, $hopLe->refresh()->trangthai);
        $this->assertSame(Cauhoi::TRANGTHAI_DRAFT, $khongHopLe->refresh()->trangthai);
    }

    public function test_khong_xoa_duoc_cau_hoi_dang_nam_trong_de_thi(): void
    {
        $cauhoi = $this->cauHoi();

        $dethi = Dethi::create([
            'ten' => 'Đề kiểm thử',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 3600,
            'trangthai' => 'draft',
        ]);

        Dethicauhoi::create([
            'dethi_id' => $dethi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'diem' => 1,
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.questions.destroy', $cauhoi))
            ->assertRedirect(route('admin.questions.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('cauhoi', ['id' => $cauhoi->id]);
    }

    public function test_khong_xoa_cau_hoi_da_xuat_ban(): void
    {
        $cauhoi = $this->cauHoi(Cauhoi::TRANGTHAI_PUBLISHED, 1);

        $this->actingAs($this->admin())
            ->delete(route('admin.questions.destroy', $cauhoi))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('cauhoi', ['id' => $cauhoi->id]);
    }

    public function test_xoa_duoc_cau_hoi_nhap_chua_nam_trong_de(): void
    {
        $cauhoi = $this->cauHoi(Cauhoi::TRANGTHAI_DRAFT, 1);

        $this->actingAs($this->admin())
            ->delete(route('admin.questions.destroy', $cauhoi))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('cauhoi', ['id' => $cauhoi->id]);
        $this->assertDatabaseMissing('luachon', ['cauhoi_id' => $cauhoi->id]);
    }

    public function test_ma_cau_hoi_tu_sinh_va_khong_doi_khi_cap_nhat(): void
    {
        $cauhoi = $this->cauHoi();

        $this->assertSame('q-'.str_pad((string) $cauhoi->id, 6, '0', STR_PAD_LEFT), $cauhoi->ma);

        $cauhoi->update(['noidung' => 'Nội dung đã sửa.']);

        $this->assertSame(
            'q-'.str_pad((string) $cauhoi->id, 6, '0', STR_PAD_LEFT),
            $cauhoi->refresh()->ma
        );
    }

    public function test_nguoi_khong_phai_admin_khong_vao_duoc_ngan_hang(): void
    {
        $hocVien = Nguoidung::create([
            'hoten' => 'Học viên',
            'email' => 'hocvien-bank@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->actingAs($hocVien)
            ->get(route('admin.questions.index'))
            ->assertForbidden();
    }
}
