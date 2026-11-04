<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamdiem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traloi_id')->constrained('traloi')->cascadeOnDelete();
            $table->foreignId('nguoicham')->constrained('nguoidung');
            $table->enum('loai', ['speaking', 'writing']);
            $table->decimal('nguphap', 5, 2)->nullable();
            $table->decimal('tuvung', 5, 2)->nullable();
            $table->decimal('luloat', 5, 2)->nullable();
            $table->decimal('phatam', 5, 2)->nullable();
            $table->decimal('lienquan', 5, 2)->nullable();
            $table->decimal('machlac', 5, 2)->nullable();
            $table->text('nhanxet')->nullable();
            $table->decimal('tongdiem', 6, 2)->default(0);
            $table->dateTime('ngaycham')->useCurrent();

            $table->unique(['traloi_id', 'nguoicham']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamdiem');
    }
};
