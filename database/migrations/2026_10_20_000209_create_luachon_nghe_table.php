<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luachon_nghe', function (Blueprint $table) {
            $table->id()->comment('Mã lựa chọn câu con (nghe)');
            $table->unsignedBigInteger('cauhoi_nghe_id')->comment('Mã câu hỏi con (cauhoi_nghe)');
            $table->text('noidung')->comment('Nội dung lựa chọn');
            $table->boolean('ladung')->default(false)->comment('1 đúng, 0 sai');
            $table->unsignedSmallInteger('thutu')->nullable()->comment('Thứ tự');

            $table->index('cauhoi_nghe_id', 'idx_luachonnghe_cauhoinghe');

            $table->foreign('cauhoi_nghe_id')->references('id')->on('cauhoi_nghe')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luachon_nghe');
    }
};
