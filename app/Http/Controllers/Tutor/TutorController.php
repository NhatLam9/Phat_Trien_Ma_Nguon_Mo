<?php

namespace App\Http\Controllers\Tutor;

use App\Exceptions\AiLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\Ailog;
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class TutorController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Trang tổng quan AI Tutor.
     */
    public function index(): View
    {
        return view('tutor.index');
    }

    /**
     * TU01 - Giao diện sửa ngữ pháp (Grammar Checker).
     */
    public function showGrammar(): View
    {
        return view('tutor.grammar');
    }

    /**
     * TU01 - Xử lý kiểm tra và sửa ngữ pháp (No-JS, PRG).
     */
    public function processGrammar(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:3000'],
        ], [
            'content.required' => 'Vui lòng nhập đoạn văn cần kiểm tra ngữ pháp.',
            'content.max' => 'Đoạn văn không được vượt quá 3000 ký tự.',
        ]);

        $userId = (int) Auth::id();
        $today = today();

        // 1. Trừ lượt trước khi gọi API (transaction ngắn)
        try {
            $this->aiService->checkAndDeductLimit($userId);
        } catch (AiLimitExceededException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['ai_limit' => $e->getMessage()]);
        }

        // 2. Gọi API ngoài transaction
        try {
            $result = $this->aiService->grammarCheck($request->input('content'), $userId);

            // 3. PRG thành công: lưu flash session
            return redirect()->back()
                ->withInput()
                ->with('ai_result', $result)
                ->with('success', 'Đã phân tích ngữ pháp thành công!');
        } catch (Throwable $e) {
            // 4. Lỗi API: hoàn lại lượt và giữ nguyên form input
            $this->aiService->refundUsage($userId, $today);

            return redirect()->back()
                ->withInput()
                ->withErrors(['api_error' => 'Dịch vụ AI gặp sự cố (' . $e->getMessage() . '). Hệ thống đã hoàn lại 1 lượt cho bạn.']);
        }
    }

    /**
     * TU02 - Giao diện dịch thuật (Translator).
     */
    public function showTranslate(): View
    {
        return view('tutor.translate');
    }

    /**
     * TU02 - Xử lý dịch thuật Anh - Việt / Việt - Anh (No-JS, PRG).
     */
    public function processTranslate(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:3000'],
            'direction' => ['required', 'in:en_vi,vi_en'],
        ], [
            'content.required' => 'Vui lòng nhập văn bản cần dịch.',
            'content.max' => 'Văn bản không được vượt quá 3000 ký tự.',
            'direction.in' => 'Chiều dịch không hợp lệ.',
        ]);

        $userId = (int) Auth::id();
        $today = today();

        try {
            $this->aiService->checkAndDeductLimit($userId);
        } catch (AiLimitExceededException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['ai_limit' => $e->getMessage()]);
        }

        try {
            $result = $this->aiService->translateText(
                $request->input('content'),
                $request->input('direction'),
                $userId
            );

            return redirect()->back()
                ->withInput()
                ->with('ai_result', $result)
                ->with('direction', $request->input('direction'))
                ->with('success', 'Dịch thuật hoàn tất!');
        } catch (Throwable $e) {
            $this->aiService->refundUsage($userId, $today);

            return redirect()->back()
                ->withInput()
                ->withErrors(['api_error' => 'Không thể kết nối dịch vụ dịch thuật. Lượt của bạn đã được hoàn lại.']);
        }
    }

    /**
     * TU03 - Giao diện viết lại câu / đoạn văn (Rewriter).
     */
    public function showRewrite(): View
    {
        return view('tutor.rewrite');
    }

    /**
     * TU03 - Xử lý viết lại câu / nâng cấp văn phong (No-JS, PRG).
     */
    public function processRewrite(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'min:5', 'max:3000'],
            'style' => ['required', 'in:academic,natural,det_120'],
        ], [
            'content.required' => 'Vui lòng nhập câu hoặc đoạn văn cần viết lại.',
            'content.max' => 'Nội dung không được vượt quá 3000 ký tự.',
            'style.in' => 'Văn phong viết lại không hợp lệ.',
        ]);

        $userId = (int) Auth::id();
        $today = today();

        try {
            $this->aiService->checkAndDeductLimit($userId);
        } catch (AiLimitExceededException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['ai_limit' => $e->getMessage()]);
        }

        try {
            $result = $this->aiService->rewriteText(
                $request->input('content'),
                $request->input('style'),
                $userId
            );

            return redirect()->back()
                ->withInput()
                ->with('ai_result', $result)
                ->with('style', $request->input('style'))
                ->with('success', 'Đã viết lại và nâng cấp câu thành công!');
        } catch (Throwable $e) {
            $this->aiService->refundUsage($userId, $today);

            return redirect()->back()
                ->withInput()
                ->withErrors(['api_error' => 'Dịch vụ AI viết lại gặp sự cố. Lượt của bạn đã được hoàn lại.']);
        }
    }

    /**
     * TU04 - Lịch sử tương tác AI Tutor của học viên.
     */
    public function history(Request $request): View
    {
        $userId = (int) Auth::id();
        $loaiFilter = $request->query('loai');

        $query = Ailog::query()
            ->where('nguoidung_id', $userId)
            ->whereIn('loai', ['grammar', 'translate', 'rewrite', 'speaking', 'writing'])
            ->orderByDesc('ngaytao');

        if ($loaiFilter) {
            $query->where('loai', $loaiFilter);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('tutor.history', compact('logs', 'loaiFilter'));
    }
}

