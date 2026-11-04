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

        $questions = [];
        foreach (glob(database_path('seeders/data/extended_questions*.json')) ?: [] as $file) {
            $data = json_decode(file_get_contents($file), true);
            $questions = array_merge($questions, $data['questions']);
        }
        $prepared = app(QuestionImportService::class)->prepare(
            json_encode(['questions' => $questions], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->assertSame([], $prepared['errors']);
        $this->assertSame([], $prepared['warnings']);
        $this->assertCount(229, $prepared['questions']);
        $this->assertSame(40, $prepared['summary']['reading_children'] + $prepared['summary']['listening_children']);
    }

    public function test_seed_them_cau_hoi_va_chay_lai_khong_tao_trung(): void
    {
        $this->taoAdmin();
        $this->seed(AdminCatalogSeeder::class);
        $this->seed(OperationalQuestionBankSeeder::class);

        $truoc = Cauhoi::count();

        $this->seed(ExtendedQuestionBankSeeder::class);

        $this->assertSame($truoc + 229, Cauhoi::count());
        $this->assertDatabaseHas('cauhoi', ['ma' => 'ext-ras-001', 'trangthai' => 'published']);
        $this->assertDatabaseHas('cauhoi', ['ma' => 'ext2-ras-080']);
        $this->assertDatabaseHas('cauhoi', ['ma' => 'ext-rts-002']);

        $doc = Cauhoi::query()->where('ma', 'ext-ir-001')->firstOrFail();
        $nghe = Cauhoi::query()->where('ma', 'ext-il-001')->firstOrFail();
        $doc2 = Cauhoi::query()->where('ma', 'ext2-ir-006')->firstOrFail();
        $nghe2 = Cauhoi::query()->where('ma', 'ext2-il-006')->firstOrFail();

        $this->assertSame(3, $doc->cauhoiDoc()->count());
        $this->assertSame(2, $nghe->cauhoiNghe()->count());
        $this->assertSame(3, $doc2->cauhoiDoc()->count());
        $this->assertSame(2, $nghe2->cauhoiNghe()->count());

        $this->seed(ExtendedQuestionBankSeeder::class);

        $this->assertSame($truoc + 229, Cauhoi::count());
    }
}
