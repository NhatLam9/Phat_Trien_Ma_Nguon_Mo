<?php

namespace App\Jobs;

use App\Models\Cauhoi;
use App\Models\Lanthicauhoi;
use App\Models\Traloi;
use App\Services\AIService;
use App\Services\TestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessAIGradingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Số lần thử tối đa.
     */
    public int $tries = 2;

    /**
     * Timeout tối đa của Job (giây).
     * Quy tắc: retry_after (200) > worker --timeout (160) > $timeout (150).
     */
    public int $timeout = 150;

    /**
     * Thời gian chờ giữa các lần thử lại (giây).
     */
    public array $backoff = [10, 30];

    public int $traloiId;

    public ?int $userId;

    public mixed $date;

    public int $attempt;

    /**
     * Tạo một Job chấm điểm AI.
     */
    public function __construct(int $traloiId, ?int $userId, mixed $date, int $attempt)
    {
        $this->traloiId = $traloiId;
        $this->userId = $userId;
        $this->date = $date;
        $this->attempt = $attempt;
    }

    /**
     * Thực thi Job chấm bài AI.
     */
    public function handle(AIService $aiService): void
    {
        // 1. Kiểm tra đầu Job: nếu không còn pending hoặc attempt cũ thì bỏ qua (Idempotency)
        $traloi = Traloi::query()->find($this->traloiId);

        if (! $traloi || $traloi->trangthai_ai !== 'pending' || (int) $traloi->luot_cham !== $this->attempt) {
            Log::info('ProcessAIGradingJob: Bỏ qua do trạng thái hoặc luot_cham không khớp.', [
                'traloi_id' => $this->traloiId,
                'current_attempt' => $traloi?->luot_cham,
                'job_attempt' => $this->attempt,
                'status' => $traloi?->trangthai_ai,
            ]);

            return;
        }

        // 2. Tìm thông tin câu hỏi và kỹ năng
        $lanThiCauHoi = null;
        $cauHoi = null;
        $loaiCauHoi = null;

        if ($traloi->lanthicauhoi_id) {
            $lanThiCauHoi = Lanthicauhoi::with(['lanThi'])->find($traloi->lanthicauhoi_id);
            if ($lanThiCauHoi && $lanThiCauHoi->cauhoi_id) {
                $cauHoi = Cauhoi::with('loai')->find($lanThiCauHoi->cauhoi_id);
                $loaiCauHoi = $cauHoi?->loai;
            }
            if (! $this->userId && $lanThiCauHoi?->lanThi) {
                $this->userId = (int) $lanThiCauHoi->lanThi->nguoidung_id;
            }
        }

        if ($lanThiCauHoi?->lanThi?->trangthai === 'abandoned') {
            $aiService->markFailedAndRefund(
                $this->traloiId, $this->userId, $this->date, $this->attempt,
                'Bài thi đã bỏ dở, lượt AI đã được hoàn.'
            );

            return;
        }

        $kyNang = strtolower((string) ($loaiCauHoi?->kynang ?? ''));
        $questionType = $loaiCauHoi?->ten ?? $loaiCauHoi?->ma ?? 'General';
        $questionContent = $cauHoi?->noidung ?? 'Evaluate the student submission according to DET criteria.';

        $isSpeaking = $kyNang === 'speaking' || ! empty($traloi->audio);

        // 3. Gọi chấm điểm ngoài transaction
        if ($isSpeaking) {
            $gradeResult = $aiService->gradeSpeaking(
                question: $questionContent,
                audioPath: $traloi->audio,
                existingTranscript: $traloi->noidung,
                questionType: $questionType,
                userId: $this->userId
            );

            // Nếu transcript quá dài: xóa noidung và markFailedAndRefund
            if (isset($gradeResult['too_long']) && $gradeResult['too_long']) {
                Traloi::query()
                    ->where('id', $this->traloiId)
                    ->where('trangthai_ai', 'pending')
                    ->where('luot_cham', $this->attempt)
                    ->update(['noidung' => null]);

                $aiService->markFailedAndRefund(
                    $this->traloiId,
                    $this->userId,
                    $this->date,
                    $this->attempt,
                    $gradeResult['message']
                );

                return;
            }

            // Nếu vừa nhận transcript mới, lưu vào noidung để lần sau chấm lại không cần gọi lại STT
            if (! empty($gradeResult['transcript']) && empty($traloi->noidung)) {
                Traloi::query()
                    ->where('id', $this->traloiId)
                    ->where('trangthai_ai', 'pending')
                    ->where('luot_cham', $this->attempt)
                    ->update(['noidung' => $gradeResult['transcript']]);
            }
        } else {
            // Luồng Writing
            $gradeResult = $aiService->gradeWriting(
                question: $questionContent,
                answer: (string) $traloi->noidung,
                questionType: $questionType,
                userId: $this->userId
            );
        }

        // 4. Xử lý kết quả trả về từ AIService
        if (($gradeResult['status'] ?? '') === 'failed') {
            // Lỗi vĩnh viễn (4xx, transcript quá dài, format lỗi): Job xử lý ngay, không retry
            $aiService->markFailedAndRefund(
                $this->traloiId,
                $this->userId,
                $this->date,
                $this->attempt,
                $gradeResult['message'] ?? 'Chấm bài thất bại, hệ thống đã hoàn lại lượt cho bạn.'
            );

            return;
        }

        // 5. Ghi nhận kết quả bằng conditional update để chống Job cũ ghi đè Job mới
        $score = $gradeResult['score'] ?? null;
        $feedback = $gradeResult['feedback'] ?? null;

        if (! empty($gradeResult['subscores'])) {
            $feedback .= "\n\n--- TIÊU CHÍ CHI TIẾT ---\n".json_encode($gradeResult['subscores'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        // Practice giữ điểm DET; Thi thử cần điểm có trọng số để tổng hợp.
        if ($lanThiCauHoi?->lanThi?->dethi_id !== null) {
            if (! is_numeric($score) || ! is_finite((float) $score) || $score < 10 || $score > 160) {
                $aiService->markFailedAndRefund(
                    $this->traloiId, $this->userId, $this->date, $this->attempt,
                    'AI trả về điểm không hợp lệ.'
                );

                return;
            }
            app(TestService::class)->ghiNhanKetQuaAi(
                $this->traloiId, (float) $score, (string) $feedback, $this->attempt
            );

            return;
        }

        $affected = Traloi::query()
            ->where('id', $this->traloiId)
            ->where('trangthai_ai', 'pending')
            ->where('luot_cham', $this->attempt)
            ->update([
                'diem_ai' => $score,
                'nhanxet_ai' => $feedback,
                'trangthai_ai' => 'done',
            ]);

        if ($affected === 0) {
            Log::warning('ProcessAIGradingJob: Kết quả bị hủy do bản ghi đã được cập nhật hoặc chấm lại.', [
                'traloi_id' => $this->traloiId,
                'attempt' => $this->attempt,
            ]);
        }
    }

    /**
     * Xử lý khi Job thất bại hoàn toàn sau các lượt retry.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessAIGradingJob failed: '.($exception ? $exception->getMessage() : 'Unknown error'), [
            'traloi_id' => $this->traloiId,
            'user_id' => $this->userId,
            'attempt' => $this->attempt,
        ]);

        $message = 'Hệ thống AI gián đoạn sau các lần thử. Hệ thống đã hoàn trả 1 lượt sử dụng cho bạn.';
        app(AIService::class)->markFailedAndRefund(
            $this->traloiId,
            $this->userId,
            $this->date,
            $this->attempt,
            $message
        );
    }
}
