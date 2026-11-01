<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dethicauhoi', function (Blueprint $table) {
            $table->id()->comment('Mã chi tiết đề');
            $table->unsignedBigInteger('dethi_id')->comment('Mã đề thi');
            $table->unsignedBigInteger('cauhoi_id')->comment('Mã câu hỏi');
            $table->unsignedSmallInteger('thutu')->comment('Thứ tự câu hỏi');
            $table->decimal('diem', 6, 2)->default(1)->comment('Điểm câu hỏi');

            $table->unique(['dethi_id', 'cauhoi_id']);
            $table->unique(['dethi_id', 'thutu']);

            $table->foreign('dethi_id')->references('id')->on('dethi')->cascadeOnDelete();
            $table->foreign('cauhoi_id')->references('id')->on('cauhoi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dethicauhoi');
    }
};
