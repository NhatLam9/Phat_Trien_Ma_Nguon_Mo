<?php

namespace Tests\Feature;

use App\Jobs\ProcessAIGradingJob;
use App\Models\Aisudung;
use App\Models\Cauhoi;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use App\Models\Traloi;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AIRetryAndPendingTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $user;
    protected Nguoidung $otherUser;
    protected Traloi $traloi;
    protected Cauhoi $cauhoi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Nguoidung::create([
            'hoten' => 'Retry Student',
            'email' => 'retry@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->otherUser = Nguoidung::create([
            'hoten' => 'Other Student',
            'email' => 'other@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $lanThi = Lanthi::create([
            'nguoidung_id' => $this->user->id,
            'trangthai' => 'practice',
        ]);

        $loai = Loaicauhoi::create([
            'ma' => 'read_then_speak',
            'ten' => 'Read then Speak',
            'kynang' => 'speaking',
            'cachcham' => 'ai',
        ]);

        $this->cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Speak about an exciting journey you took.',
        ]);

        $lanThiCauHoi = Lanthicauhoi::create([
            'lanthi_id' => $lanThi->id,
            'cauhoi_id' => $this->cauhoi->id,
            'thutu' => 1,
            'datraloi' => true,
        ]);

        $this->traloi = Traloi::create([
            'lanthicauhoi_id' => $lanThiCauHoi->id,
            'noidung' => null,
            'audio' => 'audio/sample.mp3',
            'trangthai_ai' => 'pending',
            'luot_cham' => 1,
            'ngay_tru_luot' => today()->toDateString(),
            'thoidiem_cham' => now(),
        ]);
    }

    public function test_pending_status_view_contains_meta_refresh(): void
    {
        $response = $this->actingAs($this->user)->get("/practice/ai/status/{$this->traloi->id}");

        $response->assertOk();
        $response->assertSee('<meta http-equiv="refresh" content="5">', false);
        $response->assertSee('Hệ thống AI đang chấm bài của bạn');
    }

    public function test_pending_over_five_minutes_automatically_marks_failed_and_refunds(): void
    {
        // Trừ 1 lượt
        app(AIService::class)->checkAndDeductLimit($this->user->id);
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Giả lập pending đã qua 6 phút trước
        $this->traloi->update([
            'thoidiem_cham' => now()->subMinutes(6),
        ]);

        // Người dùng tải lại trang status
        $response = $this->actingAs($this->user)->get("/practice/ai/status/{$this->traloi->id}");

        $response->assertOk();
        // Không còn refresh nữa
        $response->assertDontSee('<meta http-equiv="refresh" content="5">', false);

        // Bản ghi chuyển sang failed và đã được hoàn lượt
        $this->assertEquals('failed', $this->traloi->fresh()->trangthai_ai);
        $this->assertEquals(0, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_double_retry_clicks_only_deducts_quota_once(): void
    {
        Queue::fake();

        // Đặt trạng thái ban đầu là failed
        $this->traloi->update([
            'trangthai_ai' => 'failed',
            'nhanxet_ai' => 'API timeout',
        ]);

        // Tab 1 bấm retry -> thành công, chuyển sang pending và trừ 1 lượt
        $response1 = $this->actingAs($this->user)->post("/practice/ai/retry/{$this->traloi->id}");
        $response1->assertRedirect("/practice/ai/status/{$this->traloi->id}");

        $this->assertEquals('pending', $this->traloi->fresh()->trangthai_ai);
        $this->assertEquals(2, $this->traloi->fresh()->luot_cham);
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Tab 2 bấm retry ngay sau đó (bản ghi đang là pending, không còn là failed)
        $response2 = $this->actingAs($this->user)->post("/practice/ai/retry/{$this->traloi->id}");
        $response2->assertRedirect("/practice/ai/status/{$this->traloi->id}");

        // Lượt KHÔNG bị trừ thêm lần 2
        $this->assertEquals(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
        $this->assertEquals(2, $this->traloi->fresh()->luot_cham);

        // Job chỉ được dispatch đúng 1 lần
        Queue::assertPushed(ProcessAIGradingJob::class, 1);
    }

    public function test_user_cannot_view_or_retry_other_users_submission(): void
    {
        // otherUser cố truy cập bài làm của user -> 403 Forbidden
        $responseView = $this->actingAs($this->otherUser)->get("/practice/ai/status/{$this->traloi->id}");
        $responseView->assertForbidden();

        $responseRetry = $this->actingAs($this->otherUser)->post("/practice/ai/retry/{$this->traloi->id}");
        $responseRetry->assertForbidden();
    }

    public function test_done_speaking_status_view_contains_mandatory_pronunciation_note(): void
    {
        $this->traloi->update([
            'trangthai_ai' => 'done',
            'diem_ai' => 120,
            'nhanxet_ai' => 'Phát âm tốt, bài nói lưu loát.',
        ]);

        $response = $this->actingAs($this->user)->get("/practice/ai/status/{$this->traloi->id}");

        $response->assertOk();
        $response->assertDontSee('<meta http-equiv="refresh" content="5">', false);
        // Kiểm tra dòng ghi chú bắt buộc theo mục 1 & 4
        $response->assertSee('Điểm số dựa trên bản chép lời tự động, không đánh giá hoàn toàn độ chính xác của phát âm');
    }
}

