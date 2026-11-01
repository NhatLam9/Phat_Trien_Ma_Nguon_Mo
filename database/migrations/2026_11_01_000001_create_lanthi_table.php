<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lanthi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoidung_id')->constrained('nguoidung');
            $table->foreignId('dethi_id')->constrained('dethi');
            $table->decimal('dokho_hientai', 4, 2)->default(5.00);
            $table->dateTime('batdau')->useCurrent();
            $table->dateTime('ketthuc')->nullable();
            $table->enum('trangthai', ['in_progress', 'completed', 'abandoned'])
                ->default('in_progress');
            $table->unsignedSmallInteger('literacy')->nullable();
            $table->unsignedSmallInteger('comprehension')->nullable();
            $table->unsignedSmallInteger('conversation')->nullable();
            $table->unsignedSmallInteger('production')->nullable();
            $table->unsignedSmallInteger('tongdiem')->nullable();
            $table->dateTime('ngaytao')->useCurrent();

            $table->index(['nguoidung_id', 'trangthai'], 'idx_lanthi_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lanthi');
    }
};
