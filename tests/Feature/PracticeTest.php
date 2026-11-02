<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;
use App\Models\Nguoidung;
use App\Models\Luyentap;
use App\Models\Danhdau;

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
        if (!Schema::hasTable('loaicauhoi')) {
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
}
