<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Danhdau;

class BookmarkController extends Controller
{
    /**
     * Dữ liệu chuẩn các kỹ năng Duolingo English Test (DET)
     */
    public function getSkillMeta(): array
    {
        return [
            'reading' => [
                'name' => 'Reading (Kỹ năng Đọc)',
                'short' => 'Đọc',
                'icon' => '📖',
                'badge' => 'badge-reading',
                'color' => '#0284c7',
                'bg' => '#f0f9ff',
                'border' => '#bae6fd',
            ],
            'listening' => [
                'name' => 'Listening (Kỹ năng Nghe)',
                'short' => 'Nghe',
                'icon' => '🎧',
                'badge' => 'badge-listening',
                'color' => '#7c3aed',
                'bg' => '#f5f3ff',
                'border' => '#ddd6fe',
            ],
            'writing' => [
                'name' => 'Writing (Kỹ năng Viết)',
                'short' => 'Viết',
                'icon' => '✍️',
                'badge' => 'badge-writing',
                'color' => '#d97706',
                'bg' => '#fffbeb',
                'border' => '#fde68a',
            ],
            'speaking' => [
                'name' => 'Speaking (Kỹ năng Nói)',
                'short' => 'Nói',
                'icon' => '🎙️',
                'badge' => 'badge-speaking',
                'color' => '#16a34a',
                'bg' => '#f0fdf4',
                'border' => '#bbf7d0',
            ],
        ];
    }

    protected function getDefaultQuestions()
    {
        return collect([
            (object)[
                'id' => 1,
                'loai_id' => 1,
                'noidung' => 'Select the real English words from the list below.',
                'dokho' => 3,
                'thoigian' => 60,
                'diem' => 1.0,
            ],
            (object)[
                'id' => 2,
                'loai_id' => 2,
                'noidung' => 'Type the missing letters to complete the sentence.',
                'dokho' => 5,
                'thoigian' => 60,
                'diem' => 1.0,
            ],
            (object)[
                'id' => 3,
                'loai_id' => 6,
                'noidung' => 'Write a short description of the image in at least 50 words.',
                'dokho' => 7,
                'thoigian' => 60,
                'diem' => 10.0,
            ],
            (object)[
                'id' => 4,
                'loai_id' => 8,
                'noidung' => 'Speak into the microphone and answer the following topic.',
                'dokho' => 8,
                'thoigian' => 300,
                'diem' => 10.0,
            ],
        ]);
    }

    protected function getDefaultQuestionTypes()
    {
        return collect([
            (object)['id' => 1, 'ma' => 'read_and_select', 'ten' => 'Read and Select', 'kynang' => 'reading', 'mota' => 'Đọc và chọn từ tiếng Anh có nghĩa thực'],
            (object)['id' => 2, 'ma' => 'read_and_complete', 'ten' => 'Read and Complete', 'kynang' => 'reading', 'mota' => 'Điền ký tự còn thiếu vào đoạn văn'],
            (object)['id' => 3, 'ma' => 'interactive_reading', 'ten' => 'Interactive Reading', 'kynang' => 'reading', 'mota' => 'Đọc đoạn văn dài và trả lời các câu hỏi tương tác'],
            (object)['id' => 4, 'ma' => 'listen_and_type', 'ten' => 'Listen and Type', 'kynang' => 'listening', 'mota' => 'Nghe một câu phát âm và gõ lại chính xác'],
            (object)['id' => 5, 'ma' => 'interactive_listening', 'ten' => 'Interactive Listening', 'kynang' => 'listening', 'mota' => 'Nghe đoạn hội thoại và chọn đáp án tương tác'],
            (object)['id' => 6, 'ma' => 'write_about_photo', 'ten' => 'Write About the Photo', 'kynang' => 'writing', 'mota' => 'Viết ít nhất một câu miêu tả hình ảnh'],
            (object)['id' => 7, 'ma' => 'read_then_write', 'ten' => 'Read, Then Write', 'kynang' => 'writing', 'mota' => 'Đọc chủ đề và viết bài văn ít nhất 50 từ'],
            (object)['id' => 8, 'ma' => 'read_aloud', 'ten' => 'Read Aloud', 'kynang' => 'speaking', 'mota' => 'Đọc to câu văn hiển thị trên màn hình'],
            (object)['id' => 9, 'ma' => 'speak_about_photo', 'ten' => 'Speak About the Photo', 'kynang' => 'speaking', 'mota' => 'Nói miêu tả một hình ảnh trong 90 giây'],
        ]);
    }

    /**
     * LT07: Hiển thị danh sách các câu hỏi đã lưu & Dạng bài quan tâm
     * Phân loại đầy đủ theo 4 kỹ năng (Reading, Listening, Writing, Speaking) & Dạng bài
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $skillMeta = $this->getSkillMeta();

        $selectedSkill = $request->query('skill', 'all');
        $selectedLoai = $request->query('loai_id');
        $selectedDokho = $request->query('dokho');
        $keyword = trim((string)$request->query('q', ''));
        $activeTab = $request->query('tab', 'questions'); // 'questions' hoặc 'types'

        // Lấy danh sách tất cả các dạng bài để đổ vào bộ lọc
        $types = collect();
        if (Schema::hasTable('loaicauhoi')) {
            $types = DB::table('loaicauhoi')->orderBy('id')->get();
        }
        if ($types->isEmpty()) {
            $types = $this->getDefaultQuestionTypes();
        }

        // Lấy danh sách bookmark của người dùng
        $userBookmarks = collect();
        if (Schema::hasTable('danhdau')) {
            $userBookmarks = Danhdau::where('nguoidung_id', $userId)
                ->orderByDesc('id')
                ->get();
        }

        // Nạp thông tin chi tiết câu hỏi (kèm Dạng bài & Kỹ năng)
        if (Schema::hasTable('cauhoi') && $userBookmarks->isNotEmpty()) {
            $questionIds = $userBookmarks->pluck('cauhoi_id')->filter()->unique()->toArray();
            
            $qQuery = DB::table('cauhoi')->whereIn('cauhoi.id', $questionIds);
            if (Schema::hasTable('loaicauhoi')) {
                $qQuery->leftJoin('loaicauhoi', 'cauhoi.loai_id', '=', 'loaicauhoi.id')
                    ->select(
                        'cauhoi.*',
                        'loaicauhoi.ten as loai_ten',
                        'loaicauhoi.kynang as loai_kynang',
                        'loaicauhoi.ma as loai_ma'
                    );
            }
            $dbQuestions = $qQuery->get()->keyBy('id');

            foreach ($userBookmarks as $bm) {
                $q = $dbQuestions->get($bm->cauhoi_id);
                if (!$q) {
                    $defaultQ = $this->getDefaultQuestions()->firstWhere('id', (int)$bm->cauhoi_id);
                    if ($defaultQ) {
                        $defaultType = $types->firstWhere('id', $defaultQ->loai_id);
                        $defaultQ->loai_ten = $defaultType->ten ?? 'Tổng hợp';
                        $defaultQ->loai_kynang = $defaultType->kynang ?? 'reading';
                        $defaultQ->loai_ma = $defaultType->ma ?? '';
                        $q = $defaultQ;
                    }
                }

                if ($q) {
                    if (empty($q->loai_kynang) && !empty($bm->kynang)) {
                        $q->loai_kynang = $bm->kynang;
                    }
                    if (empty($q->loai_ten) && !empty($bm->loai_id)) {
                        $t = $types->firstWhere('id', $bm->loai_id);
                        $q->loai_ten = $t->ten ?? '';
                        $q->loai_kynang = $t->kynang ?? ($q->loai_kynang ?? 'reading');
                    }
                    if (empty($q->loai_kynang)) {
                        $t = $types->firstWhere('id', $q->loai_id ?? 1);
                        $q->loai_kynang = $t->kynang ?? 'reading';
                        $q->loai_ten = $t->ten ?? 'Tổng hợp';
                    }
                } else {
                    $q = (object)[
                        'id' => $bm->cauhoi_id,
                        'noidung' => 'Câu hỏi đã lưu #' . $bm->cauhoi_id,
                        'dokho' => 5,
                        'diem' => 1.0,
                        'loai_id' => $bm->loai_id ?? 1,
                        'loai_ten' => $bm->loai_id ? ($types->firstWhere('id', $bm->loai_id)->ten ?? 'Tổng hợp') : 'Tổng hợp',
                        'loai_kynang' => $bm->kynang ?? 'reading',
                        'loai_ma' => '',
                    ];
                }

                $bm->cauhoi = $q;
                $bm->kynang = $q->loai_kynang ?? ($bm->kynang ?? 'reading');
                $bm->loai_ten = $q->loai_ten ?? 'Tổng hợp';
                $bm->dokho = $q->dokho ?? 5;
            }
        }

        // Thống kê số lượng câu hỏi đã lưu theo 4 kỹ năng
        $stats = [
            'total' => $userBookmarks->count(),
            'reading' => $userBookmarks->filter(fn($b) => ($b->kynang ?? '') === 'reading')->count(),
            'listening' => $userBookmarks->filter(fn($b) => ($b->kynang ?? '') === 'listening')->count(),
            'writing' => $userBookmarks->filter(fn($b) => ($b->kynang ?? '') === 'writing')->count(),
            'speaking' => $userBookmarks->filter(fn($b) => ($b->kynang ?? '') === 'speaking')->count(),
        ];

        // Áp dụng bộ lọc cho danh sách câu hỏi
        $filteredBookmarks = $userBookmarks;

        if ($selectedSkill !== 'all' && in_array($selectedSkill, ['reading', 'listening', 'writing', 'speaking'])) {
            $filteredBookmarks = $filteredBookmarks->filter(fn($b) => ($b->kynang ?? '') === $selectedSkill);
        }

        if (!empty($selectedLoai)) {
            $filteredBookmarks = $filteredBookmarks->filter(function($b) use ($selectedLoai) {
                return (isset($b->cauhoi->loai_id) && $b->cauhoi->loai_id == $selectedLoai)
                    || (isset($b->loai_id) && $b->loai_id == $selectedLoai);
            });
        }

        if (!empty($selectedDokho)) {
            $filteredBookmarks = $filteredBookmarks->filter(function($b) use ($selectedDokho) {
                return isset($b->dokho) && $b->dokho == $selectedDokho;
            });
        }

        if (!empty($keyword)) {
            $filteredBookmarks = $filteredBookmarks->filter(function($b) use ($keyword) {
                $content = $b->cauhoi->noidung ?? '';
                $typeTitle = $b->loai_ten ?? '';
                return (stripos($content, $keyword) !== false) || (stripos($typeTitle, $keyword) !== false);
            });
        }

        // Phân trang thủ công cho Collection
        $perPage = 15;
        $currentPage = (int) $request->input('page', 1);
        $totalItems = $filteredBookmarks->count();
        $pagedItems = $filteredBookmarks->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $bookmarks = new LengthAwarePaginator(
            $pagedItems,
            $totalItems,
            $perPage,
            $currentPage,
            ['path' => route('practice.bookmarks'), 'query' => $request->query()]
        );

        // 2. Lấy danh sách Dạng bài đã lưu (Saved Question Types)
        $savedTypeIds = session("saved_loai_{$userId}", []);
        if (Schema::hasTable('danhdau_loai')) {
            $dbSaved = DB::table('danhdau_loai')->where('nguoidung_id', $userId)->pluck('loai_id')->toArray();
            $savedTypeIds = array_unique(array_merge($savedTypeIds, $dbSaved));
        }

        $savedTypes = $types->whereIn('id', $savedTypeIds)->values();

        // Gán thêm số lượng câu hỏi và cấp độ cho từng dạng bài đã lưu
        foreach ($savedTypes as $st) {
            $stQuestionCount = 0;
            if (Schema::hasTable('cauhoi')) {
                $stQuestionCount = DB::table('cauhoi')->where('loai_id', $st->id)->count();
            }
            $st->question_count = $stQuestionCount ?: 8;
        }

        return view('practice.danhdau', compact(
            'bookmarks',
            'stats',
            'types',
            'skillMeta',
            'selectedSkill',
            'selectedLoai',
            'selectedDokho',
            'keyword',
            'savedTypes',
            'savedTypeIds',
            'activeTab'
        ));
    }

    /**
     * Thêm hoặc bỏ đánh dấu (toggle bookmark) một câu hỏi
     * Hỗ trợ lưu kèm Kỹ năng & Dạng bài và trả về JSON tức thì không cần reload
     */
    public function toggle(Request $request, $cauhoiId)
    {
        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu câu hỏi.'], 401);
            }
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để lưu câu hỏi.');
        }

        $cauhoiId = (int)$cauhoiId;
        $loaiId = null;
        $kynang = null;
        $cauhoi = null;

        if (Schema::hasTable('cauhoi')) {
            $cauhoi = DB::table('cauhoi')->where('id', $cauhoiId)->first();
        }

        if (!$cauhoi) {
            $cauhoi = $this->getDefaultQuestions()->firstWhere('id', $cauhoiId);
            // Đảm bảo không vi phạm khóa ngoại cauhoi_id nếu dùng câu hỏi mặc định
            if ($cauhoi && Schema::hasTable('cauhoi')) {
                try {
                    $validLoaiId = 1;
                    if (Schema::hasTable('loaicauhoi')) {
                        $firstLoai = DB::table('loaicauhoi')->value('id');
                        if ($firstLoai) $validLoaiId = $firstLoai;
                    }
                    DB::table('cauhoi')->updateOrInsert(
                        ['id' => $cauhoiId],
                        [
                            'loai_id' => $cauhoi->loai_id ?? $validLoaiId,
                            'noidung' => $cauhoi->noidung ?? ('Câu hỏi luyện tập #' . $cauhoiId),
                            'dokho' => $cauhoi->dokho ?? 5,
                            'diem' => $cauhoi->diem ?? 1.0,
                            'trangthai' => 'published',
                        ]
                    );
                } catch (\Throwable $e) {
                    // Tiếp tục xử lý
                }
            }
        }

        if ($cauhoi) {
            $loaiId = $cauhoi->loai_id ?? null;
            if ($loaiId && Schema::hasTable('loaicauhoi')) {
                $kynang = DB::table('loaicauhoi')->where('id', $loaiId)->value('kynang');
            }
            if (!$kynang) {
                $defaultType = $this->getDefaultQuestionTypes()->firstWhere('id', $loaiId);
                $kynang = $defaultType->kynang ?? 'reading';
            }
        }

        $bookmark = Danhdau::where('nguoidung_id', $userId)
            ->where('cauhoi_id', $cauhoiId)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $isBookmarked = false;
            $message = 'Đã bỏ lưu câu hỏi #' . $cauhoiId . ' khỏi danh sách.';
        } else {
            $data = [
                'nguoidung_id' => $userId,
                'cauhoi_id' => $cauhoiId,
                'ngaytao' => now(),
            ];

            if (Schema::hasColumn('danhdau', 'loai_id') && $loaiId) {
                $data['loai_id'] = $loaiId;
            }
            if (Schema::hasColumn('danhdau', 'kynang') && $kynang) {
                $data['kynang'] = $kynang;
            }

            Danhdau::create($data);
            $isBookmarked = true;
            $message = 'Đã lưu câu hỏi #' . $cauhoiId . ' vào danh sách ôn tập!';
        }

        $totalBookmarks = Danhdau::where('nguoidung_id', $userId)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'cauhoi_id' => $cauhoiId,
                'total_bookmarks' => $totalBookmarks,
                'kynang' => $kynang,
                'loai_id' => $loaiId,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Thêm hoặc bỏ lưu một Dạng bài (Question Type) & Kỹ năng
     */
    public function toggleType(Request $request, $loaiId)
    {
        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
            }
            return redirect()->route('login');
        }

        $loaiId = (int)$loaiId;
        $kynang = null;
        if (Schema::hasTable('loaicauhoi')) {
            $kynang = DB::table('loaicauhoi')->where('id', $loaiId)->value('kynang');
        }
        if (!$kynang) {
            $kynang = $this->getDefaultQuestionTypes()->firstWhere('id', $loaiId)->kynang ?? 'reading';
        }

        $isBookmarked = false;
        $savedLoai = session("saved_loai_{$userId}", []);

        if (Schema::hasTable('danhdau_loai')) {
            $row = DB::table('danhdau_loai')
                ->where('nguoidung_id', $userId)
                ->where('loai_id', $loaiId)
                ->first();

            if ($row) {
                DB::table('danhdau_loai')
                    ->where('nguoidung_id', $userId)
                    ->where('loai_id', $loaiId)
                    ->delete();
                $isBookmarked = false;
                $savedLoai = array_diff($savedLoai, [$loaiId]);
            } else {
                DB::table('danhdau_loai')->insert([
                    'nguoidung_id' => $userId,
                    'loai_id' => $loaiId,
                    'kynang' => $kynang,
                    'ngaytao' => now(),
                ]);
                $isBookmarked = true;
                if (!in_array($loaiId, $savedLoai)) {
                    $savedLoai[] = $loaiId;
                }
            }
        } else {
            if (in_array($loaiId, $savedLoai)) {
                $savedLoai = array_diff($savedLoai, [$loaiId]);
                $isBookmarked = false;
            } else {
                $savedLoai[] = $loaiId;
                $isBookmarked = true;
            }
        }

        session(["saved_loai_{$userId}" => array_values($savedLoai)]);

        $message = $isBookmarked ? 'Đã lưu dạng bài vào danh sách ôn tập!' : 'Đã bỏ lưu dạng bài.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_bookmarked' => $isBookmarked,
                'loai_id' => $loaiId,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Bắt đầu làm bài luyện tập với các câu hỏi đã lưu
     */
    public function practiceSaved(Request $request)
    {
        $userId = Auth::id();
        $skill = $request->query('skill');

        $query = Danhdau::where('nguoidung_id', $userId);
        if ($skill && $skill !== 'all' && Schema::hasColumn('danhdau', 'kynang')) {
            $query->where('kynang', $skill);
        }
        $savedQids = $query->pluck('cauhoi_id')->toArray();

        if (empty($savedQids)) {
            return redirect()->route('practice.bookmarks')
                ->with('warning', 'Bạn chưa có câu hỏi đã lưu nào để bắt đầu bài luyện tập.');
        }

        // Tìm loại câu hỏi của câu đầu tiên
        $firstQ = null;
        if (Schema::hasTable('cauhoi')) {
            $firstQ = DB::table('cauhoi')->whereIn('id', $savedQids)->first();
        }
        $loaiId = $firstQ->loai_id ?? 1;

        return redirect()->route('practice.session', ['loai_id' => $loaiId])
            ->with('status', 'Bắt đầu rèn luyện với các câu hỏi tương ứng trong ngân hàng câu hỏi!');
    }
}
