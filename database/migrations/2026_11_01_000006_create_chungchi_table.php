<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chungchi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoidung_id')->constrained('nguoidung')->cascadeOnDelete();
            $table->foreignId('lanthi_id')->unique()->constrained('lanthi')->cascadeOnDelete();
            $table->string('ma', 100)->unique();
            $table->unsignedSmallInteger('tongdiem');
            $table->unsignedSmallInteger('literacy')->nullable();
            $table->unsignedSmallInteger('comprehension')->nullable();
            $table->unsignedSmallInteger('conversation')->nullable();
            $table->unsignedSmallInteger('production')->nullable();
            $table->string('trinhdo', 50)->nullable();
            $table->dateTime('ngaycap')->nullable();
            $table->dateTime('ngaytao')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chungchi');
    }
};
