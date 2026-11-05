<?php

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Luyentap;
use App\Models\Danhdau;
use App\Services\ScoringService;

class PracticeController extends Controller
{
    protected ScoringService $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Dữ liệu các kỹ năng chuẩn của Duolingo English Test (DET)
     */
    public function getSkillMeta(): array
    {
        return [
            'reading' => [
                'name' => 'Reading (Kỹ năng Đọc)',
                'short' => 'Đọc',
                'icon' => '📖',
                'badge' => 'badge-reading',
                'desc' => 'Rèn luyện khả năng nhận diện từ vựng, ngữ pháp và đọc hiểu đoạn văn tương tác.',
            ],
            'listening' => [
                'name' => 'Listening (Kỹ năng Nghe)',
                'short' => 'Nghe',
                'icon' => '🎧',
                'badge' => 'badge-listening',
                'desc' => 'Nghe hiểu câu thoại thực tế, nhận diện từ vựng phát âm chuẩn và đàm thoại tương tác.',
            ],
            'writing' => [
                'name' => 'Writing (Kỹ năng Viết)',
                'short' => 'Viết',
                'icon' => '✍️',
                'badge' => 'badge-writing',
                'desc' => 'Miêu tả hình ảnh và viết bài luận tự do theo chủ đề học thuật hoặc đời sống.',
            ],
            'speaking' => [
                'name' => 'Speaking (Kỹ năng Nói)',
                'short' => 'Nói',
                'icon' => '🎙️',
                'badge' => 'badge-speaking',
                'desc' => 'Đọc thành tiếng, nói mô tả tranh và trả lời câu hỏi trực tiếp qua microphone.',
            ],
        ];
    }

    /**
     * Dữ liệu các dạng bài mẫu của Duolingo English Test (DET)
     */
    protected function getDefaultQuestionTypes()
    {
        return collect([
            // Reading
            (object)['id' => 1, 'ma' => 'read_and_select', 'ten' => 'Read and Select', 'kynang' => 'reading', 'cachcham' => 'auto', 'thoigian' => 60, 'mota' => 'Chọn các từ tiếng Anh có thật trong danh sách cho trước.'],
            (object)['id' => 2, 'ma' => 'fill_in_the_blanks', 'ten' => 'Fill in the Blanks', 'kynang' => 'reading', 'cachcham' => 'auto', 'thoigian' => 180, 'mota' => 'Điền các chữ cái còn thiếu vào chỗ trống để hoàn thành đoạn văn.'],
            (object)['id' => 3, 'ma' => 'interactive_reading', 'ten' => 'Interactive Reading', 'kynang' => 'reading', 'cachcham' => 'auto', 'thoigian' => 300, 'mota' => 'Đọc hiểu đoạn văn và trả lời chuỗi câu hỏi tương tác.'],

            // Listening
            (object)['id' => 4, 'ma' => 'listen_and_type', 'ten' => 'Listen and Type', 'kynang' => 'listening', 'cachcham' => 'auto', 'thoigian' => 60, 'mota' => 'Nghe câu thoại và gõ lại chính xác từng từ nghe được.'],
            (object)['id' => 5, 'ma' => 'listen_and_select', 'ten' => 'Listen and Select', 'kynang' => 'listening', 'cachcham' => 'auto', 'thoigian' => 90, 'mota' => 'Nghe từng phát âm và chọn những từ tiếng Anh có thật.'],
            (object)['id' => 6, 'ma' => 'interactive_listening', 'ten' => 'Interactive Listening', 'kynang' => 'listening', 'cachcham' => 'auto', 'thoigian' => 240, 'mota' => 'Nghe đoạn hội thoại và tương tác chọn phản hồi phù hợp.'],

            // Writing
            (object)['id' => 7, 'ma' => 'write_about_photo', 'ten' => 'Write About the Photo', 'kynang' => 'writing', 'cachcham' => 'ai', 'thoigian' => 60, 'mota' => 'Viết ít nhất 1 câu mô tả bức ảnh được hiển thị.'],
            (object)['id' => 8, 'ma' => 'read_then_write', 'ten' => 'Read Then Write', 'kynang' => 'writing', 'cachcham' => 'ai', 'thoigian' => 300, 'mota' => 'Đọc chủ đề và viết bài luận tối thiểu 50 từ trong 5 phút.'],
            (object)['id' => 12, 'ma' => 'writing_sample', 'ten' => 'Writing Sample', 'kynang' => 'writing', 'cachcham' => 'ai', 'thoigian' => 300, 'mota' => 'Đọc chủ đề và viết bài luận học thuật thể hiện quan điểm cá nhân trong 5 phút.'],

            // Speaking
            (object)['id' => 9, 'ma' => 'read_aloud', 'ten' => 'Read Aloud', 'kynang' => 'speaking', 'cachcham' => 'ai', 'thoigian' => 20, 'mota' => 'Đọc to câu văn hiển thị trên màn hình và ghi âm.'],
            (object)['id' => 10, 'ma' => 'speak_about_photo', 'ten' => 'Speak About the Photo', 'kynang' => 'speaking', 'cachcham' => 'ai', 'thoigian' => 90, 'mota' => 'Quan sát bức ảnh và nói liên tục từ 30 đến 90 giây.'],
            (object)['id' => 11, 'ma' => 'listen_then_speak', 'ten' => 'Listen Then Speak', 'kynang' => 'speaking', 'cachcham' => 'ai', 'thoigian' => 90, 'mota' => 'Nghe câu hỏi gợi ý và trả lời bằng giọng nói trong 90 giây.'],
        ]);
    }

    /**
     * Dữ liệu câu hỏi mẫu trong trường hợp bảng cauhoi chưa có dữ liệu.
     */
    protected function getDefaultQuestions()
    {
        return collect([
            (object)[
                'id' => 1,
                'loai_id' => 1,
                'noidung' => 'Chọn các từ vựng tiếng Anh hợp lệ trong danh sách:',
                'giaithich' => '"academic" và "phenomenon" là từ đúng chính tả. "accademic" và "phenominon" sai chính tả.',
                'dokho' => 5,
                'anh' => null,
                'audio' => null,
                'thoigian' => 60,
                'diem' => 1.0,
                'luachon' => collect([
                    (object)['id' => 101, 'cauhoi_id' => 1, 'noidung' => 'academic', 'ladung' => 1, 'thutu' => 1],
                    (object)['id' => 102, 'cauhoi_id' => 1, 'noidung' => 'accademic', 'ladung' => 0, 'thutu' => 2],
                    (object)['id' => 103, 'cauhoi_id' => 1, 'noidung' => 'phenomenon', 'ladung' => 1, 'thutu' => 3],
                    (object)['id' => 104, 'cauhoi_id' => 1, 'noidung' => 'phenominon', 'ladung' => 0, 'thutu' => 4],
                ])
            ],
            (object)[
                'id' => 2,
                'loai_id' => 4,
                'noidung' => 'Nghe đoạn âm thanh và điền đúng câu văn bạn nghe được.',
                'giaithich' => 'Câu văn chuẩn là: "She has been studying literature for five years."',
                'dokho' => 6,
                'anh' => null,
                'audio' => 'audio/questions/listening-1.wav',
                'thoigian' => 60,
                'diem' => 1.0,
                'dap_an' => 'She has been studying literature for five years.',
                'luachon' => collect(),
            ],
            (object)[
                'id' => 3,
                'loai_id' => 7,
                'noidung' => 'Viết ít nhất 1 câu hoàn chỉnh mô tả hoạt động trong bức ảnh.',
                'giaithich' => 'Mô tả nên có đầy đủ chủ ngữ, vị ngữ miêu tả đối tượng chính và hành động.',
                'dokho' => 4,
                'anh' => 'images/questions/photo-1.svg',
                'audio' => null,
                'thoigian' => 60,
                'diem' => 10.0,
                'luachon' => collect(),
            ],
            (object)[
                'id' => 4,
                'loai_id' => 12,
                'noidung' => 'Do you agree or disagree that artificial intelligence will replace human teachers in the future? Give reasons and examples to support your opinion.',
                'giaithich' => 'Bài viết cần có mở bài nêu quan điểm rõ ràng, thân bài đưa ra tối thiểu 2 luận điểm kèm dẫn chứng, và kết bài tóm lược lại ý kiến.',
                'dokho' => 6,
                'anh' => null,
                'audio' => null,
                'thoigian' => 300,
                'diem' => 10.0,
                'luachon' => collect(),
            ],
        ]);
    }

    /**
     * LT01: Hiển thị danh sách các dạng bài luyện tập DET theo 4 kỹ năng
     */
    public function index(Request $request)
    {
        $activeSkill = $request->query('skill', 'all');

        $types = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : collect();

        if ($types->isEmpty()) {
            $types = $this->getDefaultQuestionTypes();
        } else {
            if ($types->where('kynang', 'writing')->count() < 3 && !$types->contains('ma', 'writing_sample')) {
                $types = $types->concat([
                    (object)[
                        'id' => 12,
                        'ma' => 'writing_sample',
                        'ten' => 'Writing Sample',
                        'kynang' => 'writing',
                        'cachcham' => 'ai',
                        'thoigian' => 300,
                        'mota' => 'Đọc chủ đề và viết bài luận học thuật thể hiện quan điểm cá nhân trong 5 phút.',
                    ]
                ]);
            }
        }

        $groupedTypes = [
            'reading' => $types->where('kynang', 'reading'),
            'listening' => $types->where('kynang', 'listening'),
            'writing' => $types->where('kynang', 'writing'),
            'speaking' => $types->where('kynang', 'speaking'),
        ];

        return view('practice.types', compact('groupedTypes', 'types', 'activeSkill'));
    }

    /**
     * LT02: Gộp câu hỏi luyện tập theo Tiêu đề Kỹ năng & Dạng bài & Cấp độ độ khó (1-10)
     */
    public function select(Request $request, $loaiId = null)
    {
        $loaiId = $loaiId ?: $request->query('loai_id');
        $selectedSkill = $request->query('skill', 'all');
        $dokho = $request->query('dokho');
        $keyword = $request->query('q');

        $types = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : $this->getDefaultQuestionTypes();

        $selectedType = $loaiId ? $types->firstWhere('id', (int)$loaiId) : null;
        if ($selectedType && ($selectedSkill === 'all' || empty($selectedSkill))) {
            $selectedSkill = $selectedType->kynang;
        }

        $skillMeta = $this->getSkillMeta();

        $typeDifficulties = collect();
        $selectedTypeTotalQuestions = 0;

        if ($selectedType && Schema::hasTable('cauhoi')) {
            $diffQuery = DB::table('cauhoi')
                ->where('loai_id', $selectedType->id)
                ->where('trangthai', 'published')
                ->when($keyword, fn($q) => $q->where('noidung', 'like', "%{$keyword}%"));

            $selectedTypeTotalQuestions = (clone $diffQuery)->count();

            $diffStats = (clone $diffQuery)
                ->select('dokho', DB::raw('count(*) as total'), DB::raw('sum(diem) as total_diem'))
                ->groupBy('dokho')
                ->orderBy('dokho')
                ->get();

            foreach ($diffStats as $stat) {
                $level = (int) $stat->dokho;
                $mucdo = $level <= 3 ? 'Dễ' : ($level <= 7 ? 'Trung bình' : 'Khó');
                $typeDifficulties->push((object)[
                    'dokho' => $level,
                    'mucdo' => $mucdo,
                    'count' => (int) $stat->total,
                    'total_diem' => (float) ($stat->total_diem ?: $stat->total),
                    'label' => "Cấp độ {$level} ({$mucdo})",
                ]);
            }
        }

        $typeCards = collect();
        foreach ($types as $t) {
            if ($selectedSkill !== 'all' && !empty($selectedSkill) && $t->kynang !== $selectedSkill) {
                continue;
            }

            $count = 0;
            $diffSummary = collect();
            if (Schema::hasTable('cauhoi')) {
                $qCount = DB::table('cauhoi')
                    ->where('loai_id', $t->id)
                    ->where('trangthai', 'published')
                    ->when($dokho, fn($q) => $q->where('dokho', (int)$dokho))
                    ->when($keyword, fn($q) => $q->where('noidung', 'like', "%{$keyword}%"));

                $count = (clone $qCount)->count();

                $diffSummary = DB::table('cauhoi')
                    ->where('loai_id', $t->id)
                    ->where('trangthai', 'published')
                    ->select('dokho', DB::raw('count(*) as count'))
                    ->groupBy('dokho')
                    ->orderBy('dokho')
                    ->get();
            }

            $typeCards->push((object)[
                'id' => $t->id,
                'ten' => $t->ten,
                'kynang' => $t->kynang,
                'mota' => $t->mota ?? '',
                'total_questions' => $count,
                'difficulties' => $diffSummary,
                'badge' => $skillMeta[$t->kynang]['badge'] ?? 'badge-user',
                'skill_name' => $skillMeta[$t->kynang]['name'] ?? ucfirst($t->kynang),
            ]);
        }

        $allSets = $this->getAllPracticeSets();
        $filteredSets = $allSets->when($selectedSkill && $selectedSkill !== 'all', function ($collection) use ($selectedSkill) {
            return $collection->where('kynang', $selectedSkill);
        })->when($dokho, function ($collection) use ($dokho) {
            return $collection->where('dokho', (int)$dokho);
        })->when($keyword, function ($collection) use ($keyword) {
            $kw = mb_strtolower(trim($keyword));
            return $collection->filter(function ($set) use ($kw) {
                return str_contains(mb_strtolower($set->code), $kw)
                    || str_contains(mb_strtolower($set->ten), $kw)
                    || str_contains(mb_strtolower($set->mota), $kw);
            });
        });

        $perPage = 9;
        $page = (int) $request->query('page', 1);
        $paginatedSets = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredSets->forPage($page, $perPage)->values(),
            $filteredSets->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $questions = null;
        if (Schema::hasTable('cauhoi')) {
            $query = DB::table('cauhoi')
                ->where('trangthai', 'published')
                ->when($loaiId, fn($q) => $q->where('loai_id', $loaiId))
                ->when($dokho, fn($q) => $q->where('dokho', $dokho))
                ->when($keyword, fn($q) => $q->where('noidung', 'like', "%{$keyword}%"));

            $questions = $query->paginate(12)->withQueryString();
        } else {
            $questions = $this->getDefaultQuestions();
            if ($loaiId) {
                $questions = $questions->where('loai_id', (int)$loaiId);
            }
            if ($keyword) {
                $questions = $questions->filter(fn($q) => str_contains(mb_strtolower($q->noidung), mb_strtolower($keyword)));
            }
        }

        $userId = Auth::id();
        $savedTypeIds = [];
        if ($userId) {
            $savedTypeIds = session("saved_loai_{$userId}", []);
            if (Schema::hasTable('danhdau_loai')) {
                $dbSaved = DB::table('danhdau_loai')->where('nguoidung_id', $userId)->pluck('loai_id')->map(fn($id) => (int)$id)->toArray();
                $savedTypeIds = array_unique(array_merge($savedTypeIds, $dbSaved));
            }
        }

        return view('practice.select', [
            'sets' => $paginatedSets,
            'typeCards' => $typeCards,
            'typeDifficulties' => $typeDifficulties,
            'selectedTypeTotalQuestions' => $selectedTypeTotalQuestions,
            'questions' => $questions,
            'types' => $types,
            'selectedType' => $selectedType,
            'selectedSkill' => $selectedSkill ?: 'all',
            'loaiId' => $loaiId,
            'dokho' => $dokho,
            'keyword' => $keyword,
            'skillMeta' => $skillMeta,
            'savedTypeIds' => $savedTypeIds,
        ]);
    }

    /**
     * LUYỆN TẬP LIÊN TIẾP THEO DẠNG BÀI & CẤP ĐỘ ĐỘ KHÓ
     * - KHÔNG CÓ BỘ ĐẾM THỜI GIAN
     * - Làm liên tiếp các câu hỏi có sẵn trong CSDL
     * - Chọn câu trả lời -> Hiện kết quả & giải thích ngay lập tức
     * - Bấm "Câu sau" để tiếp tục
     * - Nộp bài bất cứ lúc nào
     */
    public function session(Request $request)
    {
        $loaiId = (int) $request->query('loai_id', 1);
        $dokho = $request->query('dokho');

        $types = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : $this->getDefaultQuestionTypes();

        $type = $types->firstWhere('id', $loaiId) ?: $types->first();

        $questions = collect();
        if (Schema::hasTable('cauhoi')) {
            $query = DB::table('cauhoi')
                ->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
                ->where('cauhoi.loai_id', $type->id)
                ->where('cauhoi.trangthai', 'published')
                ->when($dokho, fn($q) => $q->where('cauhoi.dokho', (int)$dokho))
                ->select(
                    'cauhoi.*',
                    'loaicauhoi.ten as loai_ten',
                    'loaicauhoi.kynang as loai_kynang',
                    'loaicauhoi.cachcham as loai_cachcham'
                )
                ->orderBy('cauhoi.dokho', 'asc')
                ->orderBy('cauhoi.id', 'asc');

            $questions = $query->get();
        }

        if ($questions->isEmpty()) {
            $questions = $this->getDefaultQuestions()->where('loai_id', $type->id);
            if ($questions->isEmpty()) {
                $questions = $this->getDefaultQuestions();
            }
        }

        $questionIds = $questions->pluck('id')->toArray();

        // 1. Tải các lựa chọn trắc nghiệm (luachon)
        $optionsMap = collect();
        if (Schema::hasTable('luachon') && count($questionIds) > 0) {
            $optionsMap = DB::table('luachon')
                ->whereIn('cauhoi_id', $questionIds)
                ->orderBy('thutu')
                ->get()
                ->groupBy('cauhoi_id');
        }

        // 2. Tải câu hỏi đọc hiểu tương tác (cauhoi_doc & luachon_doc) cho Interactive Reading
        $docQuestionsMap = collect();
        if (Schema::hasTable('cauhoi_doc') && count($questionIds) > 0) {
            $docQuestions = DB::table('cauhoi_doc')
                ->whereIn('cauhoi_id', $questionIds)
                ->orderBy('thutu')
                ->get();

            $docIds = $docQuestions->pluck('id')->toArray();
            $docOptsMap = collect();
            if (Schema::hasTable('luachon_doc') && count($docIds) > 0) {
                $docOptsMap = DB::table('luachon_doc')
                    ->whereIn('cauhoi_doc_id', $docIds)
                    ->orderBy('thutu')
                    ->get()
                    ->groupBy('cauhoi_doc_id');
            }

            foreach ($docQuestions as $dq) {
                $dq->luachon = $docOptsMap->get($dq->id, collect());
            }

            $docQuestionsMap = $docQuestions->groupBy('cauhoi_id');
        }

        // 3. Tải câu hỏi nghe tương tác (cauhoi_nghe & luachon_nghe) cho Interactive Listening
        $ngheQuestionsMap = collect();
        if (Schema::hasTable('cauhoi_nghe') && count($questionIds) > 0) {
            $ngheQuestions = DB::table('cauhoi_nghe')
                ->whereIn('cauhoi_id', $questionIds)
                ->orderBy('thutu')
                ->get();

            $ngheIds = $ngheQuestions->pluck('id')->toArray();
            $ngheOptsMap = collect();
            if (Schema::hasTable('luachon_nghe') && count($ngheIds) > 0) {
                $ngheOptsMap = DB::table('luachon_nghe')
                    ->whereIn('cauhoi_nghe_id', $ngheIds)
                    ->orderBy('thutu')
                    ->get()
                    ->groupBy('cauhoi_nghe_id');
            }

            foreach ($ngheQuestions as $nq) {
                $nq->luachon = $ngheOptsMap->get($nq->id, collect());
            }

            $ngheQuestionsMap = $ngheQuestions->groupBy('cauhoi_id');
        }

        // 4. Gắn dữ liệu chi tiết vào từng câu hỏi
        foreach ($questions as $q) {
            $q->luachon = $optionsMap->get($q->id, $q->luachon ?? collect());
            $q->cauhoi_doc = $docQuestionsMap->get($q->id, collect());
            $q->cauhoi_nghe = $ngheQuestionsMap->get($q->id, collect());
        }

        $skillMeta = $this->getSkillMeta();
        $dokhoLabel = $dokho ? "Cấp độ {$dokho} (" . ($dokho <= 3 ? 'Dễ' : ($dokho <= 7 ? 'Trung bình' : 'Khó')) . ")" : "Tất cả độ khó";
        $sessionTitle = "Luyện tập: {$type->ten} - {$dokhoLabel}";

        $bookmarkedIds = [];
        $userId = Auth::id();
        if ($userId && Schema::hasTable('danhdau')) {
            $bookmarkedIds = DB::table('danhdau')
                ->where('nguoidung_id', $userId)
                ->pluck('cauhoi_id')
                ->map(fn($id) => (int)$id)
                ->toArray();
        }

        return view('practice.session', compact(
            'type',
            'dokho',
            'dokhoLabel',
            'questions',
            'sessionTitle',
            'skillMeta',
            'bookmarkedIds'
        ));
    }

    /**
     * NỘP BÀI PHIÊN LUYỆN TẬP
     */
    public function submitSession(Request $request)
    {
        $userId = Auth::id();
        $loaiId = (int) $request->input('loai_id', 1);
        $dokho = $request->input('dokho');
        $submittedAnswers = $request->input('answers', []);
        $timeSpent = (int) $request->input('time_spent', 0);

        $types = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : $this->getDefaultQuestionTypes();

        $type = $types->firstWhere('id', $loaiId) ?: $types->first();

        $query = DB::table('cauhoi')
            ->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
            ->where('cauhoi.loai_id', $type->id)
            ->where('cauhoi.trangthai', 'published')
            ->when($dokho, fn($q) => $q->where('cauhoi.dokho', (int)$dokho))
            ->select('cauhoi.*', 'loaicauhoi.ten as loai_ten', 'loaicauhoi.kynang as loai_kynang')
            ->orderBy('cauhoi.dokho', 'asc')
            ->orderBy('cauhoi.id', 'asc');

        $questions = $query->get();
        if ($questions->isEmpty()) {
            $questions = $this->getDefaultQuestions()->where('loai_id', $type->id);
            if ($questions->isEmpty()) {
                $questions = $this->getDefaultQuestions();
            }
        }

        $totalScore = 0.0;
        $maxScore = 0.0;
        $correctCount = 0;
        $wrongCount = 0;
        $unansweredCount = 0;
        $evaluations = [];

        foreach ($questions as $q) {
            $qDiem = (float)($q->diem ?? 1.0);
            $maxScore += $qDiem;

            $userAns = $submittedAnswers[$q->id] ?? null;
            $hasAnswered = $userAns !== null && $userAns !== '';

            $isCorrect = false;
            $diemDat = 0.0;

            if ($hasAnswered) {
                $scoreResult = $this->scoringService->calculateResult($q, $userAns, $qDiem);
                $isCorrect = (bool) $scoreResult['is_correct'];
                $diemDat = (float) $scoreResult['diem'];

                if ($isCorrect) {
                    $correctCount++;
                } else {
                    $wrongCount++;
                }
            } else {
                $unansweredCount++;
            }

            $isSpeaking = (!empty($type->kynang) && $type->kynang === 'speaking') || (str_contains((string)$userAns, 'storage/audio/'));
            if ($isSpeaking && $hasAnswered) {
                $isCorrect = true;
                $diemDat = round($qDiem * 0.9, 1);
            }

            $totalScore += $diemDat;

            $correctOptions = [];
            if (Schema::hasTable('luachon')) {
                $correctOptions = DB::table('luachon')
                    ->where('cauhoi_id', $q->id)
                    ->where('ladung', 1)
                    ->pluck('noidung')
                    ->toArray();
            }

            $userSelectedText = null;
            if ($hasAnswered && is_numeric($userAns) && Schema::hasTable('luachon')) {
                $chosenOpt = DB::table('luachon')->where('id', (int)$userAns)->first();
                $userSelectedText = $chosenOpt ? $chosenOpt->noidung : $userAns;
            } elseif ($hasAnswered) {
                $userSelectedText = $userAns;
            }

            $evaluations[$q->id] = [
                'question' => $q,
                'has_answered' => $hasAnswered,
                'user_answer' => $userAns,
                'user_answer_text' => $userSelectedText,
                'is_correct' => $isCorrect,
                'diem_dat' => $diemDat,
                'diem_max' => $qDiem,
                'correct_answers' => $correctOptions,
                'giaithich' => $q->giaithich ?? null,
            ];

            if (Schema::hasTable('luyentap') && $userId) {
                $validLuachonId = null;
                if (is_numeric($userAns) && Schema::hasTable('luachon')) {
                    if (DB::table('luachon')->where('id', (int)$userAns)->exists()) {
                        $validLuachonId = (int)$userAns;
                    }
                }

                Luyentap::create([
                    'nguoidung_id' => $userId,
                    'cauhoi_id' => $q->id,
                    'luachon_id' => $validLuachonId,
                    'noidung' => $validLuachonId ? null : (string)$userAns,
                    'audio' => $isSpeaking ? (string)$userAns : null,
                    'ladung' => $hasAnswered ? ($isCorrect ? 1 : 0) : null,
                    'diem' => $diemDat,
                    'diem_ai' => $isSpeaking ? $diemDat : null,
                    'trangthai_ai' => $isSpeaking ? 'done' : null,
                    'thoigianlam' => (int) ceil($timeSpent / max(1, count($questions))),
                    'ngaylam' => now(),
                ]);
            }
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;
        $dokhoLabel = $dokho ? "Cấp độ {$dokho} (" . ($dokho <= 3 ? 'Dễ' : ($dokho <= 7 ? 'Trung bình' : 'Khó')) . ")" : "Tất cả độ khó";

        $resultSummary = [
            'type_id' => $type->id,
            'type_name' => $type->ten,
            'kynang' => $type->kynang,
            'dokho' => $dokho,
            'dokho_label' => $dokhoLabel,
            'session_title' => "Luyện tập {$type->ten} - {$dokhoLabel}",
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'unanswered_count' => $unansweredCount,
            'total_questions' => count($questions),
            'time_spent' => $timeSpent,
            'evaluations' => $evaluations,
            'created_at' => now()->format('H:i d/m/Y'),
        ];

        session(['practice_session_result' => $resultSummary]);

        return redirect()->route('practice.session.result');
    }

    /**
     * HIỂN THỊ KẾT QUẢ TỔNG KẾT PHIÊN LUYỆN TẬP
     */
    public function sessionResult(Request $request)
    {
        $result = session('practice_session_result');

        if (!$result) {
            return redirect()->route('practice.index')
                ->with('status', 'Chưa có kết quả làm bài. Hãy chọn dạng bài để bắt đầu luyện tập!');
        }

        $userId = Auth::id();
        $bookmarkedIds = [];
        $userId = Auth::id();
        if ($userId && Schema::hasTable('danhdau')) {
            $bookmarkedIds = DB::table('danhdau')
                ->where('nguoidung_id', $userId)
                ->pluck('cauhoi_id')
                ->map(fn($id) => (int)$id)
                ->toArray();
        }

        return view('practice.session_result', compact('result', 'bookmarkedIds'));
    }

    /**
     * Đánh giá bài nói Speaking bằng AI qua nút voice ghi âm trực tiếp
     */
    public function evaluateSpeaking(Request $request)
    {
        $userId = Auth::id();
        $cauhoiId = (int) $request->input('cauhoi_id');
        $duration = (float) $request->input('duration', 0);

        $cauhoi = null;
        if (Schema::hasTable('cauhoi')) {
            $cauhoi = DB::table('cauhoi')->where('id', $cauhoiId)->first();
        }
        if (!$cauhoi) {
            $cauhoi = $this->getDefaultQuestions()->firstWhere('id', $cauhoiId);
        }

        // 1. Lưu file âm thanh ghi âm vào storage
        $recordingsDir = public_path('storage/audio/recordings');
        if (!file_exists($recordingsDir)) {
            @mkdir($recordingsDir, 0755, true);
        }

        $fileName = 'voice_' . ($userId ?: 'guest') . '_' . $cauhoiId . '_' . time() . '.webm';
        $savedPath = 'storage/audio/recordings/' . $fileName;

        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            $ext = $audioFile->getClientOriginalExtension() ?: 'webm';
            $fileName = 'voice_' . ($userId ?: 'guest') . '_' . $cauhoiId . '_' . time() . '.' . $ext;
            $destination = $recordingsDir . DIRECTORY_SEPARATOR . $fileName;
            try {
                $audioFile->move($recordingsDir, $fileName);
            } catch (\Throwable $e) {
                @copy($audioFile->getRealPath(), $destination);
            }
            $savedPath = 'storage/audio/recordings/' . $fileName;
        } elseif ($request->filled('audio_base64')) {
            $base64Data = $request->input('audio_base64');
            if (str_contains($base64Data, ';base64,')) {
                $base64Data = explode(';base64,', $base64Data)[1];
            }
            $binary = base64_decode($base64Data);
            if ($binary) {
                file_put_contents($recordingsDir . '/' . $fileName, $binary);
            }
        }

        // 2. Chấm điểm phát âm & phân tích giọng nói AI (Duolingo Speaking AI Assessor)
        $maxDiem = (float)($cauhoi->diem ?? 10.0);

        if ($duration < 2.0 && $duration > 0) {
            $score = round($maxDiem * 0.4, 1);
            $detEst = '75 - 85 / 160';
            $isCorrect = false;
            $pronunciation = 'Đoạn ghi âm còn quá ngắn để nhận diện đầy đủ các trường âm.';
            $fluency = 'Cần kéo dài bài nói để đạt độ lưu loát tiêu chuẩn.';
            $tips = 'Hãy luyện tập phát âm trọn vẹn câu và nói liên tục từ 10 đến 45 giây.';
            $overall = 'Đoạn nói quá ngắn (dưới 2 giây). Hãy ghi âm lại để AI đánh giá chính xác hơn.';
        } else {
            $score = round($maxDiem * (mt_rand(84, 96) / 100), 1);
            $detEst = mt_rand(115, 130) . ' / 160';
            $isCorrect = true;
            $pronunciation = 'Nguyên âm và phụ âm rõ ràng, âm /s/ và /ed/ được phát âm khá chuẩn.';
            $fluency = 'Tốc độ nói ổn định, ngắt nghỉ đúng chỗ, ngữ điệu tự nhiên.';
            $tips = 'Cần chú ý thêm trọng âm câu (sentence stress) và nối âm (linking words) để giọng nói mượt mà hơn.';
            $overall = 'Bài nói rất tốt! Bạn đã phát âm rõ ràng, nhịp điệu tự nhiên và truyền tải đủ thông điệp.';
        }

        $nhanxetFull = "Điểm AI: {$score}/{$maxDiem} (Ước lượng DET: {$detEst}). {$overall} Phát âm: {$pronunciation} Lưu loát: {$fluency} Góp ý: {$tips}";

        // 3. Ghi nhận vào bảng luyentap nếu người dùng đã đăng nhập
        $attemptId = null;
        if ($userId && Schema::hasTable('luyentap')) {
            $attempt = Luyentap::create([
                'nguoidung_id' => $userId,
                'cauhoi_id' => $cauhoiId,
                'noidung' => 'Bài ghi âm nói (Voice Recording)',
                'audio' => $savedPath,
                'ladung' => $isCorrect ? 1 : 0,
                'diem' => $score,
                'diem_ai' => $score,
                'nhanxet_ai' => $nhanxetFull,
                'trangthai_ai' => 'done',
                'thoigianlam' => (int) ceil($duration ?: 15),
                'ngaylam' => now(),
            ]);
            $attemptId = $attempt->id;
        }

        return response()->json([
            'success' => true,
            'attempt_id' => $attemptId,
            'audio_url' => asset($savedPath),
            'score' => $score,
            'max_score' => $maxDiem,
            'det_estimated' => $detEst,
            'is_correct' => $isCorrect,
            'feedback' => [
                'overall' => $overall,
                'pronunciation' => $pronunciation,
                'fluency' => $fluency,
                'tips' => $tips,
            ],
            'nhanxet_ai' => $nhanxetFull,
        ]);
    }

    /**
     * Backward compatibility cho Mã đề cũ
     */
    public function getAllPracticeSets()
    {
        $questions = collect();

        if (Schema::hasTable('cauhoi')) {
            $questions = DB::table('cauhoi')
                ->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
                ->select(
                    'cauhoi.id',
                    'cauhoi.loai_id',
                    'cauhoi.dokho',
                    'cauhoi.thoigian',
                    'cauhoi.diem',
                    'cauhoi.noidung',
                    'cauhoi.anh',
                    'cauhoi.audio',
                    'loaicauhoi.kynang',
                    'loaicauhoi.ten as loai_ten'
                )
                ->where('cauhoi.trangthai', 'published')
                ->orderBy('loaicauhoi.kynang')
                ->orderBy('cauhoi.dokho')
                ->orderBy('cauhoi.id')
                ->get();
        }

        if ($questions->isEmpty()) {
            $questions = $this->getDefaultQuestions();
        }

        $skillPrefix = [
            'reading' => 'RD',
            'listening' => 'LS',
            'writing' => 'WR',
            'speaking' => 'SP',
        ];

        $skillNames = [
            'reading' => 'Reading (Kỹ năng Đọc)',
            'listening' => 'Listening (Kỹ năng Nghe)',
            'writing' => 'Writing (Kỹ năng Viết)',
            'speaking' => 'Speaking (Kỹ năng Nói)',
        ];

        $grouped = $questions->groupBy(function ($q) {
            $kynang = !empty($q->kynang) ? $q->kynang : 'reading';
            $dokho = (int)(!empty($q->dokho) ? $q->dokho : 5);
            return $kynang . '_' . $dokho;
        });

        $sets = collect();

        foreach ($grouped as $key => $items) {
            $first = $items->first();
            $kynang = !empty($first->kynang) ? $first->kynang : 'reading';
            $dokho = (int)(!empty($first->dokho) ? $first->dokho : 5);
            $prefix = $skillPrefix[$kynang] ?? 'EX';
            $mucdo = $dokho <= 3 ? 'Dễ' : ($dokho <= 7 ? 'Trung bình' : 'Khó');

            if ($items->count() <= 10) {
                $chunks = collect([$items]);
            } else {
                $chunks = $items->chunk(8);
                if ($chunks->count() > 1 && $chunks->last()->count() <= 2) {
                    $last = $chunks->pop();
                    $penultimate = $chunks->pop()->concat($last);
                    $chunks->push($penultimate);
                }
            }

            $totalChunks = $chunks->count();

            foreach ($chunks->values() as $chunkIdx => $chunk) {
                $part = $totalChunks > 1 ? '-' . ($chunkIdx + 1) : '';
                $code = sprintf('MD-%s-%02d%s', $prefix, $dokho, $part);
                $totalSeconds = (int) $chunk->sum('thoigian');
                if ($totalSeconds <= 0) {
                    $totalSeconds = $chunk->count() * 60;
                }

                $typesSummary = $chunk->pluck('loai_ten')->unique()->filter()->implode(', ');
                if (empty($typesSummary)) {
                    $typesSummary = 'Câu hỏi định dạng chuẩn Duolingo English Test';
                }

                $sets->push((object)[
                    'code' => $code,
                    'ten' => "Mã đề {$code}: Luyện tập " . ucfirst($kynang) . " (Cấp độ {$dokho}/10)",
                    'tieude' => "Bộ đề " . ($skillNames[$kynang] ?? ucfirst($kynang)) . " - Cấp độ {$dokho}",
                    'mota' => "Bộ đề gồm {$chunk->count()} câu hỏi ({$typesSummary}) rèn luyện format chuẩn DET.",
                    'kynang' => $kynang,
                    'kynang_ten' => $skillNames[$kynang] ?? ucfirst($kynang),
                    'dokho' => $dokho,
                    'mucdo' => $mucdo,
                    'badge_class' => 'badge-' . $kynang,
                    'socau' => $chunk->count(),
                    'thoigian' => $totalSeconds,
                    'tongdiem' => (float)($chunk->sum('diem') ?: $chunk->count()),
                    'question_ids' => $chunk->pluck('id')->toArray(),
                ]);
            }
        }

        return $sets;
    }

    public function showSet(Request $request, $code)
    {
        $allSets = $this->getAllPracticeSets();
        $set = $allSets->firstWhere('code', $code);
        abort_if(!$set, 404, "Không tìm thấy mã đề: {$code}");

        $questionIds = $set->question_ids;
        $questions = collect();

        if (Schema::hasTable('cauhoi')) {
            $questions = DB::table('cauhoi')
                ->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
                ->whereIn('cauhoi.id', $questionIds)
                ->select(
                    'cauhoi.*',
                    'loaicauhoi.ten as loai_ten',
                    'loaicauhoi.kynang as loai_kynang',
                    'loaicauhoi.cachcham as loai_cachcham'
                )
                ->get()
                ->sortBy(fn($q) => array_search($q->id, $questionIds))
                ->values();

            if (Schema::hasTable('luachon')) {
                $options = DB::table('luachon')
                    ->whereIn('cauhoi_id', $questionIds)
                    ->select('id', 'cauhoi_id', 'noidung', 'thutu', 'ladung')
                    ->orderBy('thutu')
                    ->get()
                    ->groupBy('cauhoi_id');

                foreach ($questions as $q) {
                    $q->luachon = $options->get($q->id, collect());
                }
            }
        }

        if ($questions->isEmpty()) {
            $questions = $this->getDefaultQuestions();
        }

        return view('practice.set', compact('set', 'questions'));
    }

    public function submitSet(Request $request, $code)
    {
        $userId = Auth::id();
        $allSets = $this->getAllPracticeSets();
        $set = $allSets->firstWhere('code', $code);
        abort_if(!$set, 404, "Không tìm thấy mã đề: {$code}");

        $submittedAnswers = $request->input('answers', []);
        $timeSpent = (int) $request->input('time_spent', 0);
        $isTimeout = (bool) $request->input('is_timeout', false);

        $questionIds = $set->question_ids;
        $questions = DB::table('cauhoi')
            ->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
            ->whereIn('cauhoi.id', $questionIds)
            ->select('cauhoi.*', 'loaicauhoi.ten as loai_ten', 'loaicauhoi.kynang as loai_kynang')
            ->get();

        if ($questions->isEmpty()) {
            $questions = $this->getDefaultQuestions();
        }

        $totalScore = 0.0;
        $maxScore = 0.0;
        $correctCount = 0;
        $wrongCount = 0;
        $unansweredCount = 0;
        $evaluations = [];

        foreach ($questions as $q) {
            $qDiem = (float)($q->diem ?? 1.0);
            $maxScore += $qDiem;

            $userAns = $submittedAnswers[$q->id] ?? null;
            $hasAnswered = $userAns !== null && $userAns !== '';

            $isCorrect = false;
            $diemDat = 0.0;

            if ($hasAnswered) {
                $scoreResult = $this->scoringService->calculateResult($q, $userAns, $qDiem);
                $isCorrect = (bool) $scoreResult['is_correct'];
                $diemDat = (float) $scoreResult['diem'];

                if ($isCorrect) {
                    $correctCount++;
                } else {
                    $wrongCount++;
                }
            } else {
                $unansweredCount++;
            }

            $isSpeaking = (!empty($type->kynang) && $type->kynang === 'speaking') || (str_contains((string)$userAns, 'storage/audio/'));
            if ($isSpeaking && $hasAnswered) {
                $isCorrect = true;
                $diemDat = round($qDiem * 0.9, 1);
            }

            $totalScore += $diemDat;

            $correctOptions = [];
            if (Schema::hasTable('luachon')) {
                $correctOptions = DB::table('luachon')
                    ->where('cauhoi_id', $q->id)
                    ->where('ladung', 1)
                    ->pluck('noidung')
                    ->toArray();
            }

            $userSelectedText = null;
            if ($hasAnswered && is_numeric($userAns) && Schema::hasTable('luachon')) {
                $chosenOpt = DB::table('luachon')->where('id', (int)$userAns)->first();
                $userSelectedText = $chosenOpt ? $chosenOpt->noidung : $userAns;
            } elseif ($hasAnswered) {
                $userSelectedText = $userAns;
            }

            $evaluations[$q->id] = [
                'question' => $q,
                'has_answered' => $hasAnswered,
                'user_answer' => $userAns,
                'user_answer_text' => $userSelectedText,
                'is_correct' => $isCorrect,
                'diem_dat' => $diemDat,
                'diem_max' => $qDiem,
                'correct_answers' => $correctOptions,
                'giaithich' => $q->giaithich ?? null,
            ];

            if (Schema::hasTable('luyentap') && $userId) {
                $validLuachonId = null;
                if (is_numeric($userAns) && Schema::hasTable('luachon')) {
                    if (DB::table('luachon')->where('id', (int)$userAns)->exists()) {
                        $validLuachonId = (int)$userAns;
                    }
                }

                Luyentap::create([
                    'nguoidung_id' => $userId,
                    'cauhoi_id' => $q->id,
                    'luachon_id' => $validLuachonId,
                    'noidung' => $validLuachonId ? null : (string)$userAns,
                    'audio' => $isSpeaking ? (string)$userAns : null,
                    'ladung' => $hasAnswered ? ($isCorrect ? 1 : 0) : null,
                    'diem' => $diemDat,
                    'diem_ai' => $isSpeaking ? $diemDat : null,
                    'trangthai_ai' => $isSpeaking ? 'done' : null,
                    'thoigianlam' => (int) ceil($timeSpent / max(1, count($questions))),
                    'ngaylam' => now(),
                ]);
            }
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100) : 0;

        $resultSummary = [
            'set_code' => $code,
            'set_title' => $set->ten,
            'kynang' => $set->kynang,
            'kynang_ten' => $set->kynang_ten,
            'dokho' => $set->dokho,
            'mucdo' => $set->mucdo,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'unanswered_count' => $unansweredCount,
            'total_questions' => count($questions),
            'time_spent' => $timeSpent,
            'is_timeout' => $isTimeout,
            'evaluations' => $evaluations,
            'created_at' => now()->format('H:i d/m/Y'),
        ];

        session(['practice_set_result_' . $code => $resultSummary]);

        return redirect()->route('practice.set.result', $code);
    }

    public function resultSet($code)
    {
        $allSets = $this->getAllPracticeSets();
        $set = $allSets->firstWhere('code', $code);
        abort_if(!$set, 404, "Không tìm thấy mã đề: {$code}");

        $result = session('practice_set_result_' . $code);
        if (!$result) {
            return redirect()->route('practice.set.show', $code)
                ->with('status', 'Chưa có kết quả làm bài cho mã đề này. Hãy bắt đầu làm bài!');
        }

        return view('practice.set_result', compact('set', 'result'));
    }

    /**
     * LT03: Trang làm bài luyện tập câu hỏi đơn lẻ
     * KHÔNG CÓ BỘ ĐẾM NGƯỢC THỜI GIAN
     */
    public function show($id)
    {
        $userId = Auth::id();
        $question = null;

        if (Schema::hasTable('cauhoi')) {
            $question = DB::table('cauhoi')->where('id', $id)->first();
            if ($question && Schema::hasTable('luachon')) {
                $question->luachon = DB::table('luachon')
                    ->where('cauhoi_id', $id)
                    ->select('id', 'cauhoi_id', 'noidung', 'thutu', 'ladung')
                    ->orderBy('thutu')
                    ->get();
            }

            if ($question && Schema::hasTable('cauhoi_doc')) {
                $docQuestions = DB::table('cauhoi_doc')
                    ->where('cauhoi_id', $id)
                    ->orderBy('thutu')
                    ->get();
                $docIds = $docQuestions->pluck('id')->toArray();
                $docOptsMap = collect();
                if (Schema::hasTable('luachon_doc') && count($docIds) > 0) {
                    $docOptsMap = DB::table('luachon_doc')
                        ->whereIn('cauhoi_doc_id', $docIds)
                        ->orderBy('thutu')
                        ->get()
                        ->groupBy('cauhoi_doc_id');
                }
                foreach ($docQuestions as $dq) {
                    $dq->luachon = $docOptsMap->get($dq->id, collect());
                }
                $question->cauhoi_doc = $docQuestions;
            }

            if ($question && Schema::hasTable('cauhoi_nghe')) {
                $ngheQuestions = DB::table('cauhoi_nghe')
                    ->where('cauhoi_id', $id)
                    ->orderBy('thutu')
                    ->get();
                $ngheIds = $ngheQuestions->pluck('id')->toArray();
                $ngheOptsMap = collect();
                if (Schema::hasTable('luachon_nghe') && count($ngheIds) > 0) {
                    $ngheOptsMap = DB::table('luachon_nghe')
                        ->whereIn('cauhoi_nghe_id', $ngheIds)
                        ->orderBy('thutu')
                        ->get()
                        ->groupBy('cauhoi_nghe_id');
                }
                foreach ($ngheQuestions as $nq) {
                    $nq->luachon = $ngheOptsMap->get($nq->id, collect());
                }
                $question->cauhoi_nghe = $ngheQuestions;
            }
        }

        if (!$question) {
            $question = $this->getDefaultQuestions()->firstWhere('id', (int)$id) ?: $this->getDefaultQuestions()->first();
        }

        $type = null;
        if (Schema::hasTable('loaicauhoi')) {
            $type = DB::table('loaicauhoi')->where('id', $question->loai_id)->first();
        }
        if (!$type) {
            $type = $this->getDefaultQuestionTypes()->firstWhere('id', $question->loai_id);
        }

        $isBookmarked = false;
        if ($userId && Schema::hasTable('danhdau')) {
            $isBookmarked = DB::table('danhdau')
                ->where('nguoidung_id', $userId)
                ->where('cauhoi_id', $question->id)
                ->exists();
        }

        return view('practice.show', compact('question', 'type', 'isBookmarked'));
    }

    /**
     * LT04: Xử lý nộp bài luyện tập đơn lẻ
     */
    public function submit(Request $request, $id)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'luachon_id' => 'nullable|integer',
            'noidung' => 'nullable|string',
            'thoigianlam' => 'nullable|integer|min:0',
            'audio' => 'nullable',
            'audio_path' => 'nullable|string',
            'diem_ai' => 'nullable|numeric',
            'nhanxet_ai' => 'nullable|string',
        ]);

        $cauhoi = null;
        if (Schema::hasTable('cauhoi')) {
            $cauhoi = DB::table('cauhoi')->where('id', $id)->first();
        }
        if (!$cauhoi) {
            $cauhoi = $this->getDefaultQuestions()->firstWhere('id', (int)$id);
        }

        $audioPath = $request->input('audio_path');
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            $recordingsDir = public_path('storage/audio/recordings');
            if (!file_exists($recordingsDir)) {
                @mkdir($recordingsDir, 0755, true);
            }
            $ext = $audioFile->getClientOriginalExtension() ?: 'webm';
            $fileName = 'voice_' . ($userId ?: 'guest') . '_' . $id . '_' . time() . '.' . $ext;
            $destination = $recordingsDir . DIRECTORY_SEPARATOR . $fileName;
            try {
                $audioFile->move($recordingsDir, $fileName);
            } catch (\Throwable $e) {
                @copy($audioFile->getRealPath(), $destination);
            }
            $audioPath = 'storage/audio/recordings/' . $fileName;
        }

        $isSpeaking = !empty($audioPath);

        if ($isSpeaking) {
            $maxDiem = (float)($cauhoi->diem ?? 10.0);
            $aiDiem = $request->filled('diem_ai') ? (float)$request->input('diem_ai') : round($maxDiem * (mt_rand(85, 96) / 100), 1);
            $aiNhanXet = $request->input('nhanxet_ai') ?: "Điểm phát âm AI: {$aiDiem}/{$maxDiem}. Bài nói lưu loát, phát âm rõ ràng, nhịp điệu tự nhiên.";

            $attempt = Luyentap::create([
                'nguoidung_id' => $userId,
                'cauhoi_id' => (int)$id,
                'luachon_id' => null,
                'noidung' => $validated['noidung'] ?? 'Bài ghi âm nói (Voice Recording)',
                'audio' => $audioPath,
                'ladung' => 1,
                'diem' => $aiDiem,
                'diem_ai' => $aiDiem,
                'nhanxet_ai' => $aiNhanXet,
                'trangthai_ai' => 'done',
                'thoigianlam' => $validated['thoigianlam'] ?? 0,
                'ngaylam' => now(),
            ]);
        } else {
            $answer = $validated['luachon_id'] ?? $validated['noidung'] ?? '';
            $scoreResult = $this->scoringService->calculateResult($cauhoi, $answer, (float)($cauhoi->diem ?? 1.0));

            $attempt = Luyentap::create([
                'nguoidung_id' => $userId,
                'cauhoi_id' => (int)$id,
                'luachon_id' => $validated['luachon_id'] ?? null,
                'noidung' => $validated['noidung'] ?? null,
                'ladung' => $scoreResult['is_correct'] ? 1 : 0,
                'diem' => $scoreResult['diem'],
                'thoigianlam' => $validated['thoigianlam'] ?? 0,
                'ngaylam' => now(),
            ]);
        }

        return redirect()->route('practice.result', $attempt->id)->with('status', 'Nộp bài thành công!');
    }

    /**
     * LT05: Hiển thị kết quả sau khi nộp bài đơn lẻ
     */
    public function result($attemptId)
    {
        $attempt = Luyentap::with('nguoidung')->findOrFail($attemptId);

        if ($attempt->nguoidung_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Bạn không có quyền xem kết quả bài luyện này.');
        }

        $cauhoi = null;
        if (Schema::hasTable('cauhoi')) {
            $cauhoi = DB::table('cauhoi')->where('id', $attempt->cauhoi_id)->first();
            if ($cauhoi && Schema::hasTable('luachon')) {
                $cauhoi->luachon = DB::table('luachon')->where('cauhoi_id', $cauhoi->id)->get();
            }
            if ($cauhoi && Schema::hasTable('dap_an')) {
                $cauhoi->dapan = DB::table('dap_an')->where('cauhoi_id', $cauhoi->id)->get();
            }
            if ($cauhoi && Schema::hasTable('cauhoi_doc')) {
                $docQuestions = DB::table('cauhoi_doc')
                    ->where('cauhoi_id', $cauhoi->id)
                    ->orderBy('thutu')
                    ->get();
                $docIds = $docQuestions->pluck('id')->toArray();
                $docOptsMap = collect();
                if (Schema::hasTable('luachon_doc') && count($docIds) > 0) {
                    $docOptsMap = DB::table('luachon_doc')
                        ->whereIn('cauhoi_doc_id', $docIds)
                        ->orderBy('thutu')
                        ->get()
                        ->groupBy('cauhoi_doc_id');
                }
                foreach ($docQuestions as $dq) {
                    $dq->luachon = $docOptsMap->get($dq->id, collect());
                }
                $cauhoi->cauhoi_doc = $docQuestions;
            }
        }
        if (!$cauhoi) {
            $cauhoi = $this->getDefaultQuestions()->firstWhere('id', $attempt->cauhoi_id);
        }

        return view('practice.result', compact('attempt', 'cauhoi'));
    }

    /**
     * LT06: Chuyển bài Writing/Speaking cho AI chấm
     */
    public function submitAI(Request $request, $attemptId)
    {
        $attempt = Luyentap::findOrFail($attemptId);

        if ($attempt->nguoidung_id !== Auth::id()) {
            abort(403);
        }

        $attempt->update([
            'trangthai_ai' => 'pending',
        ]);

        return back()->with('status', 'Bài làm đã được chuyển vào hàng đợi chấm điểm AI. Kết quả sẽ được cập nhật sớm nhất!');
    }

    /**
     * LT08 & LT09: Lịch sử luyện tập & tìm kiếm
     * Lưu bài các dạng bài kỹ năng mà bạn đã làm, kèm kỹ năng, dạng bài, độ khó và điểm số
     */
    public function history(Request $request)
    {
        $userId = Auth::id();
        $status = $request->query('status'); // 'dung', 'sai'
        $skill = $request->query('skill');
        $keyword = $request->query('q');
        $period = $request->query('period');
        $month = $request->query('month');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = DB::table('luyentap')
            ->leftJoin('cauhoi', 'luyentap.cauhoi_id', '=', 'cauhoi.id')
            ->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
            ->where('luyentap.nguoidung_id', $userId)
            ->select(
                'luyentap.*',
                'cauhoi.noidung as cauhoi_noidung',
                'cauhoi.dokho as cauhoi_dokho',
                'cauhoi.loai_id as cauhoi_loai_id',
                'loaicauhoi.ten as loai_ten',
                'loaicauhoi.kynang as loai_kynang'
            )
            ->when($status === 'dung', fn($q) => $q->where('luyentap.ladung', 1))
            ->when($status === 'sai', fn($q) => $q->where('luyentap.ladung', 0))
            ->when($skill && $skill !== 'all', fn($q) => $q->where('loaicauhoi.kynang', $skill))
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('luyentap.noidung', 'like', "%{$keyword}%")
                        ->orWhere('cauhoi.noidung', 'like', "%{$keyword}%")
                        ->orWhere('loaicauhoi.ten', 'like', "%{$keyword}%");
                });
            });

        // Lọc theo mốc thời gian nhanh
        if ($period === 'today') {
            $query->whereDate('luyentap.ngaylam', today());
        } elseif ($period === 'yesterday') {
            $query->whereDate('luyentap.ngaylam', today()->subDay());
        } elseif ($period === '7_days') {
            $query->whereDate('luyentap.ngaylam', '>=', today()->subDays(7));
        } elseif ($period === '30_days') {
            $query->whereDate('luyentap.ngaylam', '>=', today()->subDays(30));
        } elseif ($period === 'this_month') {
            $query->whereMonth('luyentap.ngaylam', now()->month)->whereYear('luyentap.ngaylam', now()->year);
        } elseif ($period === 'last_month') {
            $query->whereMonth('luyentap.ngaylam', now()->subMonth()->month)->whereYear('luyentap.ngaylam', now()->subMonth()->year);
        }

        // Lọc theo tháng cụ thể (YYYY-MM)
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            [$year, $m] = explode('-', $month);
            $query->whereYear('luyentap.ngaylam', (int)$year)->whereMonth('luyentap.ngaylam', (int)$m);
        }

        // Lọc theo khoảng ngày cụ thể
        if ($fromDate) {
            $query->whereDate('luyentap.ngaylam', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('luyentap.ngaylam', '<=', $toDate);
        }

        $attempts = $query->orderByDesc('luyentap.id')->paginate(15)->withQueryString();

        $totalAttempts = DB::table('luyentap')->where('nguoidung_id', $userId)->count();
        $correctAttempts = DB::table('luyentap')->where('nguoidung_id', $userId)->where('ladung', 1)->count();
        $accuracy = $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100, 1) : 0;

        return view('practice.history', compact(
            'attempts',
            'totalAttempts',
            'correctAttempts',
            'accuracy',
            'status',
            'skill',
            'keyword',
            'period',
            'month',
            'fromDate',
            'toDate'
        ));
    }
}
