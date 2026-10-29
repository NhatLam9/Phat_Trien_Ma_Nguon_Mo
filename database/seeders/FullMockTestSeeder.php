<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dữ liệu mẫu cho một đề Mock Test hoàn chỉnh (TV3).
 *
 * Cấu trúc: 20 câu cha + 9 câu con.
 * - 5 read_and_select
 * - 5 fill_in_the_blanks
 * - 2 read_and_complete
 * - 1 interactive_reading (3 câu con)
 * - 2 interactive_listening (6 câu con)
 * - 2 write_about_photo
 * - 2 read_aloud
 * - 1 read_then_speak
 *
 * Seeder chỉ ghi vào các bảng ngân hàng câu hỏi/đề thi của TV3.
 * Không ghi vào lanthi/lanthicauhoi/traloi của TV2.
 */
class FullMockTestSeeder extends Seeder
{
    /**
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
                        "Bỏ qua FullMockTestSeeder: thiếu bảng {$table}."
                    );
                }

                return;
            }
        }

        DB::transaction(function (): void {
            $types = $this->seedQuestionTypes();
            $questions = $this->seedQuestions($types);

            $this->seedExam($questions);
        });

        if ($this->command) {
            $this->command->info(
                'Đã seed đề Mock Test hoàn chỉnh: DET Mock Test - Full 01.'
            );

            $this->command->table(
                ['ID', 'Tên đề', 'Số câu', 'Thời gian', 'Trạng thái'],
                DB::table('dethi')
                    ->where('ten', 'DET Mock Test - Full 01')
                    ->orderBy('id')
                    ->get(['id', 'ten', 'thoigian', 'trangthai'])
                    ->map(fn ($exam) => [
                        $exam->id,
                        $exam->ten,
                        DB::table('dethicauhoi')
                            ->where('dethi_id', $exam->id)
                            ->count(),
                        $exam->thoigian.' giây',
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
     * @return array<int, int>
     */
    private function seedQuestions(array $types): array
    {
        $questions = [];

        // 1-5. Read and Select
        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_select'],
            'The museum is free on Sundays, so many families ___ there.',
            [
                ['visit', true],
                ['visits', false],
                ['visited', false],
                ['visiting', false],
            ],
            [
                'dokho' => 2,
                'thoigian' => 60,
                'giaithich' => 'Chủ ngữ "many families" số nhiều nên dùng "visit".',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_select'],
            'If you want to improve your writing, you should ___ every day.',
            [
                ['practice', true],
                ['practices', false],
                ['practicing', false],
                ['practiced', false],
            ],
            [
                'dokho' => 3,
                'thoigian' => 60,
                'giaithich' => 'Sau "should" dùng động từ nguyên mẫu: should practice.',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_select'],
            'The train was delayed ___ heavy snow.',
            [
                ['because of', true],
                ['because', false],
                ['although', false],
                ['so', false],
            ],
            [
                'dokho' => 4,
                'thoigian' => 60,
                'giaithich' => 'Trước cụm danh từ "heavy snow" dùng "because of".',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_select'],
            'She spoke ___ enough for everyone in the room to hear.',
            [
                ['loudly', true],
                ['loud', false],
                ['louder', false],
                ['loudness', false],
            ],
            [
                'dokho' => 5,
                'thoigian' => 60,
                'giaithich' => 'Cần trạng từ "loudly" bổ nghĩa cho động từ "spoke".',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_select'],
            'By the time we arrived, the concert ___.',
            [
                ['had started', true],
                ['has started', false],
                ['starts', false],
                ['was starting', false],
            ],
            [
                'dokho' => 6,
                'thoigian' => 60,
                'giaithich' => 'Hành động xảy ra trước một hành động quá khứ khác dùng quá khứ hoàn thành.',
            ]
        );

        // 6-10. Fill in the Blanks
        $questions[] = $this->taoCauHoiTracNghiem(
            $types['fill_in_the_blanks'],
            'The opposite of "ancient" is ___.',
            [
                ['modern', true],
                ['old', false],
                ['historic', false],
                ['early', false],
            ],
            [
                'dokho' => 2,
                'thoigian' => 90,
                'giaithich' => 'ancient (cổ xưa) >< modern (hiện đại).',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['fill_in_the_blanks'],
            'Please ___ the lights before you leave the room.',
            [
                ['turn off', true],
                ['turn on', false],
                ['turn up', false],
                ['turn down', false],
            ],
            [
                'dokho' => 3,
                'thoigian' => 90,
                'giaithich' => 'turn off = tắt (đèn, thiết bị).',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['fill_in_the_blanks'],
            'The company plans to ___ a new product next month.',
            [
                ['launch', true],
                ['land', false],
                ['lend', false],
                ['lift', false],
            ],
            [
                'dokho' => 4,
                'thoigian' => 90,
                'giaithich' => 'launch a product = ra mắt sản phẩm.',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['fill_in_the_blanks'],
            'Her explanation was so ___ that everyone understood it immediately.',
            [
                ['clear', true],
                ['clearer', false],
                ['clearly', false],
                ['clarity', false],
            ],
            [
                'dokho' => 5,
                'thoigian' => 90,
                'giaithich' => 'Sau "was so" cần tính từ: so clear that...',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['fill_in_the_blanks'],
            'The scientist\'s findings were ___ by other researchers.',
            [
                ['confirmed', true],
                ['confirmation', false],
                ['confirming', false],
                ['confirm', false],
            ],
            [
                'dokho' => 6,
                'thoigian' => 90,
                'giaithich' => 'Câu bị động "were confirmed" (đã được xác nhận).',
            ]
        );

        // 11-12. Read and Complete
        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_complete'],
            'Cities are becoming hotter because they have fewer trees and more concrete. One solution is to create rooftop gardens. These gardens ___ buildings cool and provide habitats for birds.',
            [
                ['keep', true],
                ['keeps', false],
                ['keeping', false],
                ['kept', false],
            ],
            [
                'dokho' => 5,
                'thoigian' => 90,
                'giaithich' => 'Chủ ngữ "These gardens" số nhiều nên dùng "keep".',
            ]
        );

        $questions[] = $this->taoCauHoiTracNghiem(
            $types['read_and_complete'],
            'Many people buy bottled water because they believe it is safer than tap water. However, studies show that tap water in many cities is ___ tested and just as safe.',
            [
                ['carefully', true],
                ['careful', false],
                ['care', false],
                ['careless', false],
            ],
            [
                'dokho' => 6,
                'thoigian' => 90,
                'giaithich' => 'Cần trạng từ "carefully" bổ nghĩa cho động từ "tested".',
            ]
        );

        // 13. Interactive Reading
        $interactiveReading = $this->taoCauHoi(
            $types['interactive_reading'],
            'Read the passage: The Great Barrier Reef is the world\'s largest coral reef system, stretching more than 2,300 kilometres off the coast of Australia. It is home to thousands of species, but rising ocean temperatures have caused widespread coral bleaching. Scientists are testing new ways to protect the reef, including breeding heat-resistant corals and reducing pollution from nearby farms.',
            [
                'dokho' => 5,
                'thoigian' => 120,
            ]
        );

        $doc1 = $this->taoDocCauHoi(
            $interactiveReading,
            1,
            'complete_sentence',
            'The Great Barrier Reef is located off the coast of ___.',
            [
                ['Australia', true],
                ['Brazil', false],
                ['Canada', false],
                ['Japan', false],
            ],
            null,
            'Thông tin có trong câu đầu tiên của đoạn văn.'
        );

        $this->taoDocCauHoi(
            $interactiveReading,
            2,
            'identify_idea',
            'What is the main idea of the passage?',
            [
                ['The reef is threatened, but scientists are trying to protect it.', true],
                ['The reef is the largest in the world and has no problems.', false],
                ['Tourism is the only cause of coral bleaching.', false],
                ['Scientists have stopped all coral bleaching.', false],
            ],
            null,
            'Đoạn văn nêu nguy cơ từ nhiệt độ tăng và các biện pháp bảo vệ.'
        );

        $this->taoDocCauHoi(
            $interactiveReading,
            3,
            'title_passage',
            'Choose the best title for the passage.',
            [],
            'Protecting the Great Barrier Reef',
            'Tiêu đề khái quát nội dung bảo vệ rạn san hô.'
        );

        $questions[] = $interactiveReading;

        // 14. Interactive Listening 1
        $listening1 = $this->taoCauHoi(
            $types['interactive_listening'],
            'Listen to a conversation at a hotel and answer the questions.',
            [
                'dokho' => 6,
                'thoigian' => 120,
                'audio' => 'audio/questions/listening-2.wav',
            ]
        );

        $this->taoNgheCauHoi(
            $listening1,
            1,
            'listen_and_complete',
            'The woman wants to book a room for ___.',
            [
                ['two nights', true],
                ['three nights', false],
                ['one week', false],
                ['two weeks', false],
            ],
            null,
            'Người phụ nữ nói "for two nights".'
        );

        $this->taoNgheCauHoi(
            $listening1,
            2,
            'listen_and_respond',
            'What does the man say about breakfast?',
            [
                ['It is included in the price.', true],
                ['It costs extra.', false],
                ['It is not available.', false],
                ['It is served in the evening.', false],
            ],
            null,
            'Người đàn ông nói giá phòng đã bao gồm bữa sáng.'
        );

        $this->taoNgheCauHoi(
            $listening1,
            3,
            'summarise_conversation',
            'Summarise the conversation in one sentence.',
            [],
            'The woman books a double room for two nights, and breakfast is included.',
            'Tóm tắt cần nêu loại phòng, số đêm và bữa sáng.'
        );

        $questions[] = $listening1;

        // 15. Interactive Listening 2
        $listening2 = $this->taoCauHoi(
            $types['interactive_listening'],
            'Listen to a phone call about a part-time job and answer the questions.',
            [
                'dokho' => 7,
                'thoigian' => 120,
                'audio' => 'audio/questions/listening-3.wav',
            ]
        );

        $this->taoNgheCauHoi(
            $listening2,
            1,
            'listen_and_complete',
            'The man is applying for a ___ job.',
            [
                ['part-time', true],
                ['full-time', false],
                ['summer', false],
                ['volunteer', false],
            ],
            null,
            'Người đàn ông nói "part-time library assistant".'
        );

        $this->taoNgheCauHoi(
            $listening2,
            2,
            'listen_and_respond',
            'When can the man start?',
            [
                ['next Monday', true],
                ['next Sunday', false],
                ['next month', false],
                ['tomorrow', false],
            ],
            null,
            'Người đàn ông xác nhận có thể bắt đầu vào thứ Hai tới.'
        );

        $this->taoNgheCauHoi(
            $listening2,
            3,
            'summarise_conversation',
            'Summarise the conversation in one sentence.',
            [],
            'The man applies for a part-time library job and can start next Monday.',
            'Tóm tắt cần nêu công việc bán thời gian và thời gian bắt đầu.'
        );

        $questions[] = $listening2;

        // 16-17. Write About the Photo
        $questions[] = $this->taoCauHoi(
            $types['write_about_photo'],
            'Write a short description of the photo. Include what you see and what might be happening.',
            [
                'dokho' => 7,
                'thoigian' => 180,
                'anh' => 'images/questions/photo-2.svg',
            ]
        );

        $questions[] = $this->taoCauHoi(
            $types['write_about_photo'],
            'Describe the scene in the photo. What are the people doing, and how do they feel?',
            [
                'dokho' => 8,
                'thoigian' => 180,
                'anh' => 'images/questions/photo-3.svg',
            ]
        );

        // 18-19. Read Aloud
        $questions[] = $this->taoCauHoi(
            $types['read_aloud'],
            'Read the following sentence aloud: "Small, consistent steps often lead to the biggest improvements."',
            [
                'dokho' => 6,
                'thoigian' => 60,
            ]
        );

        $questions[] = $this->taoCauHoi(
            $types['read_aloud'],
            'Read the following paragraph aloud: "Learning a new language takes time, patience, and regular practice. Mistakes are a natural part of the process, so learners should focus on progress rather than perfection."',
            [
                'dokho' => 7,
                'thoigian' => 90,
            ]
        );

        // 20. Read Then Speak
        $questions[] = $this->taoCauHoi(
            $types['read_then_speak'],
            'Read the short text, then speak about whether you agree with it: "Students should learn a second language before they finish high school."',
            [
                'dokho' => 9,
                'thoigian' => 120,
            ]
        );

        return $questions;
    }

    /**
     * @param  array<int, array{0: string, 1: bool}>  $options
     */
    private function taoCauHoiTracNghiem(
        int $loaiId,
        string $noidung,
        array $options,
        array $data = []
    ): int {
        $cauhoiId = $this->taoCauHoi($loaiId, $noidung, $data);

        foreach ($options as $index => [$option, $correct]) {
            DB::table('luachon')->updateOrInsert(
                [
                    'cauhoi_id' => $cauhoiId,
                    'thutu' => $index + 1,
                ],
                [
                    'noidung' => $option,
                    'ladung' => $correct,
                ]
            );
        }

        return $cauhoiId;
    }

    private function taoCauHoi(
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

    /**
     * @param  array<int, array{0: string, 1: bool}>  $options
     */
    private function taoDocCauHoi(
        int $cauhoiId,
        int $thutu,
        string $loai,
        string $noidung,
        array $options = [],
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

        $cauhoiDocId = (int) DB::table('cauhoi_doc')
            ->where($attributes)
            ->value('id');

        foreach ($options as $index => [$option, $correct]) {
            DB::table('luachon_doc')->updateOrInsert(
                [
                    'cauhoi_doc_id' => $cauhoiDocId,
                    'thutu' => $index + 1,
                ],
                [
                    'noidung' => $option,
                    'ladung' => $correct,
                ]
            );
        }

        return $cauhoiDocId;
    }

    /**
     * @param  array<int, array{0: string, 1: bool}>  $options
     */
    private function taoNgheCauHoi(
        int $cauhoiId,
        int $thutu,
        string $loai,
        string $noidung,
        array $options = [],
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

        $cauhoiNgheId = (int) DB::table('cauhoi_nghe')
            ->where($attributes)
            ->value('id');

        foreach ($options as $index => [$option, $correct]) {
            DB::table('luachon_nghe')->updateOrInsert(
                [
                    'cauhoi_nghe_id' => $cauhoiNgheId,
                    'thutu' => $index + 1,
                ],
                [
                    'noidung' => $option,
                    'ladung' => $correct,
                ]
            );
        }

        return $cauhoiNgheId;
    }

    /**
     * @param  array<int, int>  $questions
     */
    private function seedExam(array $questions): void
    {
        $ten = 'DET Mock Test - Full 01';

        DB::table('dethi')->updateOrInsert(
            ['ten' => $ten],
            [
                'mota' => 'Đề Mock Test hoàn chỉnh: 20 câu cha, 9 câu con, 4 kỹ năng.',
                'chedo' => 'full',
                'kieu' => 'fixed',
                'kynang' => null,
                'thoigian' => 3600,
                'tongdiem' => count($questions),
                'trangthai' => 'published',
                'nguoitao' => null,
            ]
        );

        $dethiId = (int) DB::table('dethi')
            ->where('ten', $ten)
            ->value('id');

        DB::table('dethicauhoi')
            ->where('dethi_id', $dethiId)
            ->delete();

        foreach ($questions as $index => $cauhoiId) {
            DB::table('dethicauhoi')->insert([
                'dethi_id' => $dethiId,
                'cauhoi_id' => $cauhoiId,
                'thutu' => $index + 1,
                'diem' => 1,
            ]);
        }
    }
}
