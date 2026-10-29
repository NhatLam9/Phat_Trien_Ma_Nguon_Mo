<?php

namespace Tests\Feature;

use Database\Seeders\FullMockTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class FullMockTestSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_de_mock_test_hoan_chinh(): void
    {
        $this->seed(FullMockTestSeeder::class);

        $exam = DB::table('dethi')
            ->where('ten', 'DET Mock Test - Full 01')
            ->first();

        $this->assertNotNull($exam);
        $this->assertSame('full', $exam->chedo);
        $this->assertSame('fixed', $exam->kieu);
        $this->assertSame('published', $exam->trangthai);
        $this->assertSame('20.00', (string) $exam->tongdiem);
        $this->assertSame(3600, (int) $exam->thoigian);

        $this->assertSame(
            20,
            DB::table('dethicauhoi')->where('dethi_id', $exam->id)->count()
        );

        $this->assertSame(
            3,
            DB::table('cauhoi_doc')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'cauhoi_doc.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );

        $this->assertSame(
            6,
            DB::table('cauhoi_nghe')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'cauhoi_nghe.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );

        $this->assertSame(
            48,
            DB::table('luachon')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'luachon.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );

        $this->assertSame(
            8,
            DB::table('luachon_doc')
                ->join('cauhoi_doc', 'cauhoi_doc.id', '=', 'luachon_doc.cauhoi_doc_id')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'cauhoi_doc.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );

        $this->assertSame(
            16,
            DB::table('luachon_nghe')
                ->join('cauhoi_nghe', 'cauhoi_nghe.id', '=', 'luachon_nghe.cauhoi_nghe_id')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'cauhoi_nghe.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );

        foreach ([
            'images/questions/photo-2.svg',
            'images/questions/photo-3.svg',
            'audio/questions/listening-2.wav',
            'audio/questions/listening-3.wav',
        ] as $path) {
            $this->assertTrue(File::exists(public_path($path)), $path);
        }
    }

    public function test_seeder_chay_lai_khong_tao_du_lieu_trung(): void
    {
        $this->seed(FullMockTestSeeder::class);
        $this->seed(FullMockTestSeeder::class);

        $exam = DB::table('dethi')
            ->where('ten', 'DET Mock Test - Full 01')
            ->first();

        $this->assertNotNull($exam);
        $this->assertSame(
            20,
            DB::table('dethicauhoi')->where('dethi_id', $exam->id)->count()
        );
        $this->assertSame(
            3,
            DB::table('cauhoi_doc')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'cauhoi_doc.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );
        $this->assertSame(
            6,
            DB::table('cauhoi_nghe')
                ->join('dethicauhoi', 'dethicauhoi.cauhoi_id', '=', 'cauhoi_nghe.cauhoi_id')
                ->where('dethicauhoi.dethi_id', $exam->id)
                ->count()
        );
    }
}
