<?php

namespace App\Services;

use App\Models\Cauhoi;
use App\Models\CauhoiDoc;
use App\Models\CauhoiNghe;
use App\Models\Danhmuc;
use App\Models\Dethi;
use App\Models\Dethicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Luachon;
use App\Models\LuachonDoc;
use App\Models\LuachonNghe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class QuestionImportService
{
    /**
     * Các dạng câu hỏi bắt buộc có lựa chọn và đúng một đáp án.
     *
     * @var array<int, string>
     */
    private const AUTO_TYPES = [
        'read_and_select',
        'fill_in_the_blanks',
        'read_and_complete',
    ];

    /**
     * @var array<int, string>
     */
    private const DOC_CHILD_TYPES = [
        'complete_sentence',
        'complete_passage',
        'highlight_answer',
        'identify_idea',
        'title_passage',
    ];

    /**
     * @var array<int, string>
     */
    private const LISTEN_CHILD_TYPES = [
        'listen_and_complete',
        'listen_and_respond',
        'summarise_conversation',
    ];

    /**
     * Phân tích và kiểm tra file JSON trước khi ghi database.
     *
     * @return array{
     *     questions: array<int, array<string, mixed>>,
     *     exam: array<string, mixed>|null,
     *     summary: array<string, mixed>,
     *     errors: array<string, array<int, string>>,
     *     warnings: array<int, string>
     * }
     */
    public function prepare(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->failure(
                'File JSON không hợp lệ: '.json_last_error_msg()
            );
        }

        if (! is_array($data)) {
            return $this->failure('Nội dung file phải là một JSON object.');
        }

        $rawQuestions = $data['questions'] ?? null;

        if (! is_array($rawQuestions) || $rawQuestions === []) {
            return $this->failure(
                'File JSON phải có mảng "questions" và không được rỗng.'
            );
        }

        $types = Loaicauhoi::query()->get()->keyBy('ma');
        $categories = Danhmuc::query()->pluck('id', 'ten');

        $errors = [];
        $warnings = [];
        $questions = [];
        $seenMa = [];

        foreach ($rawQuestions as $index => $row) {
            $prefix = 'questions.'.$index;

            if (! is_array($row)) {
                $errors[$prefix][] = 'Mỗi câu hỏi phải là một object.';

                continue;
            }

            $ma = trim((string) ($row['ma'] ?? 'q-'.($index + 1)));

            if ($ma === '') {
                $ma = 'q-'.($index + 1);
            }

            if (isset($seenMa[$ma])) {
                $errors[$prefix.'.ma'][] = "Mã câu hỏi \"{$ma}\" bị trùng.";

                continue;
            }

            $seenMa[$ma] = true;

            $loaiMa = trim((string) ($row['loai'] ?? ''));

            if ($loaiMa === '') {
                $errors[$prefix.'.loai'][] = 'Thiếu mã loại câu hỏi.';

                continue;
            }

            /** @var Loaicauhoi|null $type */
            $type = $types->get($loaiMa);

            if (! $type) {
                $errors[$prefix.'.loai'][] = "Không tìm thấy loại câu hỏi \"{$loaiMa}\".";

                continue;
            }

            $noidung = trim((string) ($row['noidung'] ?? ''));

            if ($noidung === '') {
                $errors[$prefix.'.noidung'][] = 'Nội dung câu hỏi không được rỗng.';

                continue;
            }

            $dokho = filter_var(
                $row['dokho'] ?? 5,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 10]]
            );

            if ($dokho === false) {
                $errors[$prefix.'.dokho'][] = 'Độ khó phải là số nguyên từ 1 đến 10.';
                $dokho = 5;
            }

            $trangthai = (string) ($row['trangthai'] ?? 'draft');

            if (! in_array($trangthai, ['draft', 'review', 'published', 'archived'], true)) {
                $errors[$prefix.'.trangthai'][] = 'Trạng thái không hợp lệ.';
                $trangthai = 'draft';
            }

            $thoigian = $row['thoigian'] ?? $type->thoigian ?? 60;
            $thoigian = filter_var(
                $thoigian,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 86400]]
            );

            if ($thoigian === false) {
                $errors[$prefix.'.thoigian'][] = 'Thời gian phải là số nguyên từ 1 đến 86400 giây.';
                $thoigian = 60;
            }

            $diem = $row['diem'] ?? 1;

            if (! is_numeric($diem) || (float) $diem < 0) {
                $errors[$prefix.'.diem'][] = 'Điểm phải là số không âm.';
                $diem = 1;
            }

            $anh = $this->normalizeMediaPath(
                $row['anh'] ?? null,
                $prefix.'.anh',
                $errors,
                $warnings
            );

            $audio = $this->normalizeMediaPath(
                $row['audio'] ?? null,
                $prefix.'.audio',
                $errors,
                $warnings
            );

            $danhmucTen = trim((string) ($row['danhmuc'] ?? ''));
            $danhmucId = null;

            if ($danhmucTen !== '') {
                if ($categories->has($danhmucTen)) {
                    $danhmucId = (int) $categories->get($danhmucTen);
                } else {
                    $warnings[] = "Danh mục \"{$danhmucTen}\" chưa có, sẽ được tạo mới khi import.";
                }
            }

            $luachon = $this->normalizeOptions(
                $row['luachon'] ?? [],
                $prefix.'.luachon',
                $errors
            );

            $this->validateOptionsForType(
                $loaiMa,
                $luachon,
                $prefix.'.luachon',
                $errors
            );

            $cauhoiDoc = $this->normalizeChildren(
                $row['cauhoi_doc'] ?? [],
                'doc',
                $prefix.'.cauhoi_doc',
                $errors,
                $warnings
            );

            $cauhoiNghe = $this->normalizeChildren(
                $row['cauhoi_nghe'] ?? [],
                'nghe',
                $prefix.'.cauhoi_nghe',
                $errors,
                $warnings
            );

            if ($loaiMa === 'interactive_reading' && $cauhoiDoc === []) {
                $warnings[] = "Câu \"{$ma}\" là Interactive Reading nhưng chưa có câu hỏi con.";
            }

            if ($loaiMa === 'interactive_listening') {
                if ($audio === null) {
                    $warnings[] = "Câu \"{$ma}\" là Interactive Listening nhưng chưa có audio.";
                }

                if ($cauhoiNghe === []) {
                    $warnings[] = "Câu \"{$ma}\" là Interactive Listening nhưng chưa có câu hỏi con.";
                }
            }

            $questions[] = [
                'ma' => $ma,
                'loai_ma' => $loaiMa,
                'loai_ten' => $type->ten,
                'loai_id' => (int) $type->id,
                'danhmuc_id' => $danhmucId,
                'danhmuc_ten' => $danhmucTen,
                'noidung' => $noidung,
                'giaithich' => $this->nullableString($row['giaithich'] ?? null),
                'dokho' => (int) $dokho,
                'anh' => $anh,
                'audio' => $audio,
                'thoigian' => (int) $thoigian,
                'diem' => (float) $diem,
                'trangthai' => $trangthai,
                'luachon' => $luachon,
                'cauhoi_doc' => $cauhoiDoc,
                'cauhoi_nghe' => $cauhoiNghe,
            ];
        }

        $exam = $this->normalizeExam($data['exam'] ?? null, $questions, $errors);

        return [
            'questions' => $questions,
            'exam' => $exam,
            'summary' => $this->summary($questions, $exam),
            'errors' => $errors,
            'warnings' => array_values(array_unique($warnings)),
        ];
    }

    /**
     * Ghi dữ liệu đã chuẩn hóa vào database trong một transaction.
     *
     * @param  array<string, mixed>  $prepared
     * @return array<string, mixed>
     */
    public function import(array $prepared, int $nguoiTaoId): array
    {
        if (! empty($prepared['errors'])) {
            throw new InvalidArgumentException('File import còn lỗi, không thể ghi database.');
        }

        return DB::transaction(function () use ($prepared, $nguoiTaoId): array {
            $stats = [
                'questions_created' => 0,
                'questions_updated' => 0,
                'options' => 0,
                'reading_children' => 0,
                'listening_children' => 0,
                'categories_created' => 0,
            ];

            $questionIds = [];
            $questionDiem = [];

            foreach ($prepared['questions'] as $questionData) {
                $danhmucId = null;

                if ($questionData['danhmuc_ten'] !== '') {
                    $danhmuc = Danhmuc::firstOrCreate(
                        ['ten' => $questionData['danhmuc_ten']],
                        ['mota' => null]
                    );

                    if ($danhmuc->wasRecentlyCreated) {
                        $stats['categories_created']++;
                    }

                    $danhmucId = $danhmuc->id;
                }

                $question = Cauhoi::updateOrCreate(
                    [
                        'loai_id' => $questionData['loai_id'],
                        'noidung' => $questionData['noidung'],
                    ],
                    [
                        'danhmuc_id' => $danhmucId,
                        'giaithich' => $questionData['giaithich'],
                        'dokho' => $questionData['dokho'],
                        'anh' => $questionData['anh'],
                        'audio' => $questionData['audio'],
                        'thoigian' => $questionData['thoigian'],
                        'diem' => $questionData['diem'],
                        'trangthai' => $questionData['trangthai'],
                        'nguoitao' => $nguoiTaoId,
                    ]
                );

                if ($question->wasRecentlyCreated) {
                    $stats['questions_created']++;
                } else {
                    $stats['questions_updated']++;
                }

                $question->luachon()->delete();
                $question->cauhoiDoc()->delete();
                $question->cauhoiNghe()->delete();

                foreach ($questionData['luachon'] as $option) {
                    Luachon::create([
                        'cauhoi_id' => $question->id,
                        'noidung' => $option['noidung'],
                        'ladung' => $option['ladung'],
                        'thutu' => $option['thutu'],
                    ]);
                    $stats['options']++;
                }

                foreach ($questionData['cauhoi_doc'] as $childData) {
                    $child = CauhoiDoc::create([
                        'cauhoi_id' => $question->id,
                        'loai' => $childData['loai'],
                        'noidung' => $childData['noidung'],
                        'dapandung' => $childData['dapandung'],
                        'giaithich' => $childData['giaithich'],
                        'thutu' => $childData['thutu'],
                    ]);

                    foreach ($childData['luachon'] as $option) {
                        LuachonDoc::create([
                            'cauhoi_doc_id' => $child->id,
                            'noidung' => $option['noidung'],
                            'ladung' => $option['ladung'],
                            'thutu' => $option['thutu'],
                        ]);
                        $stats['options']++;
                    }

                    $stats['reading_children']++;
                }

                foreach ($questionData['cauhoi_nghe'] as $childData) {
                    $child = CauhoiNghe::create([
                        'cauhoi_id' => $question->id,
                        'loai' => $childData['loai'],
                        'noidung' => $childData['noidung'],
                        'dapandung' => $childData['dapandung'],
                        'giaithich' => $childData['giaithich'],
                        'thutu' => $childData['thutu'],
                    ]);

                    foreach ($childData['luachon'] as $option) {
                        LuachonNghe::create([
                            'cauhoi_nghe_id' => $child->id,
                            'noidung' => $option['noidung'],
                            'ladung' => $option['ladung'],
                            'thutu' => $option['thutu'],
                        ]);
                        $stats['options']++;
                    }

                    $stats['listening_children']++;
                }

                $questionIds[$questionData['ma']] = $question->id;
                $questionDiem[$questionData['ma']] = (float) $questionData['diem'];
            }

            $examResult = null;

            if ($prepared['exam']) {
                $examData = $prepared['exam'];

                $exam = Dethi::updateOrCreate(
                    ['ten' => $examData['ten']],
                    [
                        'mota' => $examData['mota'],
                        'chedo' => $examData['chedo'],
                        'kieu' => $examData['kieu'],
                        'kynang' => $examData['kynang'],
                        'thoigian' => $examData['thoigian'],
                        'tongdiem' => $examData['tongdiem'],
                        'trangthai' => $examData['trangthai'],
                        'nguoitao' => $nguoiTaoId,
                    ]
                );

                Dethicauhoi::query()
                    ->where('dethi_id', $exam->id)
                    ->delete();

                foreach ($examData['questions'] as $index => $ma) {
                    Dethicauhoi::create([
                        'dethi_id' => $exam->id,
                        'cauhoi_id' => $questionIds[$ma],
                        'thutu' => $index + 1,
                        'diem' => $questionDiem[$ma],
                    ]);
                }

                $examResult = [
                    'id' => $exam->id,
                    'ten' => $exam->ten,
                    'questions' => count($examData['questions']),
                ];
            }

            return [
                'stats' => $stats,
                'exam' => $examResult,
            ];
        }, 3);
    }

    /**
     * @param  array<int, mixed>  $input
     * @param  array<string, array<int, string>>  $errors
     * @return array<int, array{noidung: string, ladung: bool, thutu: int}>
     */
    private function normalizeOptions(mixed $input, string $prefix, array &$errors): array
    {
        if (! is_array($input)) {
            $errors[$prefix][] = 'Trường lựa chọn phải là một mảng.';

            return [];
        }

        $options = [];

        foreach (array_values($input) as $index => $option) {
            $thutu = $index + 1;

            if (is_string($option)) {
                $options[] = [
                    'noidung' => trim($option),
                    'ladung' => false,
                    'thutu' => $thutu,
                ];

                continue;
            }

            if (! is_array($option)) {
                $errors[$prefix.'.'.$index][] = 'Lựa chọn phải là string hoặc object.';

                continue;
            }

            $noidung = trim((string) ($option['noidung'] ?? ''));

            if ($noidung === '') {
                $errors[$prefix.'.'.$index.'.noidung'][] = 'Nội dung lựa chọn không được rỗng.';

                continue;
            }

            $options[] = [
                'noidung' => $noidung,
                'ladung' => $this->toBoolean($option['ladung'] ?? false),
                'thutu' => $thutu,
            ];
        }

        return $options;
    }

    /**
     * @param  array<int, array{noidung: string, ladung: bool, thutu: int}>  $options
     * @param  array<string, array<int, string>>  $errors
     */
    private function validateOptionsForType(
        string $loaiMa,
        array $options,
        string $prefix,
        array &$errors
    ): void {
        $correct = count(array_filter($options, fn (array $option): bool => $option['ladung']));

        if (in_array($loaiMa, self::AUTO_TYPES, true)) {
            if (count($options) < 2) {
                $errors[$prefix][] = 'Dạng câu hỏi này cần ít nhất 2 lựa chọn.';
            }

            if ($correct !== 1) {
                $errors[$prefix][] = 'Dạng câu hỏi này phải có đúng 1 đáp án đúng.';
            }

            return;
        }

        if ($options !== [] && $correct !== 1) {
            $errors[$prefix][] = 'Nếu có lựa chọn, phải có đúng 1 đáp án đúng.';
        }
    }

    /**
     * @param  array<int, mixed>  $input
     * @param  array<string, array<int, string>>  $errors
     * @param  array<int, string>  $warnings
     * @return array<int, array<string, mixed>>
     */
    private function normalizeChildren(
        mixed $input,
        string $kind,
        string $prefix,
        array &$errors,
        array &$warnings
    ): array {
        if ($input === null || $input === []) {
            return [];
        }

        if (! is_array($input)) {
            $errors[$prefix][] = 'Danh sách câu hỏi con phải là một mảng.';

            return [];
        }

        $allowedTypes = $kind === 'doc'
            ? self::DOC_CHILD_TYPES
            : self::LISTEN_CHILD_TYPES;

        $children = [];
        $seenThutu = [];

        foreach (array_values($input) as $index => $row) {
            $childPrefix = $prefix.'.'.$index;

            if (! is_array($row)) {
                $errors[$childPrefix][] = 'Câu hỏi con phải là một object.';

                continue;
            }

            $loai = trim((string) ($row['loai'] ?? ''));

            if (! in_array($loai, $allowedTypes, true)) {
                $errors[$childPrefix.'.loai'][] = "Loại câu hỏi con \"{$loai}\" không hợp lệ.";

                continue;
            }

            $noidung = trim((string) ($row['noidung'] ?? ''));

            if ($noidung === '') {
                $errors[$childPrefix.'.noidung'][] = 'Nội dung câu hỏi con không được rỗng.';

                continue;
            }

            $thutu = filter_var(
                $row['thutu'] ?? $index + 1,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 999]]
            );

            if ($thutu === false) {
                $errors[$childPrefix.'.thutu'][] = 'Thứ tự phải là số nguyên dương.';
                $thutu = $index + 1;
            }

            if (isset($seenThutu[$thutu])) {
                $errors[$childPrefix.'.thutu'][] = "Thứ tự {$thutu} bị trùng trong cùng câu hỏi cha.";

                continue;
            }

            $seenThutu[$thutu] = true;

            $options = $this->normalizeOptions(
                $row['luachon'] ?? [],
                $childPrefix.'.luachon',
                $errors
            );

            $correct = count(array_filter($options, fn (array $option): bool => $option['ladung']));

            if ($options !== [] && $correct !== 1) {
                $errors[$childPrefix.'.luachon'][] = 'Nếu có lựa chọn, phải có đúng 1 đáp án đúng.';
            }

            $dapandung = $this->nullableString($row['dapandung'] ?? null);

            if ($options === [] && $dapandung === null) {
                $errors[$childPrefix.'.dapandung'][] = 'Câu hỏi con không có lựa chọn phải có đáp án đúng.';
            }

            $children[] = [
                'loai' => $loai,
                'noidung' => $noidung,
                'dapandung' => $dapandung,
                'giaithich' => $this->nullableString($row['giaithich'] ?? null),
                'thutu' => (int) $thutu,
                'luachon' => $options,
            ];
        }

        return $children;
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @param  array<string, array<int, string>>  $errors
     * @return array<string, mixed>|null
     */
    private function normalizeExam(mixed $input, array $questions, array &$errors): ?array
    {
        if ($input === null) {
            return null;
        }

        if (! is_array($input)) {
            $errors['exam'][] = 'Trường exam phải là một object.';

            return null;
        }

        $ten = trim((string) ($input['ten'] ?? ''));

        if ($ten === '') {
            $errors['exam.ten'][] = 'Tên đề thi không được rỗng.';
        } elseif (mb_strlen($ten) > 150) {
            $errors['exam.ten'][] = 'Tên đề thi không được vượt quá 150 ký tự.';
        }

        $kieu = (string) ($input['kieu'] ?? 'fixed');

        if (! in_array($kieu, ['fixed', 'adaptive'], true)) {
            $errors['exam.kieu'][] = 'Kiểu đề phải là fixed hoặc adaptive.';
            $kieu = 'fixed';
        }

        $chedo = (string) ($input['chedo'] ?? 'full');

        if (! in_array($chedo, ['full', 'section'], true)) {
            $errors['exam.chedo'][] = 'Chế độ đề phải là full hoặc section.';
            $chedo = 'full';
        }

        $kynang = $input['kynang'] ?? null;
        $kynang = $kynang === '' ? null : $kynang;

        if ($kynang !== null && ! in_array($kynang, ['reading', 'listening', 'writing', 'speaking'], true)) {
            $errors['exam.kynang'][] = 'Kỹ năng không hợp lệ.';
        }

        if ($chedo === 'section' && $kynang === null) {
            $errors['exam.kynang'][] = 'Đề dạng section phải chọn kỹ năng.';
        }

        $thoigian = filter_var(
            $input['thoigian'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 86400]]
        );

        if ($thoigian === false) {
            $errors['exam.thoigian'][] = 'Thời gian đề phải là số nguyên từ 1 đến 86400 giây.';
            $thoigian = 3600;
        }

        $trangthai = (string) ($input['trangthai'] ?? 'draft');

        if (! in_array($trangthai, ['draft', 'published', 'hidden'], true)) {
            $errors['exam.trangthai'][] = 'Trạng thái đề không hợp lệ.';
            $trangthai = 'draft';
        }

        $refs = $input['questions'] ?? null;

        if (! is_array($refs) || $refs === []) {
            $errors['exam.questions'][] = 'Đề thi phải có danh sách câu hỏi.';

            return null;
        }

        $questionByMa = [];
        $questionByIndex = [];

        foreach ($questions as $index => $question) {
            $questionByMa[$question['ma']] = $question;
            $questionByIndex[$index] = $question;
        }

        $examQuestions = [];
        $seenRefs = [];

        foreach (array_values($refs) as $index => $ref) {
            $ma = null;

            if (is_int($ref)) {
                $ma = $questionByIndex[$ref]['ma'] ?? null;
            } elseif (is_string($ref)) {
                $ref = trim($ref);

                if (isset($questionByMa[$ref])) {
                    $ma = $ref;
                } elseif (ctype_digit($ref)) {
                    $ma = $questionByIndex[(int) $ref]['ma'] ?? null;
                }
            } elseif (is_array($ref) && isset($ref['ma'])) {
                $refMa = trim((string) $ref['ma']);
                $ma = isset($questionByMa[$refMa]) ? $refMa : null;
            }

            if ($ma === null) {
                $errors['exam.questions.'.$index][] = 'Không tìm thấy câu hỏi được tham chiếu.';

                continue;
            }

            if (isset($seenRefs[$ma])) {
                $errors['exam.questions.'.$index][] = "Câu hỏi \"{$ma}\" bị lặp trong đề.";

                continue;
            }

            $seenRefs[$ma] = true;
            $examQuestions[] = $ma;
        }

        if ($examQuestions === []) {
            $errors['exam.questions'][] = 'Đề thi chưa có câu hỏi hợp lệ.';

            return null;
        }

        $tongdiem = 0.0;

        foreach ($examQuestions as $ma) {
            $tongdiem += (float) $questionByMa[$ma]['diem'];
        }

        return [
            'ten' => $ten,
            'mota' => $this->nullableString($input['mota'] ?? null),
            'chedo' => $chedo,
            'kieu' => $kieu,
            'kynang' => $kynang,
            'thoigian' => (int) $thoigian,
            'tongdiem' => $tongdiem,
            'trangthai' => $trangthai,
            'questions' => $examQuestions,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @param  array<string, mixed>|null  $exam
     * @return array<string, mixed>
     */
    private function summary(array $questions, ?array $exam): array
    {
        $byType = [];

        foreach ($questions as $question) {
            $byType[$question['loai_ma']] = ($byType[$question['loai_ma']] ?? 0) + 1;
        }

        return [
            'questions' => count($questions),
            'options' => array_sum(array_map(
                fn (array $question): int => count($question['luachon'])
                    + array_sum(array_map(
                        fn (array $child): int => count($child['luachon']),
                        $question['cauhoi_doc']
                    ))
                    + array_sum(array_map(
                        fn (array $child): int => count($child['luachon']),
                        $question['cauhoi_nghe']
                    )),
                $questions
            )),
            'reading_children' => array_sum(array_map(
                fn (array $question): int => count($question['cauhoi_doc']),
                $questions
            )),
            'listening_children' => array_sum(array_map(
                fn (array $question): int => count($question['cauhoi_nghe']),
                $questions
            )),
            'by_type' => $byType,
            'exam_questions' => $exam ? count($exam['questions']) : 0,
        ];
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     * @param  array<int, string>  $warnings
     */
    private function normalizeMediaPath(
        mixed $value,
        string $prefix,
        array &$errors,
        array &$warnings
    ): ?string {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_string($value)) {
            $errors[$prefix][] = 'Đường dẫn media phải là string.';

            return null;
        }

        $path = trim(str_replace('\\', '/', $value));
        $path = ltrim($path, '/');

        if ($path === '') {
            return null;
        }

        if (str_contains($path, '..') || preg_match('#^[a-zA-Z]:#', $path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $errors[$prefix][] = 'Đường dẫn media không được chứa "..", ổ đĩa hoặc URL tuyệt đối.';

            return null;
        }

        if (! File::exists(public_path($path))) {
            $warnings[] = "Chưa thấy file media \"{$path}\" trong public/.";
        }

        return $path;
    }

    private function toBoolean(mixed $value): bool
    {
        $result = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $result ?? false;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array{
     *     questions: array<int, array<string, mixed>>,
     *     exam: null,
     *     summary: array<string, int>,
     *     errors: array<string, array<int, string>>,
     *     warnings: array<int, string>
     * }
     */
    private function failure(string $message): array
    {
        return [
            'questions' => [],
            'exam' => null,
            'summary' => [
                'questions' => 0,
                'options' => 0,
                'reading_children' => 0,
                'listening_children' => 0,
                'by_type' => [],
                'exam_questions' => 0,
            ],
            'errors' => ['file' => [$message]],
            'warnings' => [],
        ];
    }
}
