<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Tạo tài khoản quản trị trước để admin luôn là người dùng đầu tiên (id nhỏ nhất).
            AdminNguoidungSeeder::class,
            NguoidungSeeder::class,
            AdminCatalogSeeder::class,
            MockTestDataSeeder::class,
            FullMockTestSeeder::class,
            OperationalQuestionBankSeeder::class,
            ExtendedQuestionBankSeeder::class,
            MockTestLevelSeeder::class,
            LuyentapSeeder::class,
            DanhdauSeeder::class,
        ]);
    }
}
