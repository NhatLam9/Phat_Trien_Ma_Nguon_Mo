<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;
use App\Models\Nguoidung;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_practice_page_returns_200_when_question_type_exists(): void
    {
        // Tạo tạm bảng loaicauhoi nếu chưa có trong môi trường test
        if (!Schema::hasTable('loaicauhoi')) {
            Schema::create('loaicauhoi', function (Blueprint $table) {
                $table->id();
                $table->string('ma')->unique();
                $table->string('ten');
                $table->string('kynang')->nullable();
                $table->string('cachcham')->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamps();
            });
        }

        $user = Nguoidung::create([
            'hoten' => 'Practice User',
            'email' => 'practice@example.com',
            'matkhau' => bcrypt('password123'),
        ]);

        DB::table('loaicauhoi')->insert([
            'ma' => 'read_aloud',
            'ten' => 'Read Aloud',
            'kynang' => 'speaking',
            'cachcham' => 'ai',
            'is_active' => 1,
        ]);

        $this->actingAs($user)
            ->get('/practice')
            ->assertOk()
            ->assertSee('Read Aloud');
    }
}