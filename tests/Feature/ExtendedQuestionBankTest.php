<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\Nguoidung;
use App\Services\QuestionImportService;
use Database\Seeders\AdminCatalogSeeder;
use Database\Seeders\ExtendedQuestionBankSeeder;
use Database\Seeders\OperationalQuestionBankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Kiểm tra bộ câu hỏi mở rộng (extended_questions.json).
 *
 * Mục tiêu: dữ liệu luôn hợp lệ với bộ kiểm tra của module import,
 * seed được vào database và chạy lại không tạo câu trùng.
 */
class ExtendedQuestionBankTest extends TestCase
{
    use RefreshDatabase;

    private function taoAdmin(): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Admin Ngân hàng',
            'email' => 'admin-qbank-ext@example.com',
            'matkhau' => Hash::make('password'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);
    }

    public function test_du_lieu_mo_rong_hop_le_voi_bo_kiem_tra_import(): void
    {
        $this->seed(AdminCatalogSeeder::class);
        $this->seed(OperationalQuestionBankSeeder::class);

        $json = file_get_contents(database_path('seeders/data/extended_questions.json'));
        $prepared = app(QuestionImportService::class)->prepare($json);

        $this->assertSame([], $prepared['errors']);
        $this->assertSame([], $prepared['warnings']);
        $this->assertCount(23, $prepared['questions']);
        $this->assertSame(10, $prepared['summary']['reading_children'] + $prepared['summary']['listening_children']);
    }

    public function test_seed_them_cau_hoi_va_chay_lai_khong_tao_trung(): void
    {
        $this->taoAdmin();
        $this->seed(AdminCatalogSeeder::class);
        $this->seed(OperationalQuestionBankSeeder::class);

        $truoc = Cauhoi::count();

        $this->seed(ExtendedQuestionBankSeeder::class);

        $this->assertSame($truoc + 23, Cauhoi::count());
        $this->assertDatabaseHas('cauhoi', ['ma' => 'ext-ras-001', 'trangthai' => 'published']);
        $this->assertDatabaseHas('cauhoi', ['ma' => 'ext-rts-002']);

        $doc = Cauhoi::query()->where('ma', 'ext-ir-001')->firstOrFail();
        $nghe = Cauhoi::query()->where('ma', 'ext-il-001')->firstOrFail();

        $this->assertSame(3, $doc->cauhoiDoc()->count());
        $this->assertSame(2, $nghe->cauhoiNghe()->count());

        $this->seed(ExtendedQuestionBankSeeder::class);

        $this->assertSame($truoc + 23, Cauhoi::count());
    }
}
