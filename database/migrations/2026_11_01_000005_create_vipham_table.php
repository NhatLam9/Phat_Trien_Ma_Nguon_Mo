<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vipham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lanthi_id')->constrained('lanthi')->cascadeOnDelete();
            $table->enum('loai', [
                'out_tab',
                'window_change',
                'disconnect',
                'early_submit',
                'timeout',
                'other',
            ]);
            $table->enum('nguon', ['auto', 'self_report', 'admin'])->default('auto');
            $table->text('mota')->nullable();
            $table->dateTime('thoigian')->useCurrent();

            $table->index(['lanthi_id', 'thoigian'], 'idx_vipham_lanthi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vipham');
    }
};
