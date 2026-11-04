<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Them khoa on dinh "ma" cho cau hoi (TV3).
 *
 * Truoc day JSON import dung cap (loai_id, noidung) lam khoa, nen khi giao vien
 * sua noi dung thi he thong tao ra cau hoi moi thay vi cap nhat. Cot "ma" cho
 * phep import lai dung cau hoi cu va giu nguyen lich su (dethicauhoi, lanthicauhoi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cauhoi', function (Blueprint $table) {
            $table->string('ma', 100)
                ->nullable()
                ->after('id')
                ->comment('Ma cau hoi on dinh, dung khi import lai');
            $table->unique('ma', 'uniq_cauhoi_ma');
        });

        // Sinh ma cho du lieu cu: q-000001, q-000002, ...
        DB::table('cauhoi')
            ->whereNull('ma')
            ->orderBy('id')
            ->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('cauhoi')
                        ->where('id', $row->id)
                        ->update(['ma' => 'q-'.str_pad((string) $row->id, 6, '0', STR_PAD_LEFT)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('cauhoi', function (Blueprint $table) {
            $table->dropUnique('uniq_cauhoi_ma');
            $table->dropColumn('ma');
        });
    }
};
