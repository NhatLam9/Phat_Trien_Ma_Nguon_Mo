<?php

namespace Tests\Feature;

use App\Exceptions\AiLimitExceededException;
use App\Exceptions\AiTemporaryException;
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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AISubsystemTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $user;

    protected Nguoidung $otherUser;

    protected Lanthi $lanThi;

    protected Traloi $traloi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Nguoidung::create([
            'hoten' => 'AI Subsystem Student',
            'email' => 'ai-subsystem@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->otherUser = Nguoidung::create([
            'hoten' => 'Other AI Student',
            'email' => 'other-ai@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->lanThi = Lanthi::create([
            'nguoidung_id' => $this->user->id,
            'trangthai' => 'practice',
        ]);

        $loai = Loaicauhoi::create([
            'ma' => 'write_about_photo',
            'ten' => 'Write about the photo',
            'kynang' => 'writing',
            'cachcham' => 'ai',
        ]);

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Describe the picture in at least 50 words.',
        ]);

        $lanThiCauHoi = Lanthicauhoi::create([
            'lanthi_id' => $this->lanThi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'datraloi' => true,
        ]);

        $this->traloi = Traloi::create([
            'lanthicauhoi_id' => $lanThiCauHoi->id,
            'noidung' => 'A short answer.',
            'audio' => null,
            'trangthai_ai' => 'failed',
            'luot_cham' => 1,
            'ngay_tru_luot' => today()->toDateString(),
            'thoidiem_cham' => now(),
        ]);
    }

    public function test_grammar_view_renders_nested_result_array_without_array_to_string(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'ai_result' => [
                    'corrected' => 'She goes to school every day.',
                    'error_count' => 1,
                    'errors' => [
                        [
                            'original' => 'go',
                            'replacement' => 'goes',
                            'explanation' => 'Chia thì hiện tại đơn với ngôi thứ ba.',
                        ],
                    ],
                    'overall_comment' => 'Bài viết khá tốt, chỉ cần chú ý chia động từ.',
                ],
            ])
            ->get('/tutor/grammar');

        $response->assertOk();
        $response->assertSee('She goes to school every day.');
        $response->assertSee('go');
        $response->assertSee('goes');
        $response->assertSee('Chia thì hiện tại đơn với ngôi thứ ba.');
        $response->assertSee('Bài viết khá tốt, chỉ cần chú ý chia động từ.');
        $response->assertDontSee('Array');
    }

    public function test_audio_route_blocks_other_users_and_streams_for_owner(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('audio/private-sample.mp3', 'fake-audio-bytes');

        $this->traloi->update([
            'audio' => 'audio/private-sample.mp3',
            'trangthai_ai' => 'done',
            'diem_ai' => 120,
            'nhanxet_ai' => 'Tốt.',
        ]);

        $this->actingAs($this->otherUser)
            ->get("/practice/ai/audio/{$this->traloi->id}")
            ->assertForbidden();

        $this->actingAs($this->user)
            ->get("/practice/ai/audio/{$this->traloi->id}")
            ->assertOk();
    }

    public function test_retry_on_done_submission_does_not_deduct_quota_or_change_status(): void
    {
        Queue::fake();

        $this->traloi->update([
            'trangthai_ai' => 'done',
            'diem_ai' => 130,
            'nhanxet_ai' => 'Bài tốt.',
        ]);

        $response = $this->actingAs($this->user)
            ->post("/practice/ai/retry/{$this->traloi->id}");

        $response->assertRedirect("/practice/ai/status/{$this->traloi->id}");

        // Không trừ lượt, không đổi trạng thái, không đẩy job.
        $this->assertFalse(Aisudung::where('nguoidung_id', $this->user->id)->exists());
        $this->assertSame('done', $this->traloi->fresh()->trangthai_ai);
        $this->assertSame(1, $this->traloi->fresh()->luot_cham);
        Queue::assertNothingPushed();
    }

    public function test_retry_when_limit_exceeded_keeps_failed_status_and_does_not_deduct(): void
    {
        Queue::fake();
        Config::set('ai.daily_limit', 1);

        app(AIService::class)->checkAndDeductLimit($this->user->id);
        $this->assertSame(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        $this->traloi->update([
            'trangthai_ai' => 'failed',
            'nhanxet_ai' => 'API timeout',
            'luot_cham' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/practice/ai/retry/{$this->traloi->id}");

        $response->assertRedirect("/practice/ai/status/{$this->traloi->id}");
        $response->assertSessionHasErrors('ai_limit');

        // Trạng thái và lượt chấm giữ nguyên, hạn mức không bị trừ thêm.
        $this->assertSame('failed', $this->traloi->fresh()->trangthai_ai);
        $this->assertSame(2, $this->traloi->fresh()->luot_cham);
        $this->assertSame(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
        Queue::assertNothingPushed();
    }

    public function test_missing_gemini_api_key_returns_clear_failure_instead_of_fake_score(): void
    {
        Config::set('ai.provider', 'gemini');
        Config::set('ai.gemini.api_key', '');

        $result = app(AIService::class)->gradeWriting(
            'Describe your hometown.',
            'My hometown is a small city near the sea.',
            null,
            $this->user->id
        );

        $this->assertSame('failed', $result['status']);
        $this->assertStringContainsString('GEMINI_API_KEY', $result['message']);
        $this->assertArrayNotHasKey('score', $result);
        $this->assertDatabaseHas('ailog', [
            'nguoidung_id' => $this->user->id,
            'loai' => 'writing',
            'trangthai' => 'failed',
        ]);
    }

    public function test_missing_openai_api_key_returns_clear_failure_instead_of_fake_score(): void
    {
        Config::set('ai.provider', 'openai');
        Config::set('ai.openai.api_key', '');

        $result = app(AIService::class)->gradeWriting(
            'Describe your hometown.',
            'My hometown is a small city near the sea.',
            null,
            $this->user->id
        );

        $this->assertSame('failed', $result['status']);
        $this->assertStringContainsString('OPENAI_API_KEY', $result['message']);
        $this->assertArrayNotHasKey('score', $result);
        $this->assertDatabaseHas('ailog', [
            'nguoidung_id' => $this->user->id,
            'loai' => 'writing',
            'trangthai' => 'failed',
        ]);
    }

    public function test_api_failure_logs_failed_status_with_http_code(): void
    {
        Config::set('ai.provider', 'gemini');
        Config::set('ai.gemini.api_key', 'test-key');

        Http::fake([
            '*generateContent*' => Http::response('Server Error', 500),
        ]);

        try {
            app(AIService::class)->gradeWriting(
                'Describe your hometown.',
                'My hometown is a small city near the sea.',
                null,
                $this->user->id
            );
            $this->fail('Lỗi 500 phải ném AiTemporaryException để job retry.');
        } catch (AiTemporaryException $e) {
            // Mong đợi: lỗi tạm thời để queue thử lại.
        }

        $this->assertDatabaseHas('ailog', [
            'nguoidung_id' => $this->user->id,
            'loai' => 'writing',
            'trangthai' => 'failed',
            'http_code' => 500,
        ]);
    }

    public function test_bulk_limit_deducts_multiple_and_blocks_when_insufficient(): void
    {
        Config::set('ai.daily_limit', 5);

        $aiService = app(AIService::class);
        $aiService->checkAndDeductLimitBulk($this->user->id, 3);

        $this->assertSame(3, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        try {
            $aiService->checkAndDeductLimitBulk($this->user->id, 3);
            $this->fail('Không đủ 3 lượt còn lại thì phải ném AiLimitExceededException.');
        } catch (AiLimitExceededException $e) {
            // Mong đợi: chặn và không trừ đồng nào.
        }

        $this->assertSame(3, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));

        // Người dùng mới chưa có dòng hạn mức: yêu cầu vượt trần cũng không được tạo dòng.
        try {
            $aiService->checkAndDeductLimitBulk($this->otherUser->id, 6);
            $this->fail('Yêu cầu vượt trần phải ném AiLimitExceededException.');
        } catch (AiLimitExceededException $e) {
            // Mong đợi.
        }

        $this->assertFalse(Aisudung::where('nguoidung_id', $this->otherUser->id)->exists());
    }

    public function test_transcript_too_long_hides_retry_button_and_retry_is_blocked(): void
    {
        Queue::fake();

        $this->traloi->update([
            'trangthai_ai' => 'failed',
            'nhanxet_ai' => AIService::transcriptTooLongFeedback(),
            'luot_cham' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/practice/ai/status/{$this->traloi->id}");

        $response->assertOk();
        $response->assertSee('Bài nói của bạn quá dài so với giới hạn.');
        // Marker kỹ thuật không được lộ ra giao diện.
        $response->assertDontSee('[TRANSCRIPT_TOO_LONG]', false);
        // Nút chấm lại phải bị ẩn.
        $response->assertDontSee('🔄 Chấm lại bài này');

        // Gọi thẳng route retry cũng bị chặn, không trừ lượt.
        $retry = $this->actingAs($this->user)
            ->post("/practice/ai/retry/{$this->traloi->id}");

        $retry->assertRedirect("/practice/ai/status/{$this->traloi->id}");
        $retry->assertSessionHasErrors('ai_limit');
        $this->assertSame('failed', $this->traloi->fresh()->trangthai_ai);
        $this->assertSame(1, $this->traloi->fresh()->luot_cham);
        $this->assertFalse(Aisudung::where('nguoidung_id', $this->user->id)->exists());
        Queue::assertNothingPushed();
    }

    public function test_retry_dispatches_job_and_deducts_exactly_one_attempt(): void
    {
        Queue::fake();

        $this->traloi->update([
            'trangthai_ai' => 'failed',
            'nhanxet_ai' => 'Lỗi tạm thời.',
            'luot_cham' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/practice/ai/retry/{$this->traloi->id}");

        $response->assertRedirect("/practice/ai/status/{$this->traloi->id}");
        $response->assertSessionHas('success');

        $this->assertSame('pending', $this->traloi->fresh()->trangthai_ai);
        $this->assertSame(2, $this->traloi->fresh()->luot_cham);
        $this->assertSame(1, Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
        Queue::assertPushed(ProcessAIGradingJob::class, 1);
    }
}
