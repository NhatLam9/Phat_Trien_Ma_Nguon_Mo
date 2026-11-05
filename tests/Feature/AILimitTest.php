<?php

namespace Tests\Feature;

use App\Exceptions\AiLimitExceededException;
use App\Models\Aisudung;
use App\Models\Cauhoi;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use App\Models\Traloi;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AILimitTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $user;
    protected AIService $aiService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Nguoidung::create([
            'hoten' => 'Test AI Student',
            'email' => 'ai-student@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->aiService = app(AIService::class);
    }

    public function test_check_and_deduct_limit_creates_record_or_increments(): void
    {
        // Lần 1: chưa có record -> tạo mới solan = 1
        $usage1 = $this->aiService->checkAndDeductLimit($this->user->id);
        $this->assertEquals(1, $usage1->solan);
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Lần 2: tăng lên 2
        $usage2 = $this->aiService->checkAndDeductLimit($this->user->id);
        $this->assertEquals(2, $usage2->solan);
        $this->assertEquals(2, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_check_and_deduct_limit_throws_exception_when_limit_exceeded(): void
    {
        Config::set('ai.daily_limit', 3);

        // Dùng đủ 3 lượt
        $this->aiService->checkAndDeductLimit($this->user->id);
        $this->aiService->checkAndDeductLimit($this->user->id);
        $this->aiService->checkAndDeductLimit($this->user->id);

        // Lần thứ 4 phải ném ngoại lệ AiLimitExceededException
        $this->expectException(AiLimitExceededException::class);
        $this->aiService->checkAndDeductLimit($this->user->id);
    }

    public function test_refund_usage_decrements_and_never_below_zero(): void
    {
        // Trừ 2 lượt
        $this->aiService->checkAndDeductLimit($this->user->id);
        $this->aiService->checkAndDeductLimit($this->user->id);

        $today = today()->toDateString();
        $this->assertEquals(2, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Hoàn 1 lượt -> còn 1
        $refunded1 = $this->aiService->refundUsage($this->user->id, $today);
        $this->assertTrue($refunded1);
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Hoàn thêm 1 lượt -> về 0
        $refunded2 = $this->aiService->refundUsage($this->user->id, $today);
        $this->assertTrue($refunded2);
        $this->assertEquals(0, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Hoàn tiếp lần nữa -> không bị âm, trả về false
        $refunded3 = $this->aiService->refundUsage($this->user->id, $today);
        $this->assertFalse($refunded3);
        $this->assertEquals(0, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_mark_failed_and_refund_is_atomic_and_idempotent(): void
    {
        $today = today()->toDateString();

        // Trừ 1 lượt
        $this->aiService->checkAndDeductLimit($this->user->id);
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        $lanThi = Lanthi::create([
            'nguoidung_id' => $this->user->id,
            'trangthai' => 'practice',
        ]);

        $loai = Loaicauhoi::create([
            'ma' => 'write_about_photo',
            'ten' => 'Write About the Photo',
            'kynang' => 'writing',
            'cachcham' => 'ai',
        ]);

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Describe the photo.',
        ]);

        $lanThiCauHoi = Lanthicauhoi::create([
            'lanthi_id' => $lanThi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'datraloi' => true,
        ]);

        $traloi = Traloi::create([
            'lanthicauhoi_id' => $lanThiCauHoi->id,
            'noidung' => 'This is a sample answer.',
            'trangthai_ai' => 'pending',
            'luot_cham' => 1,
            'ngay_tru_luot' => $today,
            'thoidiem_cham' => now(),
        ]);

        // Lần 1: cập nhật pending -> failed và hoàn lượt thành công
        $success1 = $this->aiService->markFailedAndRefund($traloi->id, $this->user->id, $today, 1, 'Lỗi API');
        $this->assertTrue($success1);
        $this->assertEquals(0, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
        $this->assertEquals('failed', $traloi->fresh()->trangthai_ai);

        // Lần 2 (giả lập 2 tab hoặc 2 worker cùng gọi): dòng đã là failed -> không được hoàn lượt thêm lần nữa
        $success2 = $this->aiService->markFailedAndRefund($traloi->id, $this->user->id, $today, 1, 'Lỗi API lặp');
        $this->assertFalse($success2);
        // Số lần vẫn là 0, không bị âm
        $this->assertEquals(0, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }
}
