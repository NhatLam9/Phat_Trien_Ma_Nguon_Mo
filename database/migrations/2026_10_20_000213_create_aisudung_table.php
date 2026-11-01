<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aisudung', function (Blueprint $table) {
            $table->id()->comment('Mã sử dụng AI');
            $table->unsignedBigInteger('nguoidung_id')->comment('Người dùng');
            $table->date('ngay')->comment('Ngày sử dụng');
            $table->unsignedInteger('solan')->default(0)->comment('Số lần gọi AI');

            $table->unique(['nguoidung_id', 'ngay']);

            $table->foreign('nguoidung_id')->references('id')->on('nguoidung')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aisudung');
    }
};
