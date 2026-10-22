<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dữ liệu mẫu cho MT01/MT02 của TV2.
 *
 * Seeder này chỉ ghi vào các bảng do TV3 sở hữu:
 * loaicauhoi, cauhoi, luachon, cauhoi_doc, cauhoi_nghe,
 * luachon_doc, luachon_nghe, dethi, dethicauhoi.
 *
 * Không seed nguoidung (TV1) và không seed lanthi/lanthicauhoi (TV2).
 */
class MockTestDataSeeder extends Seeder
{
    /**
     * Các bảng bắt buộc phải có trước khi seed.
     *
     * @var array<int, string>
     */
    private array $requiredTables = [
        'loaicauhoi',
        'cauhoi',
        'luachon',
        'cauhoi_doc',
        'luachon_doc',
        'cauhoi_nghe',
        'luachon_nghe',
        'dethi',
        'dethicauhoi',
    ];

    public function run(): void
    {
        foreach ($this->requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                if ($this->command) {
                    $this->command->warn(
                        "Bỏ qua MockTestDataSeeder: thiếu bảng {$table}."
                    );
                }

                return;
            }
        }

        DB::transaction(function (): void {
            $types = $this->seedQuestionTypes();
            $questions = $this->seedQuestions($types);

            $this->seedExam(
                'DET Mock Test - Fixed',
                'fixed',
                'Đề mẫu fixed: 8 dạng câu hỏi, thứ tự cố định.',
                $questions
            );

            $this->seedExam(
                'DET Mock Test - Adaptive',
                'adaptive',
                'Đề mẫu adaptive: độ khó 2-9 để test chọn câu.',
                $questions
            );
        });

        if ($this->command) {
            $this->command->info(
                'Đã seed dữ liệu mẫu cho MT01/MT02.'
            );

            $this->command->table(
                ['ID', 'Tên đề', 'Kiểu', 'Trạng thái'],
                DB::table('dethi')
                    ->whereIn('ten', [
                        'DET Mock Test - Fixed',
                        'DET Mock Test - Adaptive',
                    ])
                    ->orderBy('id')
                    ->get(['id', 'ten', 'kieu', 'trangthai'])
                    ->map(fn ($exam) => [
                        $exam->id,
                        $exam->ten,
                        $exam->kieu,
                        $exam->trangthai,
                    ])
                    ->all()
            );
        }
    }

    /**
     * @return array<string, int>
     */
    private function seedQuestionTypes(): array
    {
        $types = [
            [
                'ma' => 'read_and_select',
                'ten' => 'Read and Select',
                'kynang' => 'reading',
                'cachcham' => 'auto',
                'thoigian' => 60,
                'mota' => 'Chọn từ đúng để hoàn thành câu.',
            ],
            [
                'ma' => 'fill_in_the_blanks',
                'ten' => 'Fill in the Blanks',
                'kynang' => 'reading',
                'cachcham' => 'auto',
                'thoigian' => 90,
                'mota' => 'Điền từ còn thiếu vào chỗ trống.',
            ],
            [
                'ma' => 'read_and_complete',
                'ten' => 'Read and Complete',
                'kynang' => 'reading',
                'cachcham' => 'auto',
                'thoigian' => 90,
                'mota' => 'Đọc đoạn văn và chọn từ phù hợp.',
            ],
            [
                'ma' => 'interactive_reading',
                'ten' => 'Interactive Reading',
                'kynang' => 'reading',
                'cachcham' => 'auto',
                'thoigian' => 120,
                'mota' => 'Đọc bài và trả lời các câu hỏi con.',
            ],
            [
                'ma' => 'interactive_listening',
                'ten' => 'Interactive Listening',
                'kynang' => 'listening',
                'cachcham' => 'auto',
                'thoigian' => 120,
                'mota' => 'Nghe bài và trả lời các câu hỏi con.',
            ],
            [
                'ma' => 'write_about_photo',
                'ten' => 'Write About the Photo',
                'kynang' => 'writing',
                'cachcham' => 'ai',
                'thoigian' => 180,
                'mota' => 'Viết mô tả ngắn về bức ảnh.',
            ],
            [
                'ma' => 'read_aloud',
                'ten' => 'Read Aloud',
                'kynang' => 'speaking',
                'cachcham' => 'ai',
                'thoigian' => 60,
                'mota' => 'Đọc to đoạn văn cho trước.',
            ],
            [
                'ma' => 'read_then_speak',
                'ten' => 'Read Then Speak',
                'kynang' => 'speaking',
                'cachcham' => 'ai',
                'thoigian' => 120,
                'mota' => 'Đọc đoạn văn rồi trình bày quan điểm.',
            ],
        ];

        $ids = [];

        foreach ($types as $type) {
            DB::table('loaicauhoi')->updateOrInsert(
                ['ma' => $type['ma']],
                $type + ['is_active' => 1]
            );

            $ids[$type['ma']] = (int) DB::table('loaicauhoi')
                ->where('ma', $type['ma'])
                ->value('id');
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $types
     * @return array<string, int>
     */
    private function seedQuestions(array $types): array
    {
        $questions = [];

        // 1. Read and Select — độ khó 2
        $questions['read_and_select'] = $this->upsertQuestion(
            $types['read_and_select'],
            'Choose the correct word: The library is a quiet place to ___ for exams.',
            [
                'dokho' => 2,
                'giaithich' => 'Sau "to" dùng động từ nguyên mẫu: to study.',
            ]
        );

        $this->upsertOption($questions['read_and_select'], 1, 'study', true);
        $this->upsertOption($questions['read_and_select'], 2, 'studies', false);
        $this->upsertOption($questions['read_and_select'], 3, 'studying', false);
        $this->upsertOption($questions['read_and_select'], 4, 'studied', false);

        // 2. Fill in the Blanks — độ khó 3
        $questions['fill_in_the_blanks'] = $this->upsertQuestion(
            $types['fill_in_the_blanks'],
            'Fill in the blank: The opposite of "expensive" is ___.',
            [
                'dokho' => 3,
                'giaithich' => 'expensive (đắt) >< cheap (rẻ).',
            ]
        );

        $this->upsertOption($questions['fill_in_the_blanks'], 1, 'cheap', true);
        $this->upsertOption($questions['fill_in_the_blanks'], 2, 'rich', false);
        $this->upsertOption($questions['fill_in_the_blanks'], 3, 'strong', false);
        $this->upsertOption($questions['fill_in_the_blanks'], 4, 'heavy', false);

        // 3. Read and Complete — độ khó 4
        $questions['read_and_complete'] = $this->upsertQuestion(
            $types['read_and_complete'],
            'Read the sentence and choose the best word: Rain forests are important ___ they produce oxygen.',
            [
                'dokho' => 4,
                'giaithich' => 'Hai vế có quan hệ nguyên nhân: because.',
            ]
        );

        $this->upsertOption($questions['read_and_complete'], 1, 'because', true);
        $this->upsertOption($questions['read_and_complete'], 2, 'although', false);
        $this->upsertOption($questions['read_and_complete'], 3, 'unless', false);
        $this->upsertOption($questions['read_and_complete'], 4, 'however', false);

        // 4. Interactive Reading — độ khó 5
        $questions['interactive_reading'] = $this->upsertQuestion(
            $types['interactive_reading'],
            'Read the passage: Many students take notes during lectures. Research shows that writing notes by hand helps them remember more than typing. However, the most important factor is reviewing the notes regularly.',
            ['dokho' => 5]
        );

        $doc1 = $this->upsertDocQuestion(
            $questions['interactive_reading'],
            1,
            'complete_sentence',
            'Students who take notes by hand are more likely to ___.'
        );

        $this->upsertDocOption($doc1, 1, 'remember', true);
        $this->upsertDocOption($doc1, 2, 'forget', false);
        $this->upsertDocOption($doc1, 3, 'sleep', false);
        $this->upsertDocOption($doc1, 4, 'arrive', false);

        $doc2 = $this->upsertDocQuestion(
            $questions['interactive_reading'],
            2,
            'identify_idea',
            'What is the main idea of the passage?'
        );

        $this->upsertDocOption(
            $doc2,
            1,
            'Handwritten notes and regular review help memory.',
            true
        );
        $this->upsertDocOption($doc2, 2, 'Typing is always better than writing.', false);
        $this->upsertDocOption($doc2, 3, 'Students should not take notes.', false);
        $this->upsertDocOption($doc2, 4, 'Lectures are not useful for learning.', false);

        $this->upsertDocQuestion(
            $questions['interactive_reading'],
            3,
            'title_passage',
            'Choose the best title for the passage.',
            'The Value of Note-Taking'
        );

        // 5. Interactive Listening — độ khó 6
        $questions['interactive_listening'] = $this->upsertQuestion(
            $types['interactive_listening'],
            'Listen to a short conversation and answer the questions.',
            [
                'dokho' => 6,
                'audio' => 'audio/questions/listening-1.wav',
            ]
        );

        $listen1 = $this->upsertListenQuestion(
            $questions['interactive_listening'],
            1,
            'listen_and_complete',
            'The woman needs to ___.'
        );

        $this->upsertListenOption($listen1, 1, 'book a room', true);
        $this->upsertListenOption($listen1, 2, 'cancel a flight', false);
        $this->upsertListenOption($listen1, 3, 'buy a ticket', false);
        $this->upsertListenOption($listen1, 4, 'send a package', false);

        $listen2 = $this->upsertListenQuestion(
            $questions['interactive_listening'],
            2,
            'listen_and_respond',
            'What will the man do next?'
        );

        $this->upsertListenOption($listen2, 1, 'check availability', true);
        $this->upsertListenOption($listen2, 2, 'leave the hotel', false);
        $this->upsertListenOption($listen2, 3, 'call a taxi', false);
        $this->upsertListenOption($listen2, 4, 'pay the bill', false);

        $this->upsertListenQuestion(
            $questions['interactive_listening'],
            3,
            'summarise_conversation',
            'Summarise the conversation in one sentence.',
            'The woman books a room for two nights.'
        );

        // 6. Write About the Photo — độ khó 7
        $questions['write_about_photo'] = $this->upsertQuestion(
            $types['write_about_photo'],
            'Write a short description of the photo. Include what you see and what might be happening.',
            [
                'dokho' => 7,
                'anh' => 'images/questions/photo-1.svg',
                'thoigian' => 180,
            ]
        );

        // 7. Read Aloud — độ khó 8
        $questions['read_aloud'] = $this->upsertQuestion(
            $types['read_aloud'],
            'Read the following sentence aloud: "Regular practice is the key to improving your English."',
            [
                'dokho' => 8,
                'thoigian' => 60,
            ]
        );

        // 8. Read Then Speak — độ khó 9
        $questions['read_then_speak'] = $this->upsertQuestion(
            $types['read_then_speak'],
            'Read the short text, then speak about whether you agree with it: "Learning a language requires daily practice."',
            [
                'dokho' => 9,
                'thoigian' => 120,
            ]
        );

        return $questions;
    }

    /**
     * Tạo/cập nhật câu hỏi cha.
     */
    private function upsertQuestion(
        int $loaiId,
        string $noidung,
        array $data = []
    ): int {
        $attributes = [
            'loai_id' => $loaiId,
            'noidung' => $noidung,
        ];

        $values = array_merge([
            'danhmuc_id' => null,
            'giaithich' => null,
            'dokho' => 5,
            'anh' => null,
            'audio' => null,
            'thoigian' => 60,
            'diem' => 1,
            'trangthai' => 'published',
            'nguoitao' => null,
        ], $data);

        DB::table('cauhoi')->updateOrInsert($attributes, $values);

        return (int) DB::table('cauhoi')
            ->where($attributes)
            ->value('id');
    }

    private function upsertOption(
        int $cauhoiId,
        int $thutu,
        string $noidung,
        bool $ladung
    ): void {
        DB::table('luachon')->updateOrInsert(
            [
                'cauhoi_id' => $cauhoiId,
                'thutu' => $thutu,
            ],
            [
                'noidung' => $noidung,
                'ladung' => $ladung,
            ]
        );
    }

    private function upsertDocQuestion(
        int $cauhoiId,
        int $thutu,
        string $loai,
        string $noidung,
        ?string $dapandung = null,
        ?string $giaithich = null
    ): int {
        $attributes = [
            'cauhoi_id' => $cauhoiId,
            'thutu' => $thutu,
        ];

        DB::table('cauhoi_doc')->updateOrInsert($attributes, [
            'loai' => $loai,
            'noidung' => $noidung,
            'dapandung' => $dapandung,
            'giaithich' => $giaithich,
        ]);

        return (int) DB::table('cauhoi_doc')
            ->where($attributes)
            ->value('id');
    }

    private function upsertDocOption(
        int $cauhoiDocId,
        int $thutu,
        string $noidung,
        bool $ladung
    ): void {
        DB::table('luachon_doc')->updateOrInsert(
            [
                'cauhoi_doc_id' => $cauhoiDocId,
                'thutu' => $thutu,
            ],
            [
                'noidung' => $noidung,
                'ladung' => $ladung,
            ]
        );
    }

    private function upsertListenQuestion(
        int $cauhoiId,
        int $thutu,
        string $loai,
        string $noidung,
        ?string $dapandung = null,
        ?string $giaithich = null
    ): int {
        $attributes = [
            'cauhoi_id' => $cauhoiId,
            'thutu' => $thutu,
        ];

        DB::table('cauhoi_nghe')->updateOrInsert($attributes, [
            'loai' => $loai,
            'noidung' => $noidung,
            'dapandung' => $dapandung,
            'giaithich' => $giaithich,
        ]);

        return (int) DB::table('cauhoi_nghe')
            ->where($attributes)
            ->value('id');
    }

    private function upsertListenOption(
        int $cauhoiNgheId,
        int $thutu,
        string $noidung,
        bool $ladung
    ): void {
        DB::table('luachon_nghe')->updateOrInsert(
            [
                'cauhoi_nghe_id' => $cauhoiNgheId,
                'thutu' => $thutu,
            ],
            [
                'noidung' => $noidung,
                'ladung' => $ladung,
            ]
        );
    }

    /**
     * @param  array<string, int>  $questions
     */
    private function seedExam(
        string $ten,
        string $kieu,
        string $mota,
        array $questions
    ): void {
        $attributes = ['ten' => $ten];

        DB::table('dethi')->updateOrInsert($attributes, [
            'mota' => $mota,
            'chedo' => 'full',
            'kieu' => $kieu,
            'kynang' => null,
            'thoigian' => 3600,
            'tongdiem' => count($questions),
            'trangthai' => 'published',
            'nguoitao' => null,
        ]);

        $dethiId = (int) DB::table('dethi')
            ->where($attributes)
            ->value('id');

        // Xóa liên kết cũ để tránh conflict UNIQUE(dethi_id, thutu)
        DB::table('dethicauhoi')->where('dethi_id', $dethiId)->delete();

        $thutu = 1;

        foreach ($questions as $cauhoiId) {
            DB::table('dethicauhoi')->insert([
                'dethi_id' => $dethiId,
                'cauhoi_id' => $cauhoiId,
                'thutu' => $thutu,
                'diem' => 1,
            ]);

            $thutu++;
        }
    }
}
