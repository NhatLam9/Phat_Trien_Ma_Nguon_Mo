<?php

namespace Database\Seeders;

use App\Models\Cauhoi;
use App\Models\CauhoiDoc;
use App\Models\CauhoiNghe;
use App\Models\Danhmuc;
use App\Models\Dapan;
use App\Models\Loaicauhoi;
use App\Models\Luachon;
use App\Models\LuachonDoc;
use App\Models\LuachonNghe;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu cho module quản trị (TV3 - Phi Phong).
 *
 * Seeder chạy lại nhiều lần vẫn không tạo trùng dữ liệu.
 * Bộ dữ liệu khởi đầu: 5 chủ đề, 8 dạng bài, mỗi dạng 1 câu hỏi mẫu.
 */
class AdminCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $danhmuc = $this->seedDanhmuc();
        $loai = $this->seedLoaicauhoi();

        $this->seedReadAloud($loai['read_aloud'], $danhmuc['Daily Life']);
        $this->seedWriteAboutPhoto($loai['write_about_photo'], $danhmuc['Education']);
        $this->seedInteractiveReading($loai['interactive_reading'], $danhmuc['Science & Technology']);
        $this->seedInteractiveListening($loai['interactive_listening'], $danhmuc['Travel']);
        $this->seedReadAndComplete($loai['read_and_complete'], $danhmuc['Daily Life']);
        $this->seedWriteAboutTopic($loai['write_about_topic'], $danhmuc['Work & Business']);
        $this->seedSpeakAboutPhoto($loai['speak_about_photo'], $danhmuc['Travel']);
        $this->seedListenAndType($loai['listen_and_type'], $danhmuc['Education']);
    }

    /**
     * @return array<string, Danhmuc>
     */
    private function seedDanhmuc(): array
    {
        $duLieu = [
            'Daily Life' => 'Đời sống hằng ngày',
            'Education' => 'Giáo dục và học đường',
            'Science & Technology' => 'Khoa học và công nghệ',
            'Work & Business' => 'Công việc và kinh doanh',
            'Travel' => 'Du lịch và di chuyển',
        ];

        $ketQua = [];

        foreach ($duLieu as $ten => $mota) {
            $ketQua[$ten] = Danhmuc::firstOrCreate(['ten' => $ten], ['mota' => $mota]);
        }

        return $ketQua;
    }

    /**
     * @return array<string, Loaicauhoi>
     */
    private function seedLoaicauhoi(): array
    {
        $duLieu = [
            'read_aloud' => ['Đọc to', 'speaking', 'ai', 40],
            'write_about_photo' => ['Viết về ảnh', 'writing', 'ai', 60],
            'interactive_reading' => ['Đọc tương tác', 'reading', 'auto', 120],
            'interactive_listening' => ['Nghe tương tác', 'listening', 'auto', 120],
            'read_and_complete' => ['Đọc và điền từ', 'reading', 'auto', 90],
            'write_about_topic' => ['Viết về chủ đề', 'writing', 'ai', 300],
            'speak_about_photo' => ['Nói về ảnh', 'speaking', 'ai', 90],
            'listen_and_type' => ['Nghe và gõ lại', 'listening', 'auto', 60],
        ];

        $ketQua = [];

        foreach ($duLieu as $ma => [$ten, $kynang, $cachcham, $thoigian]) {
            $ketQua[$ma] = Loaicauhoi::updateOrCreate(
                ['ma' => $ma],
                [
                    'ten' => $ten,
                    'kynang' => $kynang,
                    'cachcham' => $cachcham,
                    'thoigian' => $thoigian,
                    'is_active' => true,
                ],
            );
        }

        return $ketQua;
    }

    private function seedReadAloud(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'The museum opens at nine o\'clock on weekdays and at ten o\'clock on weekends.',
            'dokho' => 3,
            'thoigian' => 40,
        ]);

        if ($cauhoi->wasRecentlyCreated) {
            Dapan::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => 'The museum opens at nine o\'clock on weekdays and at ten o\'clock on weekends.',
                'ladung' => true,
                'thutu' => 1,
            ]);
        }
    }

    private function seedWriteAboutPhoto(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'Write one or more sentences describing the photo: a teacher is writing on a whiteboard while students take notes.',
            'dokho' => 4,
            'thoigian' => 60,
        ]);

        if ($cauhoi->wasRecentlyCreated) {
            Dapan::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => 'A teacher is writing on a whiteboard in a classroom while several students are taking notes at their desks.',
                'ladung' => true,
                'thutu' => 1,
            ]);
        }
    }

    private function seedInteractiveReading(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'Solar panels convert sunlight into electricity. Although the initial cost is high, they reduce electricity bills over time and produce no emissions while running.',
            'dokho' => 5,
            'thoigian' => 120,
        ]);

        if (! $cauhoi->wasRecentlyCreated) {
            return;
        }

        $cauHoiCon = CauhoiDoc::create([
            'cauhoi_id' => $cauhoi->id,
            'loai' => 'complete_sentence',
            'noidung' => 'Solar panels reduce electricity bills over time, even though the ___ cost is high.',
            'dapandung' => 'initial',
            'thutu' => 1,
        ]);

        foreach (['initial', 'running', 'annual', 'average'] as $thutu => $noidung) {
            LuachonDoc::create([
                'cauhoi_doc_id' => $cauHoiCon->id,
                'noidung' => $noidung,
                'ladung' => $noidung === 'initial',
                'thutu' => $thutu + 1,
            ]);
        }
    }

    private function seedInteractiveListening(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'Listen to a conversation at a train station and answer the question.',
            'audio' => 'audio/listening-demo.mp3',
            'dokho' => 5,
            'thoigian' => 120,
        ]);

        if (! $cauhoi->wasRecentlyCreated) {
            return;
        }

        $cauHoiCon = CauhoiNghe::create([
            'cauhoi_id' => $cauhoi->id,
            'loai' => 'listen_and_respond',
            'noidung' => 'What will the woman do next?',
            'dapandung' => 'She will buy a ticket for the next train.',
            'thutu' => 1,
        ]);

        $luaChon = [
            'She will buy a ticket for the next train.',
            'She will wait for her friend outside.',
            'She will take a taxi to the airport.',
        ];

        foreach ($luaChon as $thutu => $noidung) {
            LuachonNghe::create([
                'cauhoi_nghe_id' => $cauHoiCon->id,
                'noidung' => $noidung,
                'ladung' => $thutu === 0,
                'thutu' => $thutu + 1,
            ]);
        }
    }

    private function seedReadAndComplete(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'I usually ___ to work by bus when the weather is nice.',
            'dokho' => 2,
            'thoigian' => 90,
        ]);

        if (! $cauhoi->wasRecentlyCreated) {
            return;
        }

        $luaChon = ['go', 'went', 'gone', 'going'];

        foreach ($luaChon as $thutu => $noidung) {
            Luachon::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => $noidung,
                'ladung' => $noidung === 'go',
                'thutu' => $thutu + 1,
            ]);
        }
    }

    private function seedWriteAboutTopic(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'Some people prefer working from home, while others prefer working in an office. Discuss both views and give your opinion.',
            'dokho' => 7,
            'thoigian' => 300,
        ]);

        if ($cauhoi->wasRecentlyCreated) {
            Dapan::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => 'Bài viết cần nêu ưu điểm của làm việc tại nhà (linh hoạt, tiết kiệm thời gian di chuyển), ưu điểm của làm việc tại văn phòng (giao tiếp trực tiếp, tách biệt công việc và cuộc sống) và nêu quan điểm cá nhân có lý giải.',
                'ladung' => true,
                'thutu' => 1,
            ]);
        }
    }

    private function seedSpeakAboutPhoto(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'Speak about the photo for at least 30 seconds: a family is packing suitcases in a hotel room.',
            'dokho' => 6,
            'thoigian' => 90,
        ]);

        if ($cauhoi->wasRecentlyCreated) {
            Dapan::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => 'Gợi ý: mô tả người trong ảnh, hành động đang diễn ra, đồ vật xung quanh, cảm xúc và dự đoán tình huống tiếp theo.',
                'ladung' => true,
                'thutu' => 1,
            ]);
        }
    }

    private function seedListenAndType(Loaicauhoi $loai, Danhmuc $danhmuc): void
    {
        $cauhoi = $this->taoCauHoi($loai, $danhmuc, [
            'noidung' => 'Listen to the sentence and type exactly what you hear.',
            'audio' => 'audio/listen-and-type-demo.mp3',
            'dokho' => 3,
            'thoigian' => 60,
        ]);

        if ($cauhoi->wasRecentlyCreated) {
            Dapan::create([
                'cauhoi_id' => $cauhoi->id,
                'noidung' => 'The library closes at eight on Friday.',
                'ladung' => true,
                'thutu' => 1,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $thuocTinh
     */
    private function taoCauHoi(Loaicauhoi $loai, Danhmuc $danhmuc, array $thuocTinh): Cauhoi
    {
        return Cauhoi::firstOrCreate(
            ['noidung' => $thuocTinh['noidung']],
            $thuocTinh + [
                'loai_id' => $loai->id,
                'danhmuc_id' => $danhmuc->id,
                'trangthai' => 'published',
            ],
        );
    }
}
