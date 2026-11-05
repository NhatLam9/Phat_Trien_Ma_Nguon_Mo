<?php

namespace App\Console\Commands;

use App\Models\Traloi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanOldAudioFiles extends Command
{
    /**
     * Tên lệnh Artisan.
     */
    protected $signature = 'ai:clean-audio {--days=30 : Số ngày giữ file âm thanh}';

    /**
     * Mô tả lệnh.
     */
    protected $description = 'Dọn dẹp các file audio Speaking cũ hơn 30 ngày và đặt traloi.audio = NULL';

    /**
     * Thực thi lệnh console.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Bắt đầu quét và dọn dẹp các file audio cũ hơn {$days} ngày (trước {$cutoffDate->toDateTimeString()})...");

        $records = Traloi::query()
            ->whereNotNull('audio')
            ->where('thoigian', '<', $cutoffDate)
            ->get();

        $count = 0;

        foreach ($records as $traloi) {
            $audioPath = $traloi->audio;

            if ($audioPath) {
                if (Storage::disk('local')->exists($audioPath)) {
                    Storage::disk('local')->delete($audioPath);
                }
            }

            $traloi->audio = null;
            $traloi->save();
            $count++;
        }

        $this->info("Đã dọn dẹp thành công {$count} file ghi âm Speaking cũ.");
        Log::info("CleanOldAudioFiles: Đã xóa {$count} file âm thanh cũ hơn {$days} ngày.");

        return Command::SUCCESS;
    }
}

