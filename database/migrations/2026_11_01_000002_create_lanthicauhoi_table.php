<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lanthicauhoi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lanthi_id')->constrained('lanthi')->cascadeOnDelete();
            $table->foreignId('cauhoi_id')->constrained('cauhoi');
            $table->unsignedSmallInteger('thutu');
            $table->decimal('dokho_lucra', 4, 2)->nullable();
            $table->decimal('diemtoida', 6, 2)->default(1);
            $table->dateTime('thoigian_cap')->nullable();
            $table->dateTime('thoigian_tra_loi')->nullable();
            $table->boolean('datraloi')->default(false);

            $table->unique(['lanthi_id', 'cauhoi_id']);
            $table->unique(['lanthi_id', 'thutu']);
            $table->index('cauhoi_id', 'idx_lanthicauhoi_cauhoi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lanthicauhoi');
    }
};
