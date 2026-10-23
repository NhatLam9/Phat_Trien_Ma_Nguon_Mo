<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Danhdau;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\DB;

class DanhdauSeeder extends Seeder
{
    public function run(): void
    {
        $user = Nguoidung::where('email', 'lam@example.com')->first();
        
        // Lấy ID câu hỏi đầu tiên có sẵn trong bảng cauhoi (nếu có)
        $firstCauhoiId = DB::table('cauhoi')->value('id');

        if ($user && $firstCauhoiId) {
            Danhdau::updateOrCreate(
                [
                    'nguoidung_id' => $user->id,
                    'cauhoi_id' => $firstCauhoiId,
                ],
                [
                    'ngaytao' => now(),
                ]
            );
        }
    }
}