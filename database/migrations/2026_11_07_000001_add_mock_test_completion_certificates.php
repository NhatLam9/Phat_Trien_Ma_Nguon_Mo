<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dethi', function (Blueprint $table) {
            $table->unsignedTinyInteger('capdo')->nullable()->unique();
        });

        Schema::table('chungchi', function (Blueprint $table) {
            $table->string('tieude', 220)->nullable();
            $table->unsignedSmallInteger('tongdiem')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('dethi', function (Blueprint $table) {
            $table->dropUnique(['capdo']);
            $table->dropColumn('capdo');
        });

        // Chứng nhận hoàn thành không có điểm nên không dùng được schema cũ.
        \Illuminate\Support\Facades\DB::table('chungchi')->whereNotNull('tieude')->delete();

        Schema::table('chungchi', function (Blueprint $table) {
            $table->dropColumn('tieude');
            $table->unsignedSmallInteger('tongdiem')->nullable(false)->change();
        });
    }
};
