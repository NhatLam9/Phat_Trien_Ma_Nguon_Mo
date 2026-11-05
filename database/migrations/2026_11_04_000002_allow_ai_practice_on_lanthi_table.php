<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phiên luyện tập AI không gắn với đề thi (dethi_id = null) và dùng
     * trạng thái riêng "practice", nên cần nới lỏng bảng lanthi của main.
     */
    public function up(): void
    {
        Schema::table('lanthi', function (Blueprint $table) {
            $table->unsignedBigInteger('dethi_id')->nullable()->change();
            $table->string('trangthai', 30)->default('in_progress')->change();
        });
    }

    public function down(): void
    {
        Schema::table('lanthi', function (Blueprint $table) {
            $table->unsignedBigInteger('dethi_id')->nullable(false)->change();
            $table->enum('trangthai', ['in_progress', 'completed', 'abandoned'])
                ->default('in_progress')
                ->change();
        });
    }
};
