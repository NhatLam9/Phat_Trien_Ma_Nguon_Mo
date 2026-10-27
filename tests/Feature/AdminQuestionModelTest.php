<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\CauhoiDoc;
use App\Models\CauhoiNghe;
use App\Models\Danhmuc;
use App\Models\Dapan;
use App\Models\Loaicauhoi;
use App\Models\Luachon;
use App\Models\LuachonDoc;
use App\Models\LuachonNghe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiểm tra model câu hỏi của TV3 (Phi Phong).
 */
class AdminQuestionModelTest extends TestCase
{
    use RefreshDatabase;

    private function taoLoaiCauHoi(string $ma = 'read_aloud'): Loaicauhoi
    {
        return Loaicauhoi::create([
            'ma' => $ma,
            'ten' => 'Đọc to',
            'kynang' => 'speaking',
            'cachcham' => 'ai',
            'thoigian' => 60,
        ]);
    }

    public function test_tao_chu_de_va_cau_hoi_trac_nghiem(): void
    {
        $danhmuc = Danhmuc::create(['ten' => 'Khoa học', 'mota' => 'Chủ đề khoa học']);
        $loai = $this->taoLoaiCauHoi();

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'danhmuc_id' => $danhmuc->id,
            'noidung' => 'Chọn đáp án đúng',
            'dokho' => 5,
            'diem' => 2,
        ]);

        $dung = Luachon::create([
            'cauhoi_id' => $cauhoi->id,
            'noidung' => 'Đáp án đúng',
            'ladung' => true,
            'thutu' => 1,
        ]);

        Luachon::create([
            'cauhoi_id' => $cauhoi->id,
            'noidung' => 'Đáp án sai',
            'ladung' => false,
            'thutu' => 2,
        ]);

        $cauhoi->refresh();

        $this->assertSame('draft', $cauhoi->trangthai);
        $this->assertSame('2.00', $cauhoi->diem);
        $this->assertNotNull($cauhoi->ngaytao);
        $this->assertNotNull($cauhoi->ngaycapnhat);

        $this->assertSame($loai->id, $cauhoi->loai->id);
        $this->assertSame($danhmuc->id, $cauhoi->danhmuc->id);
        $this->assertCount(2, $cauhoi->luachon);
        $this->assertTrue($dung->ladung);
    }

    public function test_cau_hoi_doc_co_cau_hoi_con_va_lua_chon(): void
    {
        $loai = $this->taoLoaiCauHoi('interactive_reading');

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Đọc đoạn văn và trả lời',
            'dokho' => 6,
        ]);

        $cauHoiCon = CauhoiDoc::create([
            'cauhoi_id' => $cauhoi->id,
            'loai' => 'complete_sentence',
            'noidung' => 'Điền từ còn thiếu',
            'dapandung' => 'however',
            'thutu' => 1,
        ]);

        LuachonDoc::create([
            'cauhoi_doc_id' => $cauHoiCon->id,
            'noidung' => 'however',
            'ladung' => true,
            'thutu' => 1,
        ]);

        $this->assertCount(1, $cauhoi->cauhoiDoc);
        $this->assertCount(1, $cauHoiCon->luachonDoc);
        $this->assertTrue($cauHoiCon->luachonDoc->first()->ladung);
    }

    public function test_cau_hoi_nghe_co_cau_hoi_con_va_lua_chon(): void
    {
        $loai = $this->taoLoaiCauHoi('interactive_listening');

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Nghe hội thoại và trả lời',
            'audio' => 'audio/nghe-01.mp3',
            'dokho' => 7,
        ]);

        $cauHoiCon = CauhoiNghe::create([
            'cauhoi_id' => $cauhoi->id,
            'loai' => 'listen_and_respond',
            'noidung' => 'Người nói sẽ làm gì tiếp theo?',
            'thutu' => 1,
        ]);

        LuachonNghe::create([
            'cauhoi_nghe_id' => $cauHoiCon->id,
            'noidung' => 'Đi đến thư viện',
            'ladung' => true,
            'thutu' => 1,
        ]);

        $this->assertCount(1, $cauhoi->cauhoiNghe);
        $this->assertCount(1, $cauHoiCon->luachonNghe);
    }

    public function test_dap_an_tu_luan_luu_theo_cau_hoi(): void
    {
        $loai = $this->taoLoaiCauHoi('write_about_photo');

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Viết mô tả bức ảnh',
            'dokho' => 4,
        ]);

        Dapan::create([
            'cauhoi_id' => $cauhoi->id,
            'noidung' => 'Bức ảnh có hai người đang đi bộ trong công viên.',
            'ladung' => true,
            'thutu' => 1,
        ]);

        $this->assertCount(1, $cauhoi->dapan);
    }

    public function test_xoa_cau_hoi_thi_xoa_lua_chon_theo(): void
    {
        $loai = $this->taoLoaiCauHoi('cascade_test');

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi kiểm tra cascade',
        ]);

        Luachon::create([
            'cauhoi_id' => $cauhoi->id,
            'noidung' => 'Lựa chọn',
        ]);

        $this->assertDatabaseHas('luachon', ['cauhoi_id' => $cauhoi->id]);

        $cauhoi->delete();

        $this->assertDatabaseMissing('luachon', ['cauhoi_id' => $cauhoi->id]);
    }

    public function test_loc_cau_hoi_da_xuat_ban(): void
    {
        $loai = $this->taoLoaiCauHoi('published_test');

        Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi nháp',
            'trangthai' => 'draft',
        ]);

        Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Câu hỏi đã xuất bản',
            'trangthai' => 'published',
        ]);

        $this->assertCount(1, Cauhoi::published()->get());
    }
}
