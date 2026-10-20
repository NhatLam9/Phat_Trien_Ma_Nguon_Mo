<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danhmuc', function (Blueprint $table) {
            $table->id()->comment('Mã danh mục');
            $table->string('ten', 100)->comment('Tên chủ đề');
            $table->text('mota')->nullable()->comment('Mô tả');
            $table->dateTime('ngaytao')->useCurrent()->comment('Ngày tạo');
            $table->dateTime('ngaycapnhat')->useCurrent()->useCurrentOnUpdate()->comment('Ngày cập nhật');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danhmuc');
    }
};
