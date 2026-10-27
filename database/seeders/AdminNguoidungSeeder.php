<?php

namespace Database\Seeders;

use App\Models\Nguoidung;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminNguoidungSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            return;
        }

        Nguoidung::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'hoten' => 'Quản trị viên',
                'matkhau' => Hash::make('password'),
                'vaitro' => 'admin',
                'trangthai' => 'active',
            ]
        );
    }
}
