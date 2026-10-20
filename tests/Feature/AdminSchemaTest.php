<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Kiểm tra schema các bảng do TV3 (Phi Phong) sở hữu.
 */
class AdminSchemaTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array<int, string>
     */
    public static function tv3Tables(): array
    {
        return [
            'danhmuc',
            'loaicauhoi',
            'cauhoi',
            'luachon',
            'dap_an',
            'cauhoi_doc',
            'cauhoi_nghe',
            'luachon_doc',
            'luachon_nghe',
            'dethi',
            'dethicauhoi',
            'ailog',
            'aisudung',
        ];
    }

    public function test_tat_ca_bang_cua_tv3_duoc_tao(): void
    {
        foreach (self::tv3Tables() as $table) {
            $this->assertTrue(Schema::hasTable($table), "Thiếu bảng: {$table}");
        }
    }

    public function test_cauhoi_co_du_cot_va_khoa_ngoai(): void
    {
        $columns = Schema::getColumnListing('cauhoi');

        foreach (['loai_id', 'danhmuc_id', 'noidung', 'dokho', 'diem', 'trangthai', 'nguoitao', 'ngaytao', 'ngaycapnhat'] as $column) {
            $this->assertContains($column, $columns, "Bảng cauhoi thiếu cột {$column}");
        }

        $foreignTables = collect(Schema::getForeignKeys('cauhoi'))
            ->pluck('foreign_table')
            ->all();

        $this->assertContains('loaicauhoi', $foreignTables);
        $this->assertContains('danhmuc', $foreignTables);
        $this->assertContains('nguoidung', $foreignTables);
    }

    public function test_check_constraint_dokho_tu_1_den_10(): void
    {
        $loaiId = DB::table('loaicauhoi')->insertGetId([
            'ma' => 'check_dokho',
            'ten' => 'Kiểm tra độ khó',
            'kynang' => 'reading',
            'cachcham' => 'auto',
        ]);

        $this->expectException(QueryException::class);

        DB::table('cauhoi')->insert([
            'loai_id' => $loaiId,
            'noidung' => 'Câu hỏi có độ khó không hợp lệ',
            'dokho' => 0,
        ]);
    }
}
