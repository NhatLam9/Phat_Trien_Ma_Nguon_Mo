<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luyentap', function (Blueprint $table) {
            $table->id()->comment('Mã lần luyện tập');
            $table->foreignId('nguoidung_id')->constrained('nguoidung')->onDelete('cascade')->comment('Người luyện tập');
            $table->unsignedBigInteger('cauhoi_id')->comment('Câu hỏi');
            $table->longText('noidung')->nullable()->comment('Nội dung trả lời');
            $table->unsignedBigInteger('luachon_id')->nullable()->comment('Lựa chọn đã chọn');
            $table->string('audio', 255)->nullable()->comment('File ghi âm');
            $table->tinyInteger('ladung')->nullable()->comment('1=đúng, 0=sai, NULL=chưa chấm');
            $table->decimal('diem', 6, 2)->nullable()->comment('Điểm đạt được');
            $table->enum('trangthai_ai', ['pending', 'done', 'failed'])->nullable()->comment('Trạng thái AI: pending=chờ, done=xong, failed=lỗi');
            $table->decimal('diem_ai', 6, 2)->nullable()->comment('Điểm AI');
            $table->text('nhanxet_ai')->nullable()->comment('Nhận xét AI');
            $table->unsignedInteger('thoigianlam')->nullable()->comment('Thời gian làm tính bằng giây');
            $table->timestamp('ngaylam')->useCurrent()->comment('Ngày làm');

            $table->index(['nguoidung_id', 'ngaylam'], 'idx_luyentap_user');
            $table->index('cauhoi_id', 'idx_luyentap_cauhoi');
            $table->index('luachon_id', 'idx_luyentap_luachon');
        });

        // Bổ sung khóa ngoại nếu các bảng của TV3 đã tồn tại
        if (Schema::hasTable('cauhoi')) {
            Schema::table('luyentap', function (Blueprint $table) {
                $table->foreign('cauhoi_id')->references('id')->on('cauhoi')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('luachon')) {
            Schema::table('luyentap', function (Blueprint $table) {
                $table->foreign('luachon_id')->references('id')->on('luachon')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('luyentap');
    }
};