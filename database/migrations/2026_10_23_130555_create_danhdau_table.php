<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danhdau', function (Blueprint $table) {
            $table->id()->comment('Mã đánh dấu');
            $table->foreignId('nguoidung_id')->constrained('nguoidung')->onDelete('cascade');
            $table->unsignedBigInteger('cauhoi_id')->comment('Câu hỏi được đánh dấu');
            $table->timestamp('ngaytao')->useCurrent()->comment('Ngày tạo');

            // Đảm bảo mỗi người dùng chỉ đánh dấu 1 câu hỏi 1 lần duy nhất
            $table->unique(['nguoidung_id', 'cauhoi_id'], 'uk_user_cauhoi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danhdau');
    }
};