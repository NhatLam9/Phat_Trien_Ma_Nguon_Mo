<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm cột loai_id và kynang vào bảng danhdau nếu chưa có
        if (Schema::hasTable('danhdau')) {
            Schema::table('danhdau', function (Blueprint $table) {
                if (!Schema::hasColumn('danhdau', 'loai_id')) {
                    $table->unsignedBigInteger('loai_id')->nullable()->after('cauhoi_id');
                }
                if (!Schema::hasColumn('danhdau', 'kynang')) {
                    $table->string('kynang', 50)->nullable()->after('loai_id');
                }
            });
        }

        // 2. Tạo bảng danhdau_loai để lưu các Dạng bài & Kỹ năng yêu thích
        if (!Schema::hasTable('danhdau_loai')) {
            Schema::create('danhdau_loai', function (Blueprint $table) {
                $table->id();
                $table->foreignId('nguoidung_id')->constrained('nguoidung')->onDelete('cascade');
                $table->unsignedBigInteger('loai_id')->comment('Mã dạng bài');
                $table->string('kynang', 50)->nullable()->comment('Kỹ năng: reading, listening, writing, speaking');
                $table->timestamp('ngaytao')->useCurrent();

                $table->unique(['nguoidung_id', 'loai_id'], 'uk_user_loai_bookmark');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('danhdau')) {
            Schema::table('danhdau', function (Blueprint $table) {
                if (Schema::hasColumn('danhdau', 'loai_id')) {
                    $table->dropColumn('loai_id');
                }
                if (Schema::hasColumn('danhdau', 'kynang')) {
                    $table->dropColumn('kynang');
                }
            });
        }

        Schema::dropIfExists('danhdau_loai');
    }
};

