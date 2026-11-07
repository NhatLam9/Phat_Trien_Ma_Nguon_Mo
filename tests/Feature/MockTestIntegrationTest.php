<?php

namespace Tests\Feature;

use App\Jobs\ProcessAIGradingJob;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Traloi;
use App\Services\AIService;
use App\Services\TestService;
use Database\Seeders\MockTestLevelSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\Support\CreatesMockExam;
use Tests\TestCase;

class MockTestIntegrationTest extends TestCase
{
    use CreatesMockExam, DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        config(['ai.provider' => 'mock', 'ai.daily_limit' => 20]);
        Queue::fake();
        Storage::fake('local');
    }

    public function test_fixed_exam_can_jump_ahead_and_keep_question_order_and_timer(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $service->capCauHoiTiepTheo($attempt->id, $user->id);
        $started = $attempt->batdau->toDateTimeString();
        $this->travel(30)->seconds();
        $this->actingAs($user)->get(route('mock-test.show', ['id' => $attempt->id, 'so' => 4]))
            ->assertOk()->assertViewHas('cauDuocCap', fn ($q) => (int) $q->cauhoi_id === $questions[3]['id']);
        $this->assertSame(array_column($questions, 'id'), Lanthicauhoi::where('lanthi_id', $attempt->id)->orderBy('thutu')->pluck('cauhoi_id')->all());
        $this->assertSame($started, $attempt->fresh()->batdau->toDateTimeString());
        $this->get(route('mock-test.show', ['id' => $attempt->id, 'so' => 4]))->assertOk();
        $this->assertDatabaseCount('lanthicauhoi', 4);
        $this->assertDatabaseCount('traloi', 0);
    }

    public function test_submitted_question_can_be_viewed_without_allowing_resubmission(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $question = $service->capCauHoiTiepTheo($attempt->id, $user->id);
        $service->nopCauTraLoi($attempt->id, $question->id, $user->id, $questions[0]['option'], null);
        $this->actingAs($user)->get(route('mock-test.show', ['id' => $attempt->id, 'cau' => $question->id]))
            ->assertOk()->assertSee('Câu này đã được nộp.')
            ->assertViewHas('dapAnDaNop', fn ($a) => (int) $a->luachon_id === $questions[0]['option']);
        $this->post(route('mock-test.submit', $attempt->id), [
            'lanthicauhoi_id' => $question->id, 'luachon_id' => $questions[0]['option'],
        ])->assertSessionHasErrors();
        $this->assertDatabaseCount('traloi', 1);
    }

    public function test_navigation_rejects_other_attempts_invalid_numbers_and_adaptive_jumps(): void
    {
        $user = $this->createStudent();
        $other = $this->createStudent('other-navigation@example.com');
        [$exam] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $foreignAttempt = $service->batDauThi($other->id, $exam);
        $foreignQuestion = $service->capCauHoiTiepTheo($foreignAttempt->id, $other->id);
        $this->actingAs($user)->get(route('mock-test.show', ['id' => $attempt->id, 'cau' => $foreignQuestion->id]))->assertNotFound();
        foreach (['0', '5', 'abc', '-1'] as $number) {
            $this->get(route('mock-test.show', ['id' => $attempt->id, 'so' => $number]))->assertNotFound();
        }
        $service->boDoBaiThi($attempt->id, $user->id);
        [$adaptive] = $this->createMockExam('adaptive');
        $adaptiveAttempt = $service->batDauThi($user->id, $adaptive);
        $this->get(route('mock-test.show', ['id' => $adaptiveAttempt->id, 'so' => 4]))->assertNotFound();
        $this->assertSame(0, Lanthicauhoi::where('lanthi_id', $adaptiveAttempt->id)->count());
    }

    protected function usage(int $userId): int
    {
        return (int) DB::table('aisudung')->where('nguoidung_id', $userId)->sum('solan');
    }

    public function test_replacement_exams_cover_all_skills_and_issue_completion_certificates(): void
    {
        $user = $this->createStudent();
        [$oldExam] = $this->createMockExam(withAi: false);
        $service = app(TestService::class);
        $oldAttempt = $service->batDauThi($user->id, $oldExam);
        $service->boDoBaiThi($oldAttempt->id, $user->id);
        [$unusedExam] = $this->createMockExam(withAi: false);

        $this->seed(MockTestLevelSeeder::class);
        $this->seed(MockTestLevelSeeder::class);

        $this->assertDatabaseHas('dethi', ['id' => $oldExam, 'trangthai' => 'hidden']);
        $this->assertDatabaseMissing('dethi', ['id' => $unusedExam]);
        $exams = DB::table('dethi')->where('trangthai', 'published')->orderBy('ten')->get();
        $this->assertCount(8, $exams);

        foreach ($exams as $index => $exam) {
            $questions = DB::table('dethicauhoi as dc')
                ->join('cauhoi as ch', 'ch.id', '=', 'dc.cauhoi_id')
                ->join('loaicauhoi as lc', 'lc.id', '=', 'ch.loai_id')
                ->where('dc.dethi_id', $exam->id)
                ->orderBy('dc.thutu')
                ->get(['ch.dokho', 'lc.kynang']);

            $this->assertSame(['reading', 'listening', 'writing', 'speaking'], $questions->pluck('kynang')->all());
            $this->assertEquals([$index + 1], $questions->pluck('dokho')->unique()->values()->all());
        }

        $attempt = $service->batDauThi($user->id, $exams->first()->id);
        $this->actingAs($user)->post(route('mock-test.finish', $attempt->id), [
            'xac_nhan_bo_trong' => '1',
        ])->assertRedirect(route('mock-test.result', $attempt->id));
        $service->chotBaiThi($attempt->id, $user->id, true);
        $this->assertDatabaseCount('chungchi', 1);
        $certificate = $attempt->chungChi()->firstOrFail();
        $this->assertSame($attempt->fresh()->tongdiem, $certificate->tongdiem);
        $this->actingAs($user)->get(route('certificate.show', $certificate->id))
            ->assertOk()->assertSee('Chứng nhận hoàn thành – '.$exams->first()->ten);

        $timedAttempt = $service->batDauThi($user->id, $exams->last()->id);
        $this->travel(901)->seconds();
        $service->kiemTraHetHan($timedAttempt->id, $user->id);
        $this->assertDatabaseHas('chungchi', [
            'lanthi_id' => $timedAttempt->id,
            'tongdiem' => $timedAttempt->fresh()->tongdiem,
        ]);
    }

    public function test_preparation_does_not_create_attempt_or_consume_quota(): void
    {
        $user = $this->createStudent();
        [$exam] = $this->createMockExam();
        $this->actingAs($user)->post(route('mock-test.start'), ['dethi_id' => $exam])
            ->assertOk()->assertViewIs('mock-test.prepare');
        $this->assertDatabaseCount('lanthi', 0);
        $this->assertSame(0, $this->usage($user->id));
    }

    public function test_confirming_start_serves_first_question_and_reserves_ai_once(): void
    {
        $user = $this->createStudent();
        [$exam] = $this->createMockExam();
        $this->actingAs($user)->post(route('mock-test.start'), [
            'dethi_id' => $exam, 'xac_nhan_bat_dau' => '1',
        ])->assertRedirect();
        $attempt = Lanthi::firstOrFail();
        $started = $attempt->batdau->toDateTimeString();
        $this->travel(30)->seconds();
        $this->post(route('mock-test.start'), [
            'dethi_id' => $exam, 'xac_nhan_bat_dau' => '1',
        ])->assertRedirect();
        $this->assertDatabaseCount('lanthi', 1);
        $this->assertDatabaseCount('lanthicauhoi', 1);
        $this->assertSame($started, $attempt->fresh()->batdau->toDateTimeString());
        $this->assertSame(2, $this->usage($user->id));
    }

    public function test_insufficient_ai_quota_rolls_back_attempt_creation(): void
    {
        $user = $this->createStudent();
        [$exam] = $this->createMockExam();
        config(['ai.daily_limit' => 1]);
        $this->actingAs($user)->post(route('mock-test.start'), [
            'dethi_id' => $exam, 'xac_nhan_bat_dau' => '1',
        ])->assertSessionHasErrors('ai_limit');
        $this->assertDatabaseCount('lanthi', 0);
        $this->assertSame(0, $this->usage($user->id));
    }

    public function test_abandon_returns_only_unused_reservations_once(): void
    {
        $user = $this->createStudent();
        [$exam] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $service->boDoBaiThi($attempt->id, $user->id);
        $service->boDoBaiThi($attempt->id, $user->id);
        $this->assertSame(0, $this->usage($user->id));
        $this->assertSame('abandoned', $attempt->fresh()->trangthai);
    }

    public function test_timeout_closes_once_at_deadline_and_refunds_unused_quota(): void
    {
        $user = $this->createStudent();
        [$exam] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $deadline = $service->layHanThi($attempt);
        $this->travel(3601)->seconds();
        $service->kiemTraHetHan($attempt->id, $user->id);
        $service->kiemTraHetHan($attempt->id, $user->id);
        $this->assertSame('completed', $attempt->fresh()->trangthai);
        $this->assertTrue($deadline->equalTo($attempt->fresh()->ketthuc));
        $this->assertSame(1, DB::table('vipham')->where('lanthi_id', $attempt->id)->count());
        $this->assertSame(0, $this->usage($user->id));
    }

    public function test_automatic_answer_cannot_be_submitted_twice(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam(withAi: false);
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $question = $service->capCauHoiTiepTheo($attempt->id, $user->id);
        $service->nopCauTraLoi($attempt->id, $user->id, $question->id, $questions[0]['option'], null);
        try {
            $service->nopCauTraLoi($attempt->id, $user->id, $question->id, $questions[0]['option'], null);
            $this->fail('Duplicate answer was accepted.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('traloi', $e->errors());
        }
        $this->assertDatabaseCount('traloi', 1);
    }

    public function test_adaptive_uses_nearest_difficulty_and_does_not_repeat(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam('adaptive', false);
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $first = $service->capCauHoiTiepTheo($attempt->id, $user->id);
        $this->assertSame($questions[0]['id'], $first->cauhoi_id);
        $service->nopCauTraLoi($attempt->id, $user->id, $first->id, $questions[0]['option'], null);
        $this->assertEquals(6, $attempt->fresh()->dokho_hientai);
        $second = $service->capCauHoiTiepTheo($attempt->id, $user->id);
        $this->assertSame($questions[1]['id'], $second->cauhoi_id);
        $this->assertNotEquals($first->cauhoi_id, $second->cauhoi_id);
    }

    public function test_full_ai_flow_records_weighted_and_det_scores_then_allows_certificate(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $answers = [];
        foreach ($questions as $item) {
            $question = $service->capCauHoiTiepTheo($attempt->id, $user->id);
            $audio = $item['skill'] === 'speaking'
                ? UploadedFile::fake()->create('answer.wav', 10, 'audio/wav') : null;
            $answers[] = $service->nopCauTraLoi(
                $attempt->id, $user->id, $question->id,
                $item['option'], $item['ai'] && ! $audio ? 'A clear and complete answer.' : null, $audio
            );
        }
        Queue::assertPushed(ProcessAIGradingJob::class, 2);
        $this->assertSame(2, $this->usage($user->id));
        $service->chotBaiThi($attempt->id, $user->id);
        // Pending answers must block a direct certificate request, even with valid temporary scores.
        $this->actingAs($user)->post(route('certificate.issue', $attempt->id))
            ->assertSessionHasErrors('certificate');
        foreach (array_slice($answers, 2) as $answer) {
            $job = new ProcessAIGradingJob($answer->id, $user->id, $answer->ngay_tru_luot, 1);
            $job->handle(app(AIService::class));
            $this->assertEquals(115, $answer->fresh()->diem_ai);
            $this->assertEquals(0.7, $answer->fresh()->diem);
            $this->assertSame('done', $answer->fresh()->trangthai_ai);
        }
        $this->assertSame(140, $attempt->fresh()->tongdiem);
        $this->post(route('certificate.issue', $attempt->id))->assertRedirect();
        $this->post(route('certificate.issue', $attempt->id))->assertRedirect();
        $this->assertDatabaseCount('chungchi', 1);
        $certificateId = DB::table('chungchi')->value('id');
        $this->get(route('certificate.show', $certificateId))->assertOk();
        $other = $this->createStudent('certificate-other@example.com');
        $this->actingAs($other)->get(route('certificate.show', $certificateId))->assertNotFound();
        $this->actingAs($user);
        $this->assertDatabaseHas('chungchi', ['lanthi_id' => $attempt->id, 'tongdiem' => $attempt->fresh()->tongdiem]);
        $this->get(route('mock-test.result', $attempt->id))->assertOk();
    }

    public function test_other_user_cannot_access_or_issue_certificate(): void
    {
        $owner = $this->createStudent();
        $other = $this->createStudent('other@example.com');
        [$exam] = $this->createMockExam(withAi: false);
        $attempt = app(TestService::class)->batDauThi($owner->id, $exam);
        $this->actingAs($other)->get(route('mock-test.show', $attempt->id))->assertForbidden();
        $this->post(route('certificate.issue', $attempt->id))->assertNotFound();
    }

    public function test_stale_ai_attempt_cannot_replace_final_score(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $question = Lanthicauhoi::create([
            'lanthi_id' => $attempt->id, 'cauhoi_id' => $questions[2]['id'],
            'thutu' => 1, 'diemtoida' => 1, 'datraloi' => true,
        ]);
        $answer = Traloi::create([
            'lanthicauhoi_id' => $question->id, 'noidung' => 'Answer',
            'trangthai_ai' => 'pending', 'luot_cham' => 2,
        ]);
        $this->assertFalse($service->ghiNhanKetQuaAi($answer->id, 160, 'stale', 1));
        $this->assertNull($answer->fresh()->diem);
        $this->assertTrue($service->ghiNhanKetQuaAi($answer->id, 115, 'current', 2));
        $this->assertFalse($service->ghiNhanKetQuaAi($answer->id, 160, 'duplicate', 2));
        $this->assertEquals(115, $answer->fresh()->diem_ai);
    }

    public function test_all_automatic_answers_produce_160_and_finish_is_idempotent(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam(withAi: false);
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        foreach ($questions as $item) {
            $question = $service->capCauHoiTiepTheo($attempt->id, $user->id);
            $service->nopCauTraLoi($attempt->id, $user->id, $question->id, $item['option'], null);
        }
        $service->chotBaiThi($attempt->id, $user->id);
        $ended = $attempt->fresh()->ketthuc->toDateTimeString();
        $this->travel(30)->seconds();
        $service->chotBaiThi($attempt->id, $user->id);
        $this->assertSame(160, $attempt->fresh()->tongdiem);
        $this->assertSame($ended, $attempt->fresh()->ketthuc->toDateTimeString());
        Queue::assertNothingPushed();
    }

    public function test_failed_ai_job_refunds_once_and_does_not_award_a_score(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam();
        $service = app(TestService::class);
        $attempt = $service->batDauThi($user->id, $exam);
        $question = Lanthicauhoi::create([
            'lanthi_id' => $attempt->id, 'cauhoi_id' => $questions[2]['id'],
            'thutu' => 1, 'diemtoida' => 1, 'datraloi' => true,
        ]);
        $answer = Traloi::create([
            'lanthicauhoi_id' => $question->id, 'noidung' => 'Answer',
            'trangthai_ai' => 'pending', 'luot_cham' => 1,
            'ngay_tru_luot' => today(), 'thoidiem_cham' => now(),
        ]);
        $job = new ProcessAIGradingJob($answer->id, $user->id, today(), 1);
        $job->failed(new \RuntimeException('Provider unavailable'));
        $job->failed(new \RuntimeException('Repeated failure callback'));
        $this->assertSame('failed', $answer->fresh()->trangthai_ai);
        $this->assertNull($answer->fresh()->diem);
        $this->assertSame(1, $this->usage($user->id));
    }

    public function test_unfinished_or_invalid_scored_attempt_cannot_receive_certificate(): void
    {
        $user = $this->createStudent();
        [$exam] = $this->createMockExam(withAi: false);
        $attempt = app(TestService::class)->batDauThi($user->id, $exam);
        $this->actingAs($user)->post(route('certificate.issue', $attempt->id))
            ->assertSessionHasErrors('certificate');
        $attempt->update(['trangthai' => 'completed', 'tongdiem' => 161,
            'literacy' => 100, 'comprehension' => 100, 'conversation' => 100, 'production' => 100]);
        $this->post(route('certificate.issue', $attempt->id))->assertSessionHasErrors('certificate');
        $this->assertDatabaseCount('chungchi', 0);
    }

    public function test_failed_ai_blocks_certificate_even_when_numeric_points_exist(): void
    {
        $user = $this->createStudent();
        [$exam, $questions] = $this->createMockExam();
        $attempt = app(TestService::class)->batDauThi($user->id, $exam);
        $attempt->update(['trangthai' => 'completed', 'tongdiem' => 100,
            'literacy' => 100, 'comprehension' => 100, 'conversation' => 100, 'production' => 100]);
        $question = Lanthicauhoi::create([
            'lanthi_id' => $attempt->id, 'cauhoi_id' => $questions[2]['id'],
            'thutu' => 1, 'datraloi' => true, 'diemtoida' => 1,
        ]);
        Traloi::create(['lanthicauhoi_id' => $question->id, 'diem' => 0.5, 'trangthai_ai' => 'failed']);
        $this->actingAs($user)->post(route('certificate.issue', $attempt->id))
            ->assertSessionHasErrors('certificate');
        $this->assertDatabaseCount('chungchi', 0);
    }
}
