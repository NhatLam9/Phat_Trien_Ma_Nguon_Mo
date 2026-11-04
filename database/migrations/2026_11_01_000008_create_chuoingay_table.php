<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuoingay', function (Blueprint $table) {
            $table->foreignId('nguoidung_id')->primary()
                ->constrained('nguoidung')
                ->cascadeOnDelete();
            $table->unsignedInteger('hientai')->default(0);
            $table->unsignedInteger('dainhat')->default(0);
            $table->date('ngaygannhat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuoingay');
    }
};
