<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dethi', function (Blueprint $table) {
            $table->id()->comment('Mã đề thi');
            $table->string('ten', 150)->comment('Tên đề thi');
            $table->text('mota')->nullable()->comment('Mô tả');
            $table->enum('chedo', ['full', 'section'])->default('full')->comment('Chế độ thi');
            $table->enum('kieu', ['fixed', 'adaptive'])->default('fixed')->comment('Kiểu đề thi: fixed=cố định, adaptive=thích ứng');
            $table->enum('kynang', ['reading', 'listening', 'writing', 'speaking'])->nullable()->comment('Kỹ năng nếu thi từng phần');
            $table->unsignedInteger('thoigian')->comment('Thời gian thi tính bằng giây');
            $table->decimal('tongdiem', 7, 2)->default(0)->comment('Tổng điểm');
            $table->enum('trangthai', ['draft', 'published', 'hidden'])->default('draft')->comment('Trạng thái đề');
            $table->unsignedBigInteger('nguoitao')->nullable()->comment('Người tạo đề');
            $table->dateTime('ngaytao')->useCurrent()->comment('Ngày tạo');
            $table->dateTime('ngaycapnhat')->useCurrent()->useCurrentOnUpdate()->comment('Ngày cập nhật');

            $table->foreign('nguoitao')->references('id')->on('nguoidung')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dethi');
    }
};
