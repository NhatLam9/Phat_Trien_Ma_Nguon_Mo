<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dap_an', function (Blueprint $table) {
            $table->id()->comment('Mã đáp án');
            $table->unsignedBigInteger('cauhoi_id')->comment('Mã câu hỏi');
            $table->text('noidung')->comment('Nội dung đáp án');
            $table->boolean('ladung')->default(false)->comment('1 đúng, 0 sai');
            $table->unsignedSmallInteger('thutu')->nullable()->comment('Thứ tự đáp án');

            $table->index('cauhoi_id', 'idx_dapan_cauhoi');

            $table->foreign('cauhoi_id')->references('id')->on('cauhoi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dap_an');
    }
};
