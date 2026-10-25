<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScoringService
{
    /**
     * Chấm điểm tự động cho các dạng câu hỏi có đáp án chuẩn.
     * Hợp đồng giao tiếp giữa TV1 và TV2:
     * - Tham số:
     *   + $question: Object/Array/ID của câu hỏi (hoặc có sẵn 'id', 'loai_id', v.v.)
     *   + $answer: Lựa chọn đã chọn (luachon_id int/string/array) hoặc text đáp án người dùng nhập
     * - Trả về: bool (true nếu đúng, false nếu sai)
     */
    public function scoreAuto($question, $answer): bool
    {
        if (empty($question) || $answer === null || $answer === '') {
            return false;
        }

        // Lấy question ID nếu truyền vào object hoặc array
        $questionId = is_object($question)
            ? ($question->id ?? null)
            : (is_array($question) ? ($question['id'] ?? null) : $question);

        // Trường hợp 1: $answer là lựa chọn trắc nghiệm (ID của luachon hoặc mảng có 'luachon_id')
        $luachonId = is_array($answer) ? ($answer['luachon_id'] ?? null) : (is_numeric($answer) ? (int)$answer : null);

        if ($luachonId !== null && Schema::hasTable('luachon')) {
            $isCorrectChoice = DB::table('luachon')
                ->where('id', $luachonId)
                ->when($questionId, function ($query, $qid) {
                    return $query->where('cauhoi_id', $qid);
                })
                ->where('ladung', 1)
                ->exists();

            if ($isCorrectChoice) {
                return true;
            }
        }

        // Trường hợp 2: Đáp án dạng văn bản / điền từ kiểm tra với bảng dap_an
        $answerText = is_array($answer) ? ($answer['noidung'] ?? null) : (string)$answer;
        if ($answerText !== null && $questionId && Schema::hasTable('dap_an')) {
            $validAnswers = DB::table('dap_an')
                ->where('cauhoi_id', $questionId)
                ->where('ladung', 1)
                ->pluck('noidung')
                ->map(fn($item) => mb_strtolower(trim($item)))
                ->toArray();

            $normalizedAnswer = mb_strtolower(trim($answerText));

            if (in_array($normalizedAnswer, $validAnswers, true)) {
                return true;
            }
        }

        // Trường hợp 3: $question mang sẵn mảng luachon / dapan đã load trong bộ nhớ (dùng khi mock hoặc unit test)
        if (is_object($question) && isset($question->luachon)) {
            foreach ($question->luachon as $opt) {
                $optId = is_object($opt) ? ($opt->id ?? null) : ($opt['id'] ?? null);
                $optLadung = is_object($opt) ? ($opt->ladung ?? 0) : ($opt['ladung'] ?? 0);
                if ($optId == $answer && $optLadung == 1) {
                    return true;
                }
            }
        }

        if (is_array($question) && isset($question['luachon'])) {
            foreach ($question['luachon'] as $opt) {
                $optId = is_array($opt) ? ($opt['id'] ?? null) : ($opt->id ?? null);
                $optLadung = is_array($opt) ? ($opt['ladung'] ?? 0) : ($opt->ladung ?? 0);
                if ($optId == $answer && $optLadung == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Tính toán chi tiết kết quả và điểm số tự động.
     * Trả về mảng: ['is_correct' => bool, 'diem' => float, 'nhanxet' => string]
     */
    public function calculateResult($question, $answer, float $maxPoint = 1.0): array
    {
        $isCorrect = $this->scoreAuto($question, $answer);
        $score = $isCorrect ? $maxPoint : 0.0;

        return [
            'is_correct' => $isCorrect,
            'diem' => $score,
            'nhanxet' => $isCorrect ? 'Đáp án chính xác.' : 'Đáp án chưa chính xác.',
        ];
    }
}
