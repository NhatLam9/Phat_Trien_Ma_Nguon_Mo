<?php

namespace Tests\Feature;

use App\Jobs\ProcessAIGradingJob;
use App\Models\Cauhoi;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use App\Models\Traloi;
use App\Services\AIService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcessAIGradingJobTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $user;
    protected Traloi $traloi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Nguoidung::create([
            'hoten' => 'Job Test Student',
            'email' => 'job-test@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $lanThi = Lanthi::create([
            'nguoidung_id' => $this->user->id,
            'trangthai' => 'practice',
        ]);

        $loai = Loaicauhoi::create([
            'ma' => 'write_about_photo',
            'ten' => 'Write About Photo',
            'kynang' => 'writing',
            'cachcham' => 'ai',
        ]);

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Describe the picture in at least 50 words.',
        ]);

        $lanThiCauHoi = Lanthicauhoi::create([
            'lanthi_id' => $lanThi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'datraloi' => true,
        ]);

        $this->traloi = Traloi::create([
            'lanthicauhoi_id' => $lanThiCauHoi->id,
            'noidung' => 'The photograph depicts a bustling marketplace in the morning.',
            'trangthai_ai' => 'pending',
            'luot_cham' => 1,
            'ngay_tru_luot' => today()->toDateString(),
            'thoidiem_cham' => now(),
        ]);

        Config::set('ai.provider', 'gemini');
        Config::set('ai.gemini.api_key', 'test-key');
    }

    public function test_job_ignores_if_attempt_does_not_match(): void
    {
        // Giả lập attempt trong DB đã tăng lên 2 (do user bấm chấm lại)
        $this->traloi->update(['luot_cham' => 2]);

        // Job cũ mang attempt = 1 chạy
        $job = new ProcessAIGradingJob($this->traloi->id, $this->user->id, today()->toDateString(), 1);
        $job->handle(app(AIService::class));

        // Trạng thái vẫn là pending của lượt 2, không bị job cũ ghi đè
        $this->assertEquals('pending', $this->traloi->fresh()->trangthai_ai);
        $this->assertNull($this->traloi->fresh()->diem_ai);
    }

    public function test_job_ignores_if_status_is_already_done(): void
    {
        $this->traloi->update([
            'trangthai_ai' => 'done',
            'diem_ai' => 130,
        ]);

        $job = new ProcessAIGradingJob($this->traloi->id, $this->user->id, today()->toDateString(), 1);
        $job->handle(app(AIService::class));

        $this->assertEquals(130, $this->traloi->fresh()->diem_ai);
    }

    public function test_job_successful_grading_updates_done_conditionally(): void
    {
        Http::fake([
            '*generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'score' => 125,
                                        'feedback' => 'Tốt, câu văn rõ ràng.',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $job = new ProcessAIGradingJob($this->traloi->id, $this->user->id, today()->toDateString(), 1);
        $job->handle(app(AIService::class));

        $fresh = $this->traloi->fresh();
        $this->assertEquals('done', $fresh->trangthai_ai);
        $this->assertEquals(125, $fresh->diem_ai);
        $this->assertStringContainsString('Tốt, câu văn rõ ràng', $fresh->nhanxet_ai);
    }

    public function test_job_permanent_error_calls_mark_failed_and_refund(): void
    {
        app(AIService::class)->checkAndDeductLimit($this->user->id); // solan = 1

        // Mock AI trả về lỗi 400 (Bad Request - lỗi vĩnh viễn)
        Http::fake([
            '*generateContent*' => Http::response('Bad Request', 400),
        ]);

        $job = new ProcessAIGradingJob($this->traloi->id, $this->user->id, today()->toDateString(), 1);
        $job->handle(app(AIService::class));

        $fresh = $this->traloi->fresh();
        $this->assertEquals('failed', $fresh->trangthai_ai);
        // Kiểm tra đã hoàn lượt
        $this->assertEquals(0, \App\Models\Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }

    public function test_job_failed_lifecycle_calls_mark_failed_and_refund(): void
    {
        app(AIService::class)->checkAndDeductLimit($this->user->id); // solan = 1

        $job = new ProcessAIGradingJob($this->traloi->id, $this->user->id, today()->toDateString(), 1);
        $job->failed(new Exception('Queue timeout 150 seconds reached'));

        $fresh = $this->traloi->fresh();
        $this->assertEquals('failed', $fresh->trangthai_ai);
        $this->assertStringContainsString('gián đoạn', $fresh->nhanxet_ai);
        // Kiểm tra lượt đã được hoàn
        $this->assertEquals(0, \App\Models\Aisudung::where('nguoidung_id', $this->user->id)->value('solan'));
    }
}

