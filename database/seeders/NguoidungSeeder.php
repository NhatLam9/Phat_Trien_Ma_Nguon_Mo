<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Hash;

class NguoidungSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: Chỉ chạy seeder khi ở môi trường local hoặc testing
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        // Dùng updateOrCreate để nếu chưa có thì tạo mới, nếu có rồi thì cập nhật lại
        Nguoidung::updateOrCreate(
            ['email' => 'lam@example.com'], // Điều kiện tìm kiếm
            [
                'hoten' => 'Lâm',
                'matkhau' => Hash::make('password'),
                'trangthai' => 'active',
                'vaitro' => 'user',
            ]
        );
    }
}