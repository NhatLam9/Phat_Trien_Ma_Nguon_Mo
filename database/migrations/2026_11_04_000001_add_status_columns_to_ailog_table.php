<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bổ sung cột trạng thái và mã HTTP cho nhật ký AI.
     * Mục đích: làm bằng chứng khi học viên khiếu nại bị trừ lượt mà không có điểm.
     */
    public function up(): void
    {
        Schema::table('ailog', function (Blueprint $table) {
            $table->enum('trangthai', ['success', 'failed'])
                ->default('success')
                ->after('model')
                ->comment('Kết quả lần gọi API: success hoặc failed');
            $table->unsignedSmallInteger('http_code')
                ->nullable()
                ->after('trangthai')
                ->comment('Mã HTTP trả về từ nhà cung cấp AI (nếu có)');
        });
    }

    public function down(): void
    {
        Schema::table('ailog', function (Blueprint $table) {
            $table->dropColumn(['trangthai', 'http_code']);
        });
    }
};
