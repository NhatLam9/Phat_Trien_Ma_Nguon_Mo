<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiendo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoidung_id')->constrained('nguoidung')->cascadeOnDelete();
            $table->foreignId('loai_id')->constrained('loaicauhoi')->cascadeOnDelete();
            $table->unsignedInteger('tongso')->default(0);
            $table->unsignedInteger('sodung')->default(0);
            $table->decimal('diemtrungbinh', 6, 2)->nullable();
            $table->timestamp('ngaycapnhat')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['nguoidung_id', 'loai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiendo');
    }
};
