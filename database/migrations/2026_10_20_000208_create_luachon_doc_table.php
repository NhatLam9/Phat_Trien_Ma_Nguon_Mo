<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luachon_doc', function (Blueprint $table) {
            $table->id()->comment('Mã lựa chọn câu con (đọc)');
            $table->unsignedBigInteger('cauhoi_doc_id')->comment('Mã câu hỏi con (cauhoi_doc)');
            $table->text('noidung')->comment('Nội dung lựa chọn');
            $table->boolean('ladung')->default(false)->comment('1 đúng, 0 sai');
            $table->unsignedSmallInteger('thutu')->nullable()->comment('Thứ tự');

            $table->index('cauhoi_doc_id', 'idx_luachondoc_cauhoidoc');

            $table->foreign('cauhoi_doc_id')->references('id')->on('cauhoi_doc')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luachon_doc');
    }
};
