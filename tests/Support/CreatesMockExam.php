<?php

namespace Tests\Support;

use App\Models\Nguoidung;
use Illuminate\Support\Facades\DB;

trait CreatesMockExam
{
    protected function createStudent(string $email = 'mock-student@example.com'): Nguoidung
    {
        return Nguoidung::create([
            'hoten' => 'Mock Student', 'email' => $email,
            'matkhau' => bcrypt('Password123!'), 'vaitro' => 'user',
            'trangthai' => 'active', 'email_xacthuc' => now(),
        ]);
    }

    protected function createMockExam(string $kind = 'fixed', bool $withAi = true): array
    {
        $examId = DB::table('dethi')->insertGetId([
            'ten' => 'Integration exam', 'chedo' => 'full', 'kieu' => $kind,
            'thoigian' => 3600, 'trangthai' => 'published', 'tongdiem' => 4,
        ]);
        $questions = [];
        foreach (['reading', 'listening', 'writing', 'speaking'] as $index => $skill) {
            $ai = $withAi && in_array($skill, ['writing', 'speaking'], true);
            $typeId = DB::table('loaicauhoi')->insertGetId([
                'ma' => $skill.'_'.$examId, 'ten' => ucfirst($skill),
                'kynang' => $skill, 'cachcham' => $ai ? 'ai' : 'auto',
            ]);
            $id = DB::table('cauhoi')->insertGetId([
                'loai_id' => $typeId, 'noidung' => 'Describe this topic clearly.',
                'ma' => 'q-'.$typeId, 'trangthai' => 'published', 'dokho' => 5 + $index,
            ]);
            DB::table('dethicauhoi')->insert([
                'dethi_id' => $examId, 'cauhoi_id' => $id, 'thutu' => $index + 1, 'diem' => 1,
            ]);
            $option = $ai ? null : DB::table('luachon')->insertGetId([
                'cauhoi_id' => $id, 'noidung' => 'Correct', 'ladung' => true, 'thutu' => 1,
            ]);
            $questions[] = ['id' => $id, 'skill' => $skill, 'ai' => $ai, 'option' => $option];
        }

        return [$examId, $questions];
    }
}
