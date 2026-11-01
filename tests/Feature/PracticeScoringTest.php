<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ScoringService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PracticeScoringTest extends TestCase
{
    use RefreshDatabase;

    protected ScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScoringService();

        if (Schema::hasTable('loaicauhoi')) {
            DB::table('loaicauhoi')->insertOrIgnore([
                'id' => 1,
                'ma' => 'multiple_choice',
                'ten' => 'Multiple Choice',
                'kynang' => 'reading',
                'cachcham' => 'auto',
                'is_active' => 1,
            ]);
        }

        if (Schema::hasTable('cauhoi')) {
            DB::table('cauhoi')->insertOrIgnore([
                'id' => 50,
                'loai_id' => 1,
                'noidung' => 'Sample Question 50',
                'dokho' => 5,
                'diem' => 10,
                'trangthai' => 'published',
            ]);
            DB::table('cauhoi')->insertOrIgnore([
                'id' => 60,
                'loai_id' => 1,
                'noidung' => 'Sample Question 60',
                'dokho' => 5,
                'diem' => 10,
                'trangthai' => 'published',
            ]);
        }
    }

    /**
     * Test chấm tự động với dữ liệu nạp sẵn trong bộ nhớ (In-memory Object/Array)
     */
    public function test_score_auto_with_in_memory_question_and_choices(): void
    {
        $question = (object)[
            'id' => 10,
            'luachon' => [
                (object)['id' => 1, 'noidung' => 'Option A', 'ladung' => 0],
                (object)['id' => 2, 'noidung' => 'Option B', 'ladung' => 1],
                (object)['id' => 3, 'noidung' => 'Option C', 'ladung' => 0],
            ]
        ];

        // Lựa chọn đúng
        $this->assertTrue($this->service->scoreAuto($question, 2));

        // Lựa chọn sai
        $this->assertFalse($this->service->scoreAuto($question, 1));
        $this->assertFalse($this->service->scoreAuto($question, 3));
        $this->assertFalse($this->service->scoreAuto($question, 999));
    }

    /**
     * Test chấm tự động khi có bảng luachon trong CSDL
     */
    public function test_score_auto_with_database_luachon_table(): void
    {
        if (!Schema::hasTable('luachon')) {
            Schema::create('luachon', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cauhoi_id');
                $table->text('noidung');
                $table->tinyInteger('ladung')->default(0);
                $table->timestamps();
            });
        }

        DB::table('luachon')->insert([
            ['id' => 101, 'cauhoi_id' => 50, 'noidung' => 'Correct Answer', 'ladung' => 1],
            ['id' => 102, 'cauhoi_id' => 50, 'noidung' => 'Wrong Answer', 'ladung' => 0],
        ]);

        // Chọn ID đúng
        $this->assertTrue($this->service->scoreAuto(50, 101));

        // Chọn ID sai
        $this->assertFalse($this->service->scoreAuto(50, 102));

        // Mảng chứa luachon_id
        $this->assertTrue($this->service->scoreAuto(50, ['luachon_id' => 101]));
        $this->assertFalse($this->service->scoreAuto(50, ['luachon_id' => 102]));
    }

    /**
     * Test chấm tự động cho dạng câu hỏi điền từ / văn bản với bảng dap_an
     */
    public function test_score_auto_with_database_dap_an_table(): void
    {
        if (!Schema::hasTable('dap_an')) {
            Schema::create('dap_an', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cauhoi_id');
                $table->text('noidung');
                $table->tinyInteger('ladung')->default(1);
                $table->timestamps();
            });
        }

        DB::table('dap_an')->insert([
            ['id' => 1, 'cauhoi_id' => 60, 'noidung' => 'literature', 'ladung' => 1],
        ]);

        // Trùng khớp hoàn toàn
        $this->assertTrue($this->service->scoreAuto(60, 'literature'));

        // Trùng khớp không phân biệt hoa thường và khoảng trắng
        $this->assertTrue($this->service->scoreAuto(60, '  Literature  '));

        // Sai chính tả
        $this->assertFalse($this->service->scoreAuto(60, 'literatures'));
        $this->assertFalse($this->service->scoreAuto(60, 'science'));
    }

    /**
     * Test hàm tính điểm calculateResult
     */
    public function test_calculate_result_returns_score_and_comments(): void
    {
        $question = (object)[
            'id' => 70,
            'luachon' => [
                (object)['id' => 5, 'noidung' => 'Yes', 'ladung' => 1],
                (object)['id' => 6, 'noidung' => 'No', 'ladung' => 0],
            ]
        ];

        // Đúng -> đạt trọn 5 điểm
        $correctResult = $this->service->calculateResult($question, 5, 5.0);
        $this->assertTrue($correctResult['is_correct']);
        $this->assertEquals(5.0, $correctResult['diem']);
        $this->assertStringContainsString('chính xác', $correctResult['nhanxet']);

        // Sai -> 0 điểm
        $wrongResult = $this->service->calculateResult($question, 6, 5.0);
        $this->assertFalse($wrongResult['is_correct']);
        $this->assertEquals(0.0, $wrongResult['diem']);

        // Trống hoặc null
        $emptyResult = $this->service->calculateResult($question, null);
        $this->assertFalse($emptyResult['is_correct']);
        $this->assertEquals(0.0, $emptyResult['diem']);
    }
}

