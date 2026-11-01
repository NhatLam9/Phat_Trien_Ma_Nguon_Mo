<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cauhoi_nghe', function (Blueprint $table) {
            $table->id()->comment('Mã câu hỏi con (nghe)');
            $table->unsignedBigInteger('cauhoi_id')->comment('Mã câu hỏi cha');
            $table->enum('loai', ['listen_and_complete', 'listen_and_respond', 'summarise_conversation'])->comment('Loại câu hỏi con');
            $table->text('noidung')->comment('Nội dung câu hỏi con');
            $table->text('dapandung')->nullable()->comment('Đáp án đúng');
            $table->text('giaithich')->nullable()->comment('Giải thích');
            $table->unsignedSmallInteger('thutu')->comment('Thứ tự câu hỏi con');

            $table->unique(['cauhoi_id', 'thutu']);

            $table->foreign('cauhoi_id')->references('id')->on('cauhoi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cauhoi_nghe');
    }
};
