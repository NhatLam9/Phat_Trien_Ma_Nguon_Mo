<?php

namespace Tests\Feature;

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
        $result->assertSee('Import hoàn tất');

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
}
