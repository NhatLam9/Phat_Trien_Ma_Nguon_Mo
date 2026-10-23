<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Luyentap;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\DB;

class LuyentapSeeder extends Seeder
{
    public function run(): void
    {
        $user = Nguoidung::where('email', 'lam@example.com')->first();
        
        // Lấy ID câu hỏi đầu tiên có sẵn trong bảng cauhoi (nếu có)
        $firstCauhoiId = DB::table('cauhoi')->value('id');

        if ($user && $firstCauhoiId) {
            // Dùng updateOrCreate để tránh lỗi trùng lặp khi chạy seed nhiều lần
            Luyentap::updateOrCreate(
                [
                    'nguoidung_id' => $user->id,
                    'cauhoi_id' => $firstCauhoiId,
                ],
                [
                    'noidung' => 'This is a sample answer for testing practice module.',
                    'ladung' => 1,
                    'trangthai_ai' => 'done',
                    'diem_ai' => 9.5,
                    'thoigianlam' => 45,
                ]
            );
        }
    }
}