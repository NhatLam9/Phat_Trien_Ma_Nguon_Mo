<?php

namespace Database\Seeders;

use App\Models\Loaicauhoi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MockTestLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = require __DIR__.'/data/mock_test_levels.php';
        $bank = require __DIR__.'/data/operational_questions.php';

        DB::transaction(function () use ($levels, $bank): void {
            $types = $this->questionTypes();

            foreach ($levels as $index => [$name, $audio, $reading, $options, $writing, $speaking, $listeningInference]) {
                $level = $index + 1;
                $exam = DB::table('dethi')->where('capdo', $level)->first();
                if ($exam) {
                    DB::table('dethi')->where('id', $exam->id)->update(['trangthai' => 'published']);
                    continue;
                }

                $listening = $bank['interactive_listening'][$audio - 1];
                if ($listeningInference !== null) {
                    [$prompt, $answers] = $listeningInference;
                    $listening['children'][] = [
                        'loai' => 'listen_and_respond',
                        'noidung' => $prompt,
                        'options' => array_map(
                            fn ($answer, $index) => [$answer, $index === 0],
                            $answers,
                            array_keys($answers)
                        ),
                    ];
                }
                $listeningId = $this->createListening($types['interactive_listening'], $listening, $level);

                $readingId = $this->createQuestion($types['read_and_select'], "det-level-{$level}-reading", $reading, $level, 90);
                // Dịch vị trí đáp án đúng giữa các đề.
                foreach ($options as $position => $option) {
                    DB::table('luachon')->insert([
                        'cauhoi_id' => $readingId,
                        'noidung' => $option,
                        'thutu' => ($position + $index) % count($options) + 1,
                        'ladung' => $position === 0,
                    ]);
                }

                $questions = [
                    $readingId,
                    $listeningId,
                    $this->createQuestion($types['write_about_photo'], "det-level-{$level}-writing", $writing, $level, 180, "images/questions/photo-{$level}.svg"),
                    $this->createQuestion($types[$level <= 2 ? 'read_aloud' : 'read_then_speak'], "det-level-{$level}-speaking", $speaking, $level, 120),
                ];

                $examId = DB::table('dethi')->insertGetId([
                    'capdo' => $level,
                    'ten' => sprintf('DET Practice %02d – %s', $level, $name),
                    'mota' => "Mức {$level}/8. Đề tổng hợp Nghe, Nói, Đọc, Viết.",
                    'chedo' => 'full',
                    'kieu' => 'fixed',
                    'thoigian' => 900,
                    'tongdiem' => count($questions),
                    'trangthai' => 'published',
                ]);

                foreach ($questions as $position => $questionId) {
                    DB::table('dethicauhoi')->insert([
                        'dethi_id' => $examId,
                        'cauhoi_id' => $questionId,
                        'thutu' => $position + 1,
                        'diem' => 1,
                    ]);
                }
            }

            // Lưu đề đã có lượt thi để lịch sử và chứng chỉ vẫn truy cập được.
            DB::table('dethi')->whereNull('capdo')->update(['trangthai' => 'hidden']);

            DB::table('dethi')->whereNull('capdo')->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('lanthi')->whereColumn('lanthi.dethi_id', 'dethi.id');
            })->delete();
        }, 3);
    }

    private function questionTypes(): array
    {
        $definitions = [
            'read_and_select' => ['Read and Select', 'reading', 'auto', 90],
            'interactive_listening' => ['Interactive Listening', 'listening', 'auto', 150],
            'write_about_photo' => ['Write About the Photo', 'writing', 'ai', 180],
            'read_aloud' => ['Read Aloud', 'speaking', 'ai', 120],
            'read_then_speak' => ['Read Then Speak', 'speaking', 'ai', 120],
        ];

        $types = [];
        foreach ($definitions as $code => [$name, $skill, $grading, $seconds]) {
            $types[$code] = Loaicauhoi::firstOrCreate(['ma' => $code], [
                'ten' => $name,
                'kynang' => $skill,
                'cachcham' => $grading,
                'thoigian' => $seconds,
                'is_active' => true,
            ])->id;
        }

        return $types;
    }

    private function createQuestion(
        int $type,
        string $code,
        string $prompt,
        int $level,
        int $seconds,
        ?string $image = null,
        ?string $audio = null
    ): int
    {
        return DB::table('cauhoi')->insertGetId([
            'loai_id' => $type,
            'ma' => $code,
            'noidung' => $prompt,
            'dokho' => $level,
            'thoigian' => $seconds,
            'anh' => $image,
            'audio' => $audio,
            'diem' => 1,
            'trangthai' => 'published',
        ]);
    }

    private function createListening(int $type, array $listening, int $level): int
    {
        if (! is_file(public_path($listening['audio']))) {
            throw new RuntimeException("Thiếu tệp âm thanh cho đề mức {$level}.");
        }

        $questionId = $this->createQuestion(
            $type,
            "det-level-{$level}-listening",
            $listening['noidung'],
            $level,
            150,
            audio: $listening['audio']
        );

        foreach ($listening['children'] as $index => $child) {
            // Chấm tự động theo lựa chọn, tránh chấm tóm tắt bằng so khớp từng chữ.
            if (empty($child['options'])) {
                continue;
            }

            $childId = DB::table('cauhoi_nghe')->insertGetId([
                'cauhoi_id' => $questionId,
                'loai' => $child['loai'],
                'noidung' => $child['noidung'],
                'giaithich' => $child['giaithich'] ?? null,
                'thutu' => $index + 1,
            ]);

            foreach ($child['options'] as $position => [$option, $correct]) {
                DB::table('luachon_nghe')->insert([
                    'cauhoi_nghe_id' => $childId,
                    'noidung' => $option,
                    'thutu' => ($position + $level + $index) % count($child['options']) + 1,
                    'ladung' => $correct,
                ]);
            }
        }

        return $questionId;
    }
}
