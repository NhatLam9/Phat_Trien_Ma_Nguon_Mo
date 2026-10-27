<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loaicauhoi', function (Blueprint $table) {
            $table->id()->comment('Mã loại câu hỏi');
            $table->string('ma', 50)->unique()->comment('Mã loại (vd: read_aloud)');
            $table->string('ten', 100)->comment('Tên loại câu hỏi');
            $table->enum('kynang', ['reading', 'listening', 'writing', 'speaking'])->comment('Kỹ năng');
            $table->enum('cachcham', ['auto', 'ai', 'manual'])->default('auto')->comment('Cách chấm: auto=tự động, ai=AI, manual=thủ công');
            $table->unsignedInteger('thoigian')->nullable()->comment('Thời gian mặc định tính bằng giây');
            $table->text('mota')->nullable()->comment('Mô tả');
            $table->boolean('is_active')->default(true)->comment('1=bật, 0=tắt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loaicauhoi');
    }
};
