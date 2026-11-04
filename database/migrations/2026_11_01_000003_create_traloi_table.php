<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traloi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lanthicauhoi_id')
                ->unique()
                ->constrained('lanthicauhoi')
                ->cascadeOnDelete();
            $table->longText('noidung')->nullable();
            $table->foreignId('luachon_id')->nullable()
                ->constrained('luachon')
                ->nullOnDelete();
            $table->string('audio', 255)->nullable();
            $table->boolean('ladung')->nullable();
            $table->decimal('diem', 6, 2)->nullable();
            $table->enum('trangthai_ai', ['pending', 'done', 'failed'])->nullable();
            $table->decimal('diem_ai', 6, 2)->nullable();
            $table->text('nhanxet_ai')->nullable();
            $table->dateTime('thoigian')->useCurrent();

            $table->index('luachon_id', 'idx_traloi_luachon');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traloi');
    }
};
