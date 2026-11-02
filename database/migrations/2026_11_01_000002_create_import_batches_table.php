<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lich su import cau hoi (TV3).
 *
 * Moi lan admin import file JSON se tao mot ban ghi de biet ai import, file nao,
 * bao nhieu cau tao moi / cap nhat, va co bao nhieu canh bao.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id()->comment('Ma lan import');
            $table->string('ten_file', 255)->comment('Ten file JSON da import');
            $table->unsignedBigInteger('nguoitao')->nullable()->comment('Nguoi thuc hien import');
            $table->unsignedInteger('tong_cau')->default(0)->comment('So cau hoi trong file');
            $table->unsignedInteger('tao_moi')->default(0)->comment('So cau hoi tao moi');
            $table->unsignedInteger('cap_nhat')->default(0)->comment('So cau hoi cap nhat');
            $table->unsignedInteger('so_canhbao')->default(0)->comment('So canh bao khi kiem tra');
            $table->enum('trangthai', ['success', 'failed'])->default('success')->comment('Ket qua import');
            $table->text('ghichu')->nullable()->comment('Thong bao loi neu that bai');
            $table->dateTime('ngaytao')->useCurrent()->comment('Thoi diem import');

            $table->index(['ngaytao', 'trangthai'], 'idx_import_batches_ngaytao');

            $table->foreign('nguoitao')->references('id')->on('nguoidung')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
