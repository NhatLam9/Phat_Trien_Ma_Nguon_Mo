<?php

namespace App\Http\Controllers\Practice;

use App\Exceptions\AiLimitExceededException;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessAIGradingJob;
use App\Models\Cauhoi;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Traloi;
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PracticeAIController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Hiển thị giao diện làm bài câu hỏi AI Writing hoặc Speaking.
     */
    public function showQuestion(int $cauhoiId): View
    {
        $cauhoi = Cauhoi::with('loai')->findOrFail($cauhoiId);
        $kynang = strtolower((string) ($cauhoi->loai?->kynang ?? ''));
        $isSpeaking = $kynang === 'speaking';

        return view('practice.ai.question', compact('cauhoi', 'isSpeaking'));
    }

    /**
     * Nộp bài Luyện tập Writing/Speaking và đưa vào Queue chấm AI.
     */
    public function submit(int $cauhoiId, Request $request): RedirectResponse
    {
        $cauhoi = Cauhoi::with('loai')->findOrFail($cauhoiId);
        $kynang = strtolower((string) ($cauhoi->loai?->kynang ?? ''));
        $isSpeaking = $kynang === 'speaking';

        if ($isSpeaking) {
            $request->validate([
                'audio' => ['required', 'file', 'mimes:mp3,wav,m4a,ogg,webm', 'max:10240'],
            ], [
                'audio.required' => 'Vui lòng chọn hoặc tải lên file ghi âm câu trả lời của bạn.',
                'audio.mimes' => 'Định dạng file âm thanh không hợp lệ (hỗ trợ mp3, wav, m4a, ogg, webm).',
                'audio.max' => 'Dung lượng file ghi âm tối đa là 10 MB.',
            ]);
        } else {
            $request->validate([
                'noidung' => ['required', 'string', 'min:5', 'max:3000'],
            ], [
                'noidung.required' => 'Vui lòng nhập nội dung bài viết của bạn.',
                'noidung.max' => 'Bài viết không được vượt quá 3000 ký tự.',
            ]);
        }

        $userId = (int) Auth::id();
        $today = today();
        $audioPath = null;

        // Lưu file audio vào private storage nếu là speaking
        if ($isSpeaking && $request->hasFile('audio')) {
            $file = $request->file('audio');
            $audioPath = $file->store('audio', 'local');
        }

        $traloi = null;

        try {
            // Mở transaction gộp trừ lượt và lưu dữ liệu
            DB::transaction(function () use ($userId, $cauhoi, $today, $request, $audioPath, $isSpeaking, &$traloi) {
                // 1. Trừ lượt sử dụng AI
                $this->aiService->checkAndDeductLimit($userId);

                // 2. Tạo phiên luyện tập (Lanthi) độc lập để gắn kết cấu trúc CSDL
                $lanThi = Lanthi::create([
                    'nguoidung_id' => $userId,
                    'dethi_id' => null,
                    'batdau' => now(),
                    'ketthuc' => now(),
                    'trangthai' => 'practice',
                ]);

                // 3. Tạo Lanthicauhoi
                $lanThiCauHoi = Lanthicauhoi::create([
                    'lanthi_id' => $lanThi->id,
                    'cauhoi_id' => $cauhoi->id,
                    'thutu' => 1,
                    'diemtoida' => 160.0,
                    'thoigian_cap' => now(),
                    'thoigian_tra_loi' => now(),
                    'datraloi' => true,
                ]);

                // 4. Lưu Traloi với trạng thái pending
                $traloi = Traloi::create([
                    'lanthicauhoi_id' => $lanThiCauHoi->id,
                    'noidung' => $isSpeaking ? null : $request->input('noidung'),
                    'audio' => $audioPath,
                    'trangthai_ai' => 'pending',
                    'luot_cham' => 1,
                    'ngay_tru_luot' => $today,
                    'thoidiem_cham' => now(),
                    'thoigian' => now(),
                ]);
            });

            // 5. Sau khi commit transaction, dispatch Job vào Queue
            ProcessAIGradingJob::dispatch($traloi->id, $userId, $today, 1)->afterCommit();

            return redirect()->route('practice.ai.status', $traloi->id);
        } catch (AiLimitExceededException $e) {
            if ($audioPath && Storage::disk('local')->exists($audioPath)) {
                Storage::disk('local')->delete($audioPath);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['ai_limit' => $e->getMessage()]);
        } catch (Throwable $e) {
            // Không để lại file audio mồ côi khi lưu database thất bại.
            if ($audioPath && Storage::disk('local')->exists($audioPath)) {
                Storage::disk('local')->delete($audioPath);
            }

            throw $e;
        }
    }

    /**
     * Hiển thị trạng thái chấm bài hoặc kết quả chấm AI (No-JS, auto refresh nếu pending).
     */
    public function status(int $id): View
    {
        $traloi = Traloi::with(['lanThiCauHoi.cauHoi.loai', 'lanThiCauHoi.lanThi'])->findOrFail($id);

        // Kiểm tra quyền sở hữu bài làm
        $chuSoHuuId = (int) ($traloi->lanThiCauHoi?->lanThi?->nguoidung_id ?? 0);
        abort_unless($chuSoHuuId === (int) Auth::id(), 403, 'Bạn không có quyền xem kết quả chấm này.');

        // Cơ chế chống pending vĩnh viễn (> 5 phút)
        $timeoutMinutes = (int) config('ai.pending_timeout_minutes', 5);
        if (
            $traloi->trangthai_ai === 'pending'
            && $traloi->thoidiem_cham
            && $traloi->thoidiem_cham->lt(now()->subMinutes($timeoutMinutes))
        ) {
            $this->aiService->markFailedAndRefund(
                $traloi->id,
                $chuSoHuuId,
                $traloi->ngay_tru_luot,
                $traloi->luot_cham,
                "Hệ thống chấm bài quá {$timeoutMinutes} phút không nhận được phản hồi. Hệ thống đã tự động hoàn lại lượt cho bạn."
            );
            $traloi->refresh();
        }

        // Kiểm tra nếu lỗi do transcript quá dài thì ẩn nút Chấm lại
        $isTranscriptTooLong = $traloi->trangthai_ai === 'failed'
            && AIService::isTranscriptTooLong($traloi->nhanxet_ai);

        $nhanXet = AIService::cleanFeedback($traloi->nhanxet_ai);

        $cauhoi = $traloi->lanThiCauHoi?->cauHoi;
        $isSpeaking = ! empty($traloi->audio) || strtolower((string) ($cauhoi?->loai?->kynang ?? '')) === 'speaking';

        return view('practice.ai.status', compact('traloi', 'cauhoi', 'isSpeaking', 'isTranscriptTooLong', 'nhanXet'));
    }

    /**
     * Phát file ghi âm Speaking từ private storage.
     * File không nằm trong public/ nên phải đi qua route có kiểm tra quyền sở hữu.
     */
    public function audio(int $id): StreamedResponse
    {
        $traloi = Traloi::with(['lanThiCauHoi.lanThi'])->findOrFail($id);

        $chuSoHuuId = (int) ($traloi->lanThiCauHoi?->lanThi?->nguoidung_id ?? 0);
        abort_unless($chuSoHuuId === (int) Auth::id(), 403, 'Bạn không có quyền nghe file ghi âm này.');

        $path = (string) $traloi->audio;
        abort_if($path === '' || ! Storage::disk('local')->exists($path), 404, 'Không tìm thấy file ghi âm.');

        return Storage::disk('local')->response($path);
    }

    /**
     * Chấm lại bài làm bị lỗi (Chỉ cho phép khi trangthai_ai = 'failed').
     */
    public function retry(int $id): RedirectResponse
    {
        $traloi = Traloi::with(['lanThiCauHoi.lanThi'])->findOrFail($id);
        $userId = (int) ($traloi->lanThiCauHoi?->lanThi?->nguoidung_id ?? 0);

        abort_unless($userId === (int) Auth::id(), 403, 'Bạn không có quyền chấm lại bài làm này.');

        // Chỉ chấm lại khi bài đang ở trạng thái thất bại.
        // pending/done không được phép trừ thêm lượt của người dùng.
        if ($traloi->trangthai_ai !== 'failed') {
            return redirect()->route('practice.ai.status', $id);
        }

        // Bài nói quá dài: bắt buộc nộp audio mới, không cho chấm lại (tránh lãng phí lượt).
        if (AIService::isTranscriptTooLong($traloi->nhanxet_ai)) {
            return redirect()->route('practice.ai.status', $id)
                ->withErrors(['ai_limit' => 'Bài nói quá dài so với quy định, vui lòng nộp bản ghi âm mới thay vì chấm lại.']);
        }

        $today = today();
        $attemptCu = (int) $traloi->luot_cham;

        // 1. Trừ lượt trước: hết hạn mức thì giữ nguyên bản ghi ở trạng thái failed.
        try {
            $this->aiService->checkAndDeductLimit($userId);
        } catch (AiLimitExceededException $e) {
            return redirect()->route('practice.ai.status', $id)
                ->withErrors(['ai_limit' => $e->getMessage()]);
        }

        // 2. Chuyển failed -> pending có điều kiện để hai tab cùng bấm chỉ thắng một lần.
        $affected = Traloi::query()
            ->where('id', $id)
            ->where('trangthai_ai', 'failed')
            ->update([
                'trangthai_ai' => 'pending',
                'luot_cham' => DB::raw('luot_cham + 1'),
                'ngay_tru_luot' => $today,
                'thoidiem_cham' => now(),
            ]);

        if ($affected !== 1) {
            // Tab khác đã chuyển bản ghi sang pending trước: hoàn lại lượt vừa trừ.
            $this->aiService->refundUsage($userId, $today);

            return redirect()->route('practice.ai.status', $id);
        }

        ProcessAIGradingJob::dispatch($traloi->id, $userId, $today, $attemptCu + 1)->afterCommit();

        return redirect()->route('practice.ai.status', $id)
            ->with('success', 'Đã gửi yêu cầu chấm lại thành công!');
    }
}
