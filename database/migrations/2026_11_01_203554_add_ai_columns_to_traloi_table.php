<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('traloi', function (Blueprint $table) {
            $table->unsignedInteger('luot_cham')->default(1)->after('nhanxet_ai')->comment('Số lần chấm; Job cũ có số lần cũ sẽ bị loại khi ghi kết quả');
            $table->date('ngay_tru_luot')->nullable()->after('luot_cham')->comment('Ngày đã trừ lượt, để hoàn đúng ngày');
            $table->dateTime('thoidiem_cham')->nullable()->after('ngay_tru_luot')->comment('Mốc bắt đầu chấm, làm đồng hồ pending 5 phút');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traloi', function (Blueprint $table) {
            $table->dropColumn(['luot_cham', 'ngay_tru_luot', 'thoidiem_cham']);
        });
    }
};
