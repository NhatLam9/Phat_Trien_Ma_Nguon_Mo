<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gói ngân hàng câu hỏi vận hành tối thiểu.
 *
 * Kết quả:
 * - 120 câu cha.
 * - 60 câu con (30 đọc + 30 nghe).
 * - 5 đề full, mỗi đề 20 câu cha + 9 câu con.
 *
 * Đây là dữ liệu seed để chạy thử/demo. Trước khi dùng chính thức,
 * giáo viên vẫn cần rà soát nội dung, đáp án và audio.
 */
class OperationalQuestionBankSeeder extends Seeder
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
                        "Bỏ qua OperationalQuestionBankSeeder: thiếu bảng {$table}."
                    );
                }

                return;
            }
        }

        $data = require database_path('seeders/data/operational_questions.php');

        DB::transaction(function () use ($data): void {
            $types = $this->seedQuestionTypes();
            $questionIds = $this->seedQuestions($data, $types);

            $this->seedExams($questionIds);
        });

        if ($this->command) {
            $this->command->info(
                'Đã seed ngân hàng vận hành: 120 câu cha, 60 câu con, 5 đề full.'
            );

            $this->command->table(
                ['ID', 'Tên đề', 'Số câu', 'Thời gian', 'Trạng thái'],
                DB::table('dethi')
                    ->where('ten', 'like', 'DET Mock Test - Operational %')
                    ->orderBy('ten')
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
     * @param  array<string, array<int, array<string, mixed>>>  $data
     * @param  array<string, int>  $types
     * @return array<string, array<int, int>>
     */
    private function seedQuestions(array $data, array $types): array
    {
        $ids = [
            'read_and_select' => [],
            'fill_in_the_blanks' => [],
            'read_and_complete' => [],
            'interactive_reading' => [],
            'interactive_listening' => [],
            'write_about_photo' => [],
            'read_aloud' => [],
            'read_then_speak' => [],
        ];

        foreach ($data['read_and_select'] as $row) {
            $ids['read_and_select'][] = $this->taoCauHoiTracNghiem(
                $types['read_and_select'],
                $row['noidung'],
                $row['options'],
                [
                    'giaithich' => $row['giaithich'] ?? null,
                    'dokho' => $row['dokho'] ?? 5,
                    'thoigian' => 60,
                ]
            );
        }

        foreach ($data['fill_in_the_blanks'] as $row) {
            $ids['fill_in_the_blanks'][] = $this->taoCauHoiTracNghiem(
                $types['fill_in_the_blanks'],
                $row['noidung'],
                $row['options'],
                [
                    'giaithich' => $row['giaithich'] ?? null,
                    'dokho' => $row['dokho'] ?? 5,
                    'thoigian' => 90,
                ]
            );
        }

        foreach ($data['read_and_complete'] as $row) {
            $ids['read_and_complete'][] = $this->taoCauHoiTracNghiem(
                $types['read_and_complete'],
                $row['noidung'],
                $row['options'],
                [
                    'giaithich' => $row['giaithich'] ?? null,
                    'dokho' => $row['dokho'] ?? 5,
                    'thoigian' => 90,
                ]
            );
        }

        foreach ($data['interactive_reading'] as $row) {
            $questionId = $this->taoCauHoi(
                $types['interactive_reading'],
                $row['noidung'],
                [
                    'dokho' => $row['dokho'] ?? 5,
                    'thoigian' => 120,
                ]
            );

            $this->xoaCauHoiConDoc($questionId);

            foreach (array_values($row['children']) as $index => $child) {
                $this->taoDocCauHoi(
                    $questionId,
                    $index + 1,
                    $child['loai'],
                    $child['noidung'],
                    $child['options'] ?? [],
                    $child['dapandung'] ?? null,
                    $child['giaithich'] ?? null
                );
            }

            $ids['interactive_reading'][] = $questionId;
        }

        foreach ($data['interactive_listening'] as $row) {
            $questionId = $this->taoCauHoi(
                $types['interactive_listening'],
                $row['noidung'],
                [
                    'dokho' => $row['dokho'] ?? 5,
                    'audio' => $row['audio'] ?? null,
                    'thoigian' => 120,
                ]
            );

            $this->xoaCauHoiConNghe($questionId);

            foreach (array_values($row['children']) as $index => $child) {
                $this->taoNgheCauHoi(
                    $questionId,
                    $index + 1,
                    $child['loai'],
                    $child['noidung'],
                    $child['options'] ?? [],
                    $child['dapandung'] ?? null,
                    $child['giaithich'] ?? null
                );
            }

            $ids['interactive_listening'][] = $questionId;
        }

        foreach ($data['write_about_photo'] as $row) {
            $ids['write_about_photo'][] = $this->taoCauHoi(
                $types['write_about_photo'],
                $row['noidung'],
                [
                    'dokho' => $row['dokho'] ?? 7,
                    'anh' => $row['anh'] ?? null,
                    'thoigian' => 180,
                ]
            );
        }

        foreach ($data['read_aloud'] as $row) {
            $ids['read_aloud'][] = $this->taoCauHoi(
                $types['read_aloud'],
                $row['noidung'],
                [
                    'dokho' => $row['dokho'] ?? 6,
                    'thoigian' => 60,
                ]
            );
        }

        foreach ($data['read_then_speak'] as $row) {
            $ids['read_then_speak'][] = $this->taoCauHoi(
                $types['read_then_speak'],
                $row['noidung'],
                [
                    'dokho' => $row['dokho'] ?? 8,
                    'thoigian' => 120,
                ]
            );
        }

        return $ids;
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

        DB::table('luachon')->where('cauhoi_id', $cauhoiId)->delete();

        foreach ($options as $index => [$option, $correct]) {
            DB::table('luachon')->insert([
                'cauhoi_id' => $cauhoiId,
                'noidung' => $option,
                'ladung' => $correct,
                'thutu' => $index + 1,
            ]);
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

    private function xoaCauHoiConDoc(int $cauhoiId): void
    {
        $ids = DB::table('cauhoi_doc')
            ->where('cauhoi_id', $cauhoiId)
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('luachon_doc')->whereIn('cauhoi_doc_id', $ids)->delete();
            DB::table('cauhoi_doc')->whereIn('id', $ids)->delete();
        }
    }

    private function xoaCauHoiConNghe(int $cauhoiId): void
    {
        $ids = DB::table('cauhoi_nghe')
            ->where('cauhoi_id', $cauhoiId)
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('luachon_nghe')->whereIn('cauhoi_nghe_id', $ids)->delete();
            DB::table('cauhoi_nghe')->whereIn('id', $ids)->delete();
        }
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
    ): void {
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

        DB::table('luachon_doc')->where('cauhoi_doc_id', $cauhoiDocId)->delete();

        foreach ($options as $index => [$option, $correct]) {
            DB::table('luachon_doc')->insert([
                'cauhoi_doc_id' => $cauhoiDocId,
                'noidung' => $option,
                'ladung' => $correct,
                'thutu' => $index + 1,
            ]);
        }
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
    ): void {
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

        DB::table('luachon_nghe')->where('cauhoi_nghe_id', $cauhoiNgheId)->delete();

        foreach ($options as $index => [$option, $correct]) {
            DB::table('luachon_nghe')->insert([
                'cauhoi_nghe_id' => $cauhoiNgheId,
                'noidung' => $option,
                'ladung' => $correct,
                'thutu' => $index + 1,
            ]);
        }
    }

    /**
     * @param  array<string, array<int, int>>  $ids
     */
    private function seedExams(array $ids): void
    {
        for ($examIndex = 0; $examIndex < 5; $examIndex++) {
            $questions = array_merge(
                array_slice($ids['read_and_select'], $examIndex * 5, 5),
                array_slice($ids['fill_in_the_blanks'], $examIndex * 5, 5),
                array_slice($ids['read_and_complete'], $examIndex * 2, 2),
                [$ids['interactive_reading'][$examIndex]],
                array_slice($ids['interactive_listening'], $examIndex * 2, 2),
                array_slice($ids['write_about_photo'], $examIndex * 2, 2),
                array_slice($ids['read_aloud'], $examIndex * 2, 2),
                [$ids['read_then_speak'][$examIndex]]
            );

            if (count($questions) !== 20) {
                throw new \RuntimeException('Đề vận hành phải có đúng 20 câu hỏi.');
            }

            $ten = sprintf('DET Mock Test - Operational %02d', $examIndex + 1);

            DB::table('dethi')->updateOrInsert(
                ['ten' => $ten],
                [
                    'mota' => 'Đề vận hành tối thiểu: 20 câu cha, 9 câu con, đủ 4 kỹ năng.',
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
}
