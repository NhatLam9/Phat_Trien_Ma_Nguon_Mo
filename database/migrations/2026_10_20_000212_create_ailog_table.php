<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ailog', function (Blueprint $table) {
            $table->id()->comment('Mã nhật ký AI');
            $table->unsignedBigInteger('nguoidung_id')->comment('Người sử dụng AI');
            $table->enum('loai', ['speaking', 'writing', 'grammar', 'translate', 'rewrite', 'chat'])->comment('Loại yêu cầu');
            $table->mediumText('dauvao')->nullable()->comment('Dữ liệu đầu vào');
            $table->mediumText('daura')->nullable()->comment('Kết quả AI');
            $table->string('model', 100)->nullable()->comment('Model AI');
            $table->unsignedInteger('token_vao')->nullable()->comment('Token đầu vào');
            $table->unsignedInteger('token_ra')->nullable()->comment('Token đầu ra');
            $table->dateTime('ngaytao')->useCurrent()->comment('Ngày sử dụng');

            $table->index(['nguoidung_id', 'ngaytao'], 'idx_ailog_user');

            $table->foreign('nguoidung_id')->references('id')->on('nguoidung')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ailog');
    }
};
