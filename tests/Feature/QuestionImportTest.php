<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QuestionImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Admin Import',
            'email' => 'admin-import@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);
    }

    private function seedTypes(): void
    {
        foreach ([
            ['read_and_select', 'Read and Select', 'reading'],
            ['interactive_reading', 'Interactive Reading', 'reading'],
            ['interactive_listening', 'Interactive Listening', 'listening'],
        ] as [$ma, $ten, $kynang]) {
            Loaicauhoi::create([
                'ma' => $ma,
                'ten' => $ten,
                'kynang' => $kynang,
                'cachcham' => 'auto',
                'thoigian' => 60,
                'mota' => null,
                'is_active' => true,
            ]);
        }
    }

    private function templateFile(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'import-cau-hoi-template.json',
            file_get_contents(public_path('downloads/import-cau-hoi-template.json'))
        );
    }

    public function test_admin_co_the_preview_va_import_json(): void
    {
        $this->seedTypes();
        $admin = $this->admin();

        $preview = $this->actingAs($admin)->post(
            route('admin.questions.import.preview'),
            ['file' => $this->templateFile()]
        );

        $preview->assertOk();
        $preview->assertSee('Dữ liệu hợp lệ');

        $result = $this->post(route('admin.questions.import.store'));

        $result->assertOk();
        $result->assertSee('Nhập hoàn tất');

        $this->assertDatabaseCount('cauhoi', 3);
        $this->assertDatabaseCount('luachon', 4);
        $this->assertDatabaseCount('luachon_doc', 3);
        $this->assertDatabaseCount('luachon_nghe', 3);
        $this->assertDatabaseCount('cauhoi_doc', 2);
        $this->assertDatabaseCount('cauhoi_nghe', 2);
        $this->assertDatabaseCount('dethi', 1);
        $this->assertDatabaseCount('dethicauhoi', 3);

        $this->assertDatabaseHas('dethi', [
            'ten' => 'DET Import Demo',
            'trangthai' => 'draft',
        ]);
        $this->assertDatabaseHas('cauhoi', [
            'noidung' => 'The museum is free on Sundays, so many families ___ there.',
            'trangthai' => 'draft',
        ]);
    }

    public function test_json_rong_bi_tu_choi(): void
    {
        $this->seedTypes();
        $admin = $this->admin();

        $file = UploadedFile::fake()->createWithContent(
            'empty.json',
            json_encode(['questions' => []])
        );

        $response = $this->actingAs($admin)->post(
            route('admin.questions.import.preview'),
            ['file' => $file]
        );

        $response->assertOk();
        $response->assertSee('không được rỗng');
        $this->assertDatabaseCount('cauhoi', 0);
    }

    public function test_template_download_duoc(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.questions.import.template'))
            ->assertOk()
            ->assertHeader('content-type', 'application/json; charset=UTF-8');
    }

    public function test_import_ghi_lich_su_va_giu_ma_on_dinh_khi_import_lai(): void
    {
        $this->seedTypes();
        $admin = $this->admin();

        $this->actingAs($admin)->post(
            route('admin.questions.import.preview'),
            ['file' => $this->templateFile()]
        )->assertOk();

        $this->post(route('admin.questions.import.store'))->assertOk();

        $this->assertDatabaseCount('import_batches', 1);
        $this->assertDatabaseHas('import_batches', [
            'ten_file' => 'import-cau-hoi-template.json',
            'nguoitao' => $admin->id,
            'tao_moi' => 3,
            'cap_nhat' => 0,
            'trangthai' => 'success',
        ]);

        $goc = Cauhoi::query()->where('ma', 'reading-001')->firstOrFail();

        // Import lại chính file đó: phải cập nhật 3 câu cũ, không tạo thêm.
        $this->post(
            route('admin.questions.import.preview'),
            ['file' => $this->templateFile()]
        )->assertOk();

        $result = $this->post(route('admin.questions.import.store'));

        $result->assertOk();
        $result->assertSee('Nhập hoàn tất');

        $this->assertDatabaseCount('cauhoi', 3);
        $this->assertDatabaseCount('import_batches', 2);

        $this->assertDatabaseHas('import_batches', [
            'tao_moi' => 0,
            'cap_nhat' => 3,
            'trangthai' => 'success',
        ]);

        $sauKhiImport = Cauhoi::query()->where('ma', 'reading-001')->firstOrFail();

        $this->assertSame($goc->id, $sauKhiImport->id);
        $this->assertSame($goc->ma, $sauKhiImport->ma);
    }

    public function test_import_cung_ma_cap_nhat_noi_dung_thay_vi_tao_trung(): void
    {
        $this->seedTypes();
        $admin = $this->admin();

        $json = json_encode([
            'questions' => [[
                'ma' => 'reading-001',
                'loai' => 'read_and_select',
                'noidung' => 'Nội dung ban đầu ___ .',
                'luachon' => [
                    ['noidung' => 'A', 'ladung' => true],
                    ['noidung' => 'B', 'ladung' => false],
                ],
            ]],
        ], JSON_UNESCAPED_UNICODE);

        $this->actingAs($admin)->post(
            route('admin.questions.import.preview'),
            ['file' => UploadedFile::fake()->createWithContent('lan-1.json', $json)]
        )->assertOk();
        $this->post(route('admin.questions.import.store'))->assertOk();

        $cauHoi = Cauhoi::query()->where('ma', 'reading-001')->firstOrFail();

        $jsonSua = json_encode([
            'questions' => [[
                'ma' => 'reading-001',
                'loai' => 'read_and_select',
                'noidung' => 'Nội dung đã sửa ___ .',
                'luachon' => [
                    ['noidung' => 'A', 'ladung' => true],
                    ['noidung' => 'B', 'ladung' => false],
                ],
            ]],
        ], JSON_UNESCAPED_UNICODE);

        $this->actingAs($admin)->post(
            route('admin.questions.import.preview'),
            ['file' => UploadedFile::fake()->createWithContent('lan-2.json', $jsonSua)]
        )->assertOk();
        $this->post(route('admin.questions.import.store'))->assertOk();

        $this->assertDatabaseCount('cauhoi', 1);
        $this->assertSame($cauHoi->id, Cauhoi::query()->where('ma', 'reading-001')->value('id'));
        $this->assertDatabaseHas('cauhoi', [
            'ma' => 'reading-001',
            'noidung' => 'Nội dung đã sửa ___ .',
        ]);
    }

    public function test_nhap_cau_hoi_la_tab_trong_ngan_hang_khong_nam_o_menu(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.questions.index'))
            ->assertOk()
            ->assertSee('Danh sách câu hỏi')
            ->assertSee('Nhập câu hỏi')
            ->assertDontSee('Import JSON');

        $this->actingAs($admin)
            ->get(route('admin.questions.import.create'))
            ->assertOk()
            ->assertSee('Nhập câu hỏi từ JSON')
            ->assertSee('Danh sách câu hỏi');
    }
}
