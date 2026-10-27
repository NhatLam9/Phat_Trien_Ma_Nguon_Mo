<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            NguoidungSeeder::class,
            AdminNguoidungSeeder::class,
            AdminCatalogSeeder::class,
            MockTestDataSeeder::class,
            LuyentapSeeder::class,
            DanhdauSeeder::class,
        ]);
    }
}