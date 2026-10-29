<?php

namespace Tests\Feature;

use Database\Seeders\OperationalQuestionBankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalQuestionBankSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_tao_du_ngan_hang_va_5_de(): void
    {
        $this->seed(OperationalQuestionBankSeeder::class);

        $this->assertDatabaseCount('cauhoi', 120);
        $this->assertDatabaseCount('cauhoi_doc', 30);
        $this->assertDatabaseCount('cauhoi_nghe', 30);
        $this->assertDatabaseCount('luachon', 280);
        $this->assertDatabaseCount('luachon_doc', 80);
        $this->assertDatabaseCount('luachon_nghe', 80);
        $this->assertDatabaseCount('dethi', 5);
        $this->assertDatabaseCount('dethicauhoi', 100);

        $this->assertDatabaseHas('dethi', [
            'ten' => 'DET Mock Test - Operational 01',
            'chedo' => 'full',
            'kieu' => 'fixed',
            'thoigian' => 3600,
            'trangthai' => 'published',
        ]);
    }

    public function test_seeder_chay_lai_khong_tao_du_lieu_trung(): void
    {
        $this->seed(OperationalQuestionBankSeeder::class);
        $this->seed(OperationalQuestionBankSeeder::class);

        $this->assertDatabaseCount('cauhoi', 120);
        $this->assertDatabaseCount('cauhoi_doc', 30);
        $this->assertDatabaseCount('cauhoi_nghe', 30);
        $this->assertDatabaseCount('dethi', 5);
        $this->assertDatabaseCount('dethicauhoi', 100);
    }

    public function test_media_cua_bo_de_co_ton_tai(): void
    {
        $this->seed(OperationalQuestionBankSeeder::class);

        for ($i = 4; $i <= 13; $i++) {
            $this->assertFileExists(public_path("images/questions/photo-{$i}.svg"));
        }

        for ($i = 1; $i <= 10; $i++) {
            $number = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $this->assertFileExists(public_path("audio/questions/listening-bank-{$number}.wav"));
        }
    }
}
