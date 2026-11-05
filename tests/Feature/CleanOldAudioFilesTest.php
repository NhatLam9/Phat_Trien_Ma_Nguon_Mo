<?php

namespace Tests\Feature;

use App\Models\Cauhoi;
use App\Models\Lanthi;
use App\Models\Lanthicauhoi;
use App\Models\Loaicauhoi;
use App\Models\Nguoidung;
use App\Models\Traloi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanOldAudioFilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_clean_audio_command_deletes_old_files_and_sets_audio_null(): void
    {
        Storage::fake('local');

        $user = Nguoidung::create([
            'hoten' => 'Audio Student',
            'email' => 'audio@example.com',
            'matkhau' => Hash::make('password123'),
        ]);

        $lanThi = Lanthi::create([
            'nguoidung_id' => $user->id,
            'trangthai' => 'practice',
        ]);

        $loai = Loaicauhoi::create([
            'ma' => 'read_aloud',
            'ten' => 'Read Aloud',
            'kynang' => 'speaking',
            'cachcham' => 'ai',
        ]);

        $cauhoi = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Please read the text aloud.',
        ]);

        $cauhoi2 = Cauhoi::create([
            'loai_id' => $loai->id,
            'noidung' => 'Please read the second text aloud.',
        ]);

        $lanThiCauHoi1 = Lanthicauhoi::create([
            'lanthi_id' => $lanThi->id,
            'cauhoi_id' => $cauhoi->id,
            'thutu' => 1,
            'datraloi' => true,
        ]);

        $lanThiCauHoi2 = Lanthicauhoi::create([
            'lanthi_id' => $lanThi->id,
            'cauhoi_id' => $cauhoi2->id,
            'thutu' => 2,
            'datraloi' => true,
        ]);

        // File 1: Cũ 35 ngày (> 30 ngày)
        $oldPath = 'audio/old_recording.mp3';
        Storage::disk('local')->put($oldPath, 'dummy audio data');
        $oldTraloi = Traloi::create([
            'lanthicauhoi_id' => $lanThiCauHoi1->id,
            'audio' => $oldPath,
            'trangthai_ai' => 'done',
            'diem_ai' => 110,
            'thoigian' => now()->subDays(35),
        ]);

        // File 2: Mới 5 ngày (< 30 ngày)
        $newPath = 'audio/recent_recording.mp3';
        Storage::disk('local')->put($newPath, 'dummy audio data');
        $recentTraloi = Traloi::create([
            'lanthicauhoi_id' => $lanThiCauHoi2->id,
            'audio' => $newPath,
            'trangthai_ai' => 'done',
            'diem_ai' => 130,
            'thoigian' => now()->subDays(5),
        ]);

        // Chạy lệnh artisan
        $this->artisan('ai:clean-audio --days=30')
            ->assertSuccessful();

        // File cũ bị xóa khỏi storage và DB đặt audio = NULL
        Storage::disk('local')->assertMissing($oldPath);
        $this->assertNull($oldTraloi->fresh()->audio);

        // File mới vẫn được giữ nguyên
        Storage::disk('local')->assertExists($newPath);
        $this->assertEquals($newPath, $recentTraloi->fresh()->audio);
    }
}
