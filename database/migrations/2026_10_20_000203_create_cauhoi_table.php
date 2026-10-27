<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cauhoi', function (Blueprint $table) {
            $table->id()->comment('Mã câu hỏi');
            $table->unsignedBigInteger('loai_id')->comment('Mã loại câu hỏi');
            $table->unsignedBigInteger('danhmuc_id')->nullable()->comment('Mã danh mục');
            $table->text('noidung')->comment('Nội dung câu hỏi');
            $table->text('giaithich')->nullable()->comment('Giải thích đáp án');
            $table->unsignedTinyInteger('dokho')->default(5)->comment('Độ khó từ 1 đến 10');
            $table->string('anh', 255)->nullable()->comment('Hình ảnh câu hỏi');
            $table->string('audio', 255)->nullable()->comment('File âm thanh');
            $table->unsignedInteger('thoigian')->nullable()->comment('Thời gian làm câu tính bằng giây');
            $table->decimal('diem', 6, 2)->default(1)->comment('Điểm câu hỏi');
            $table->enum('trangthai', ['draft', 'review', 'published', 'archived'])->default('draft')->comment('Trạng thái câu hỏi');
            $table->unsignedBigInteger('nguoitao')->nullable()->comment('Người tạo câu hỏi');
            $table->dateTime('ngaytao')->useCurrent()->comment('Ngày tạo');
            $table->dateTime('ngaycapnhat')->useCurrent()->useCurrentOnUpdate()->comment('Ngày cập nhật');

            $table->index(['loai_id', 'dokho', 'trangthai'], 'idx_cauhoi_loai');
            $table->index('danhmuc_id', 'idx_cauhoi_danhmuc');

            $table->foreign('loai_id')->references('id')->on('loaicauhoi');
            $table->foreign('danhmuc_id')->references('id')->on('danhmuc')->nullOnDelete();
            $table->foreign('nguoitao')->references('id')->on('nguoidung')->nullOnDelete();
        });

        DB::statement('ALTER TABLE cauhoi ADD CONSTRAINT chk_cauhoi_dokho CHECK (dokho BETWEEN 1 AND 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cauhoi');
    }
};
