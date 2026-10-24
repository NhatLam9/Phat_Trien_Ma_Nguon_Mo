<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoidung', function (Blueprint $table) {
            $table->id()->comment('Mã người dùng');
            $table->string('hoten', 100)->comment('Họ và tên');
            $table->string('email', 150)->unique()->comment('Email đăng nhập');
            $table->dateTime('email_xacthuc')->nullable()->comment('Thời gian xác thực email');
            $table->string('matkhau', 255)->nullable()->comment('Mật khẩu đã mã hóa');
            $table->string('google_id', 150)->nullable()->unique()->comment('Mã tài khoản Google');
            $table->enum('vaitro', ['user', 'admin'])->default('user')->comment('Vai trò: user=học viên, admin=quản trị');
            $table->string('anh', 255)->nullable()->comment('Ảnh đại diện');
            $table->smallInteger('muctieu')->unsigned()->nullable()->comment('Mục tiêu điểm DET (10-160)');
            $table->enum('trangthai', ['active', 'locked'])->default('active')->comment('Trạng thái: active=hoạt động, locked=bị khóa');
            $table->rememberToken()->comment('Token ghi nhớ đăng nhập');
            $table->timestamp('ngaytao')->useCurrent()->comment('Ngày tạo');
            $table->timestamp('ngaycapnhat')->useCurrent()->useCurrentOnUpdate()->comment('Ngày cập nhật');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoidung');
    }
};