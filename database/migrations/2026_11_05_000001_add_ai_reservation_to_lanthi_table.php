<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lanthi', function (Blueprint $table) {
            $table->date('ai_ngay_tru_luot')->nullable();
            $table->unsignedInteger('ai_luot_du_phong')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('lanthi', function (Blueprint $table) {
            $table->dropColumn(['ai_ngay_tru_luot', 'ai_luot_du_phong']);
        });
    }
};
