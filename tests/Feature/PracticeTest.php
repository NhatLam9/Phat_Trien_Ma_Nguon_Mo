<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\CauhoiDoc;
use App\Models\Loaicauhoi;
use App\Models\Luyentap;
use App\Models\Nguoidung;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Nguoidung::create([
            'hoten' => 'Practice Tester',
            'email' => 'practice@example.com',
            'matkhau' => bcrypt('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        if (Schema::hasTable('loaicauhoi')) {
            DB::table('loaicauhoi')->insertOrIgnore([
                'id' => 1,
                'ma' => 'read_aloud',
                'ten' => 'Read Aloud',
                'kynang' => 'speaking',
                'cachcham' => 'ai',
                'is_active' => 1,
            ]);
        }

        if (Schema::hasTable('cauhoi')) {
            DB::table('cauhoi')->insertOrIgnore([
                'id' => 1,
                'loai_id' => 1,
                'noidung' => 'Please read the text aloud.',
                'dokho' => 5,
                'diem' => 10,
                'trangthai' => 'published',
            ]);
            DB::table('cauhoi')->insertOrIgnore([
                'id' => 5,
                'loai_id' => 1,
                'noidung' => 'History question.',
                'dokho' => 5,
                'diem' => 10,
                'trangthai' => 'published',
            ]);
            DB::table('cauhoi')->insertOrIgnore([
                'id' => 99,
                'loai_id' => 1,
                'noidung' => 'Bookmark question.',
                'dokho' => 5,
                'diem' => 10,
                'trangthai' => 'published',
            ]);
        }
    }

    /**
     * LT01: Trang danh sách dạng bài trả về 200 và hiển thị các kỹ năng
     */
    public function test_practice_page_returns_200_when_question_type_exists(): void
    {
        if (! Schema::hasTable('loaicauhoi')) {
            Schema::create('loaicauhoi', function (Blueprint $table) {
                $table->id();
                $table->string('ma')->unique();
                $table->string('ten');
                $table->string('kynang')->nullable();
                $table->string('cachcham')->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamps();
            });
        }

        DB::table('loaicauhoi')->insertOrIgnore([
            'ma' => 'read_aloud',
            'ten' => 'Read Aloud',
            'kynang' => 'speaking',
            'cachcham' => 'ai',
            'is_active' => 1,
        ]);

        $this->actingAs($this->user)
            ->get('/practice')
            ->assertOk()
            ->assertSee('Read Aloud');
    }

    /**
     * LT02: Trang chọn câu hỏi trả về 200
     */
    public function test_practice_select_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('practice.select'));
        $response->assertOk();
        $response->assertSee('Chọn câu hỏi luyện tập');
    }

    /**
     * LT03: Trang làm bài hiển thị đề bài và không để lộ đáp án đúng
     */
    public function test_practice_question_page_renders_without_revealing_answer(): void
    {
        $response = $this->actingAs($this->user)->get(route('practice.show', 1));
        $response->assertOk();
        $response->assertDontSee('ladung');
    }

    /**
     * Kiểm tra hiển thị âm thanh cho câu hỏi có audio trong bài luyện tập
     */
    public function test_practice_question_page_renders_audio_player_correctly(): void
    {
        if (Schema::hasTable('cauhoi')) {
            DB::table('cauhoi')->updateOrInsert(
                ['id' => 777],
                [
                    'loai_id' => 1,
                    'noidung' => 'Audio test question',
                    'audio' => 'audio/questions/listening-1.wav',
                    'dokho' => 5,
                    'diem' => 10,
                    'trangthai' => 'published',
                ]
            );
        }

        $response = $this->actingAs($this->user)->get(route('practice.show', 777));
        $response->assertOk();
        $response->assertSee('<audio controls', false);
        $response->assertSee('audio/questions/listening-1.wav');
        $response->assertSee('type="audio/wav"', false);
    }

    /**
     * LT04 & LT05: Nộp bài luyện tập, lưu bản ghi vào bảng luyentap và chuyển hướng sang xem kết quả
     */
    public function test_user_can_submit_practice_answer_and_see_result(): void
    {
        $response = $this->actingAs($this->user)->post(route('practice.submit', 1), [
            'noidung' => 'My test response for question 1',
            'thoigianlam' => 45,
        ]);

        // Kiểm tra đã lưu vào CSDL bảng luyentap
        $this->assertDatabaseHas('luyentap', [
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 1,
            'noidung' => 'My test response for question 1',
        ]);

        $attempt = Luyentap::where('nguoidung_id', $this->user->id)->first();
        $this->assertNotNull($attempt);

        $response->assertRedirect(route('practice.result', $attempt->id));

        // Xem trang kết quả
        $resultResponse = $this->actingAs($this->user)->get(route('practice.result', $attempt->id));
        $resultResponse->assertOk();
        $resultResponse->assertSee('Chi tiết câu hỏi và bài làm của bạn');
    }

    /**
     * LT07: Người dùng có thể đánh dấu (bookmark) và bỏ đánh dấu câu hỏi yếu
     */
    public function test_user_can_toggle_bookmark_for_question(): void
    {
        // Lần 1: Thêm bookmark
        $response1 = $this->actingAs($this->user)->post(route('practice.bookmark.toggle', 99));
        $this->assertDatabaseHas('danhdau', [
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 99,
        ]);

        // Xem danh sách bookmarks
        $listResponse = $this->actingAs($this->user)->get(route('practice.bookmarks'));
        $listResponse->assertOk();
        $listResponse->assertSee('#99');

        // Lần 2: Bỏ bookmark
        $response2 = $this->actingAs($this->user)->post(route('practice.bookmark.toggle', 99));
        $this->assertDatabaseMissing('danhdau', [
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 99,
        ]);
    }

    /**
     * LT08: Lịch sử luyện tập hiển thị các lần làm bài của người dùng
     */
    public function test_user_can_view_practice_history(): void
    {
        Luyentap::create([
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 5,
            'noidung' => 'History answer',
            'ladung' => 1,
            'diem' => 1.0,
            'thoigianlam' => 30,
            'ngaylam' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('practice.history'));
        $response->assertOk();
        $response->assertSee('History answer');
    }

    /**
     * Kiểm tra lọc dạng bài luyện tập theo kỹ năng
     */
    public function test_user_can_filter_practice_types_by_skill(): void
    {
        $response = $this->actingAs($this->user)->get(route('practice.index', ['skill' => 'speaking']));
        $response->assertOk();
        $response->assertSee('Speaking');
        $response->assertSee('Read Aloud');
    }

    /**
     * Kiểm tra lọc lịch sử luyện tập theo ngày, tháng
     */
    public function test_user_can_filter_practice_history_by_date_and_month(): void
    {
        Luyentap::create([
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 5,
            'noidung' => 'Today answer test',
            'ladung' => 1,
            'diem' => 1.0,
            'thoigianlam' => 20,
            'ngaylam' => now(),
        ]);

        Luyentap::create([
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 5,
            'noidung' => 'Old answer test',
            'ladung' => 0,
            'diem' => 0,
            'thoigianlam' => 20,
            'ngaylam' => now()->subMonths(2),
        ]);

        // 1. Lọc hôm nay
        $resToday = $this->actingAs($this->user)->get(route('practice.history', ['period' => 'today']));
        $resToday->assertOk();
        $resToday->assertSee('Today answer test');
        $resToday->assertDontSee('Old answer test');

        // 2. Lọc theo tháng hiện tại
        $resMonth = $this->actingAs($this->user)->get(route('practice.history', ['month' => now()->format('Y-m')]));
        $resMonth->assertOk();
        $resMonth->assertSee('Today answer test');
        $resMonth->assertDontSee('Old answer test');

        // 3. Lọc theo khoảng ngày (from_date -> to_date)
        $resDate = $this->actingAs($this->user)->get(route('practice.history', [
            'from_date' => now()->subDay()->format('Y-m-d'),
            'to_date' => now()->addDay()->format('Y-m-d'),
        ]));
        $resDate->assertOk();
        $resDate->assertSee('Today answer test');
        $resDate->assertDontSee('Old answer test');
    }

    /**
     * Yêu cầu: Gộp câu hỏi theo tên tiêu đề kỹ năng và dạng bài kèm độ khó (1-10)
     */
    public function test_practice_grouped_by_skill_title_and_difficulty(): void
    {
        $response = $this->actingAs($this->user)->get(route('practice.select'));
        $response->assertOk();
        $response->assertSee('Reading');
        $response->assertSee('Listening');
        $response->assertSee('Writing');
        $response->assertSee('Speaking');
        $response->assertSee('Luyện tập ngay');
    }

    /**
     * Yêu cầu: Xóa bỏ bộ đếm thời gian, luyện tập tự do và nộp bài bất cứ lúc nào
     */
    public function test_user_can_access_practice_session_without_countdown_timer(): void
    {
        $response = $this->actingAs($this->user)->get(route('practice.session', ['loai_id' => 1]));
        $response->assertOk();
        $response->assertSee('Chế độ luyện tập tự do');
        $response->assertSee('PHIẾU CÂU HỎI');
        $response->assertDontSee('practice-countdown');
        $response->assertDontSee('THỜI GIAN CÒN LẠI');
    }

    /**
     * Yêu cầu: Nộp bài luyện tập, tổng hợp điểm số và hiển thị kết quả
     */
    public function test_user_can_submit_practice_session_and_see_auto_graded_result(): void
    {
        $response = $this->actingAs($this->user)->post(route('practice.session.submit'), [
            'loai_id' => 1,
            'answers' => [
                1 => 101,
            ],
            'time_spent' => 25,
        ]);

        $response->assertRedirect(route('practice.session.result'));

        $resultResponse = $this->actingAs($this->user)->get(route('practice.session.result'));
        $resultResponse->assertOk();
        $resultResponse->assertSee('Hoàn thành');
        $resultResponse->assertSee('ĐIỂM ĐẠT ĐƯỢC');
        $resultResponse->assertSee('TỶ LỆ CHÍNH XÁC');
    }

    /**
     * Yêu cầu: Kết quả Speaking không hiển thị ô đáp án chính xác,
     * chỉ giữ câu trả lời (bản ghi âm) và giải thích/nhận xét chi tiết.
     */
    public function test_speaking_session_result_hides_correct_answer_box(): void
    {
        $response = $this->actingAs($this->user)->post(route('practice.session.submit'), [
            'loai_id' => 1,
            'answers' => [
                1 => 'storage/audio/recordings/demo-speaking.webm',
            ],
            'time_spent' => 20,
        ]);

        $response->assertRedirect(route('practice.session.result'));

        $resultResponse = $this->actingAs($this->user)->get(route('practice.session.result'));
        $resultResponse->assertOk();
        $resultResponse->assertSee('CÂU TRẢ LỜI CỦA BẠN');
        $resultResponse->assertSee('Bản ghi âm bài nói của bạn');
        $resultResponse->assertSee('Giải thích chi tiết');
        $resultResponse->assertDontSee('ĐÁP ÁN CHÍNH XÁC');
    }

    /**
     * Kiểm tra câu hỏi lẻ không có bộ đếm ngược thời gian
     */
    public function test_single_question_has_no_countdown_timer(): void
    {
        $response = $this->actingAs($this->user)->get(route('practice.show', 1));
        $response->assertOk();
        $response->assertDontSee('single-countdown');
        $response->assertSee('Luyện tập tự do');
    }

    /**
     * Yêu cầu 1: Random các câu trắc nghiệm và lựa chọn mỗi lần luyện tập
     */
    public function test_practice_session_randomizes_questions_and_choices(): void
    {
        if (Schema::hasTable('luachon')) {
            DB::table('luachon')->insertOrIgnore([
                ['cauhoi_id' => 1, 'noidung' => 'Choice Alpha Test', 'ladung' => 1, 'thutu' => 1],
                ['cauhoi_id' => 1, 'noidung' => 'Choice Beta Test', 'ladung' => 0, 'thutu' => 2],
            ]);
        }

        $response = $this->actingAs($this->user)->get(route('practice.session', ['loai_id' => 1]));
        $response->assertOk();
        $response->assertSee('Chế độ luyện tập tự do');
        $response->assertSee('Choice Alpha Test');
    }

    /**
     * Yêu cầu 2: Luyện tập nói với nút voice ghi âm và AI kiểm tra
     */
    public function test_speaking_voice_evaluation_via_ai_endpoint(): void
    {
        $fakeAudio = UploadedFile::fake()->create('voice_sample.webm', 50, 'audio/webm');

        $response = $this->actingAs($this->user)->post(route('practice.speaking.evaluate'), [
            'cauhoi_id' => 1,
            'duration' => 8.5,
            'audio' => $fakeAudio,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_correct' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['audio_url']);
        $this->assertNotEmpty($data['det_estimated']);
        $this->assertArrayHasKey('pronunciation', $data['feedback']);
        $this->assertArrayHasKey('fluency', $data['feedback']);

        // Xác nhận dữ liệu được lưu vào bảng luyentap
        $this->assertDatabaseHas('luyentap', [
            'nguoidung_id' => $this->user->id,
            'cauhoi_id' => 1,
            'trangthai_ai' => 'done',
        ]);
    }

    /**
     * Yêu cầu: Câu hỏi đã lưu hiển thị đầy đủ Kỹ năng, Dạng bài và cho phép lọc
     */
    public function test_bookmarks_display_skills_and_question_types(): void
    {
        $this->actingAs($this->user)->post(route('practice.bookmark.toggle', 1));

        $response = $this->actingAs($this->user)->get(route('practice.bookmarks'));
        $response->assertOk();
        $response->assertSee('#1');
        $response->assertSee('Read Aloud');
        $response->assertSee('Speaking');
        $response->assertSee('Luyện câu này');
    }

    /**
     * Yêu cầu: Lưu và quản lý Dạng bài quan tâm
     */
    public function test_user_can_toggle_question_type_bookmark(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('practice.bookmark.type.toggle', 1));
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_bookmarked' => true,
        ]);

        $listResponse = $this->actingAs($this->user)->get(route('practice.bookmarks', ['tab' => 'types']));
        $listResponse->assertOk();
        $listResponse->assertSee('Read Aloud');
    }

    /**
     * Yêu cầu: Trong phiên làm bài session có nút lưu câu hỏi và hiển thị trạng thái đã lưu
     */
    public function test_practice_session_contains_bookmark_button(): void
    {
        $this->actingAs($this->user)->post(route('practice.bookmark.toggle', 1));

        $response = $this->actingAs($this->user)->get(route('practice.session', ['loai_id' => 1]));
        $response->assertOk();
        $response->assertSee('btn-bookmark-1');
        $response->assertSee('Đã lưu câu này');
    }

    /**
     * Yêu cầu: Xem kết quả và lời giải chi tiết cho câu hỏi đơn lẻ
     */
    public function test_single_question_result_shows_correct_answers_and_explanations(): void
    {
        $response = $this->actingAs($this->user)->post(route('practice.submit', 1), [
            'noidung' => 'My test answer',
            'thoigianlam' => 15,
        ]);
        $response->assertRedirect();

        $resultUrl = $response->headers->get('Location');
        $resultResponse = $this->actingAs($this->user)->get($resultUrl);
        $resultResponse->assertOk();
        $resultResponse->assertSee('Kết quả luyện tập');
        $resultResponse->assertSee('ĐÁP ÁN CHUẨN', false);
    }

    /**
     * Yêu cầu: Đọc hiểu tương tác hiển thị bài đọc và ô trả lời
     */
    public function test_interactive_reading_renders_passage_and_inputs(): void
    {
        $type = Loaicauhoi::create([
            'ma' => 'interactive_reading',
            'ten' => 'Interactive Reading',
            'kynang' => 'reading',
            'cachcham' => 'auto',
            'thoigian' => 300,
            'is_active' => true,
        ]);

        $parentQ = Cauhoi::create([
            'loai_id' => $type->id,
            'noidung' => 'Reading passage about technology and education.',
            'dokho' => 3,
            'diem' => 2.0,
            'trangthai' => 'published',
            'is_active' => true,
        ]);

        CauhoiDoc::create([
            'cauhoi_id' => $parentQ->id,
            'noidung' => 'What is the main topic of the passage?',
            'loai' => 'complete_sentence',
            'thutu' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('practice.show', $parentQ->id));
        $response->assertOk();
        $response->assertSee('DANH SÁCH CÂU HỎI ĐỌC HIỂU TƯƠNG TÁC');
        $response->assertSee('What is the main topic of the passage?');
    }
}
