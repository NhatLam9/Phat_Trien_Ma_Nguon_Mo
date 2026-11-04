<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lanthi')) {
            Schema::create('lanthi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('nguoidung_id');
                $table->unsignedBigInteger('dethi_id')->nullable();
                $table->decimal('dokho_hientai', 3, 2)->default(0.50);
                $table->dateTime('batdau')->useCurrent();
                $table->dateTime('ketthuc')->nullable();
                $table->string('trangthai', 30)->default('in_progress');
                $table->decimal('literacy', 6, 2)->nullable();
                $table->decimal('comprehension', 6, 2)->nullable();
                $table->decimal('conversation', 6, 2)->nullable();
                $table->decimal('production', 6, 2)->nullable();
                $table->unsignedInteger('tongdiem')->nullable();
                $table->dateTime('ngaytao')->useCurrent();

                $table->foreign('nguoidung_id')->references('id')->on('nguoidung')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('lanthicauhoi')) {
            Schema::create('lanthicauhoi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lanthi_id');
                $table->unsignedBigInteger('cauhoi_id');
                $table->unsignedInteger('thutu');
                $table->decimal('dokho_lucra', 3, 2)->nullable();
                $table->decimal('diemtoida', 6, 2)->default(160.00);
                $table->dateTime('thoigian_cap')->useCurrent();
                $table->dateTime('thoigian_tra_loi')->nullable();
                $table->boolean('datraloi')->default(false);

                $table->foreign('lanthi_id')->references('id')->on('lanthi')->cascadeOnDelete();
                $table->foreign('cauhoi_id')->references('id')->on('cauhoi')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('traloi')) {
            Schema::create('traloi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lanthicauhoi_id')->unique();
                $table->longText('noidung')->nullable();
                $table->unsignedBigInteger('luachon_id')->nullable();
                $table->string('audio', 255)->nullable();
                $table->boolean('ladung')->nullable();
                $table->decimal('diem', 6, 2)->nullable();
                $table->string('trangthai_ai', 20)->nullable();
                $table->decimal('diem_ai', 6, 2)->nullable();
                $table->text('nhanxet_ai')->nullable();
                $table->dateTime('thoigian')->useCurrent();

                $table->foreign('lanthicauhoi_id')->references('id')->on('lanthicauhoi')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('traloi');
        Schema::dropIfExists('lanthicauhoi');
        Schema::dropIfExists('lanthi');
    }
};

