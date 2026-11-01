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
     * Dữ liệu các dạng bài mẫu của Duolingo English Test (DET)
     * Đảm bảo hệ thống hoạt động ngay cả khi TV3 chưa merge bảng loaicauhoi.
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
                'luachon' => [
                    (object)['id' => 101, 'noidung' => 'academic', 'ladung' => 1],
                    (object)['id' => 102, 'noidung' => 'accademic', 'ladung' => 0],
                    (object)['id' => 103, 'noidung' => 'phenomenon', 'ladung' => 1],
                    (object)['id' => 104, 'noidung' => 'phenominon', 'ladung' => 0],
                ]
            ],
            (object)[
                'id' => 2,
                'loai_id' => 4,
                'noidung' => 'Nghe đoạn âm thanh và điền đúng câu văn bạn nghe được.',
                'giaithich' => 'Câu văn chuẩn là: "She has been studying literature for five years."',
                'dokho' => 6,
                'anh' => null,
                'audio' => 'sample_audio.mp3',
                'thoigian' => 60,
                'diem' => 1.0,
                'dap_an' => 'She has been studying literature for five years.',
            ],
            (object)[
                'id' => 3,
                'loai_id' => 7,
                'noidung' => 'Viết ít nhất 1 câu hoàn chỉnh mô tả hoạt động trong bức ảnh.',
                'giaithich' => 'Mô tả nên có đầy đủ chủ ngữ, vị ngữ miêu tả đối tượng chính và hành động.',
                'dokho' => 4,
                'anh' => 'sample_photo.jpg',
                'audio' => null,
                'thoigian' => 60,
                'diem' => 10.0,
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
            ],
        ]);
    }

    /**
     * LT01: Hiển thị danh sách các dạng bài luyện tập DET theo 4 kỹ năng
     */
    public function index()
    {
        $types = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : collect();

        if ($types->isEmpty()) {
            $types = $this->getDefaultQuestionTypes();
        } else {
            // Đảm bảo kỹ năng Writing có đủ 3 dạng bài cân đối với các kỹ năng khác
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

        // Nhóm các dạng bài theo 4 kỹ năng chuẩn DET
        $groupedTypes = [
            'reading' => $types->where('kynang', 'reading'),
            'listening' => $types->where('kynang', 'listening'),
            'writing' => $types->where('kynang', 'writing'),
            'speaking' => $types->where('kynang', 'speaking'),
        ];

        return view('practice.types', compact('groupedTypes', 'types'));
    }

    /**
     * LT02: Chọn câu luyện tập theo dạng bài, chủ đề, độ khó
     */
    public function select(Request $request, $loaiId = null)
    {
        $loaiId = $loaiId ?: $request->query('loai_id');
        $dokho = $request->query('dokho');
        $keyword = $request->query('q');

        $types = Schema::hasTable('loaicauhoi')
            ? DB::table('loaicauhoi')->where('is_active', 1)->get()
            : $this->getDefaultQuestionTypes();

        $selectedType = $loaiId ? $types->firstWhere('id', (int)$loaiId) : null;

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

        return view('practice.select', compact('questions', 'types', 'selectedType', 'loaiId', 'dokho', 'keyword'));
    }

    /**
     * LT03: Trang làm bài luyện tập
     */
    public function show($id)
    {
        $userId = Auth::id();
        $question = null;

        if (Schema::hasTable('cauhoi')) {
            $question = DB::table('cauhoi')->where('id', $id)->first();
            if ($question) {
                // Lấy thêm lựa chọn nếu là câu trắc nghiệm (không lấy cột ladung để tránh lộ đáp án)
                if (Schema::hasTable('luachon')) {
                    $question->luachon = DB::table('luachon')
                        ->where('cauhoi_id', $id)
                        ->select('id', 'cauhoi_id', 'noidung', 'thutu')
                        ->orderBy('thutu')
                        ->get();
                }
            }
        }

        if (!$question) {
            $question = $this->getDefaultQuestions()->firstWhere('id', (int)$id) ?: $this->getDefaultQuestions()->first();
        }

        // Lấy thông tin loại câu hỏi
        $type = null;
        if (Schema::hasTable('loaicauhoi')) {
            $type = DB::table('loaicauhoi')->where('id', $question->loai_id)->first();
        }
        if (!$type) {
            $type = $this->getDefaultQuestionTypes()->firstWhere('id', $question->loai_id);
        }

        // Kiểm tra xem câu này người dùng đã bookmark chưa
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
     * LT04: Xử lý nộp bài luyện tập
     */
    public function submit(Request $request, $id)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'luachon_id' => 'nullable|integer',
            'noidung' => 'nullable|string',
            'thoigianlam' => 'nullable|integer|min:0',
        ]);

        $cauhoi = null;
        if (Schema::hasTable('cauhoi')) {
            $cauhoi = DB::table('cauhoi')->where('id', $id)->first();
        }
        if (!$cauhoi) {
            $cauhoi = $this->getDefaultQuestions()->firstWhere('id', (int)$id);
        }

        $answer = $validated['luachon_id'] ?? $validated['noidung'] ?? '';

        // Chấm điểm tự động qua ScoringService
        $scoreResult = $this->scoringService->calculateResult($cauhoi, $answer, (float)($cauhoi->diem ?? 1.0));

        // Lưu vào bảng luyentap
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

        return redirect()->route('practice.result', $attempt->id)->with('status', 'Nộp bài thành công!');
    }

    /**
     * LT05: Hiển thị kết quả sau khi nộp bài
     */
    public function result($attemptId)
    {
        $attempt = Luyentap::with('nguoidung')->findOrFail($attemptId);

        // Bảo mật: chỉ người làm bài hoặc admin mới được xem kết quả này
        if ($attempt->nguoidung_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Bạn không có quyền xem kết quả bài luyện này.');
        }

        $cauhoi = null;
        if (Schema::hasTable('cauhoi')) {
            $cauhoi = DB::table('cauhoi')->where('id', $attempt->cauhoi_id)->first();
            if ($cauhoi && Schema::hasTable('luachon')) {
                $cauhoi->luachon = DB::table('luachon')->where('cauhoi_id', $cauhoi->id)->get();
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

        // Đánh dấu bài luyện tập ở trạng thái chờ AI chấm (pending)
        $attempt->update([
            'trangthai_ai' => 'pending',
        ]);

        return back()->with('status', 'Bài làm đã được chuyển vào hàng đợi chấm điểm AI. Kết quả sẽ được cập nhật sớm nhất!');
    }

    /**
     * LT08 & LT09: Lịch sử luyện tập & tìm kiếm
     */
    public function history(Request $request)
    {
        $userId = Auth::id();
        $status = $request->query('status'); // 'dung', 'sai'
        $keyword = $request->query('q');

        $query = Luyentap::where('nguoidung_id', $userId)
            ->when($status === 'dung', fn($q) => $q->where('ladung', 1))
            ->when($status === 'sai', fn($q) => $q->where('ladung', 0))
            ->when($keyword, fn($q) => $q->where('noidung', 'like', "%{$keyword}%"))
            ->orderByDesc('id');

        $attempts = $query->paginate(15)->withQueryString();

        // Thống kê nhanh
        $totalAttempts = Luyentap::where('nguoidung_id', $userId)->count();
        $correctAttempts = Luyentap::where('nguoidung_id', $userId)->where('ladung', 1)->count();
        $accuracy = $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100, 1) : 0;

        return view('practice.history', compact('attempts', 'totalAttempts', 'correctAttempts', 'accuracy', 'status', 'keyword'));
    }
}

