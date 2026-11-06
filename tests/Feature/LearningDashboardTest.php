<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\CreatesMockExam;
use Tests\TestCase;

class LearningDashboardTest extends TestCase
{
    use CreatesMockExam, RefreshDatabase;

    public function test_guest_must_sign_in(): void
    {
        $this->get(route('learning.dashboard'))->assertRedirect(route('login'));
    }

    public function test_empty_dashboard_has_no_division_by_zero(): void
    {
        $user = $this->createStudent();
        $this->actingAs($user)->get(route('learning.dashboard'))
            ->assertOk()->assertViewHas('tongCauLuyen', 0)
            ->assertViewHas('tyLeDung', null)
            ->assertViewHas('chuoiHienTai', 0)
            ->assertViewHas('chuoiDaiNhat', 0);
    }

    public function test_dashboard_filters_by_owner_and_counts_distinct_active_days(): void
    {
        $user = $this->createStudent();
        $other = $this->createStudent('other@example.com');
        [$exam, $questions] = $this->createMockExam(withAi: false);
        foreach ([today()->subDay(), today(), today()] as $day) {
            DB::table('luyentap')->insert([
                'nguoidung_id' => $user->id, 'cauhoi_id' => $questions[0]['id'],
                'ladung' => false, 'thoigianlam' => 30, 'ngaylam' => $day,
            ]);
        }
        DB::table('luyentap')->insert([
            'nguoidung_id' => $other->id, 'cauhoi_id' => $questions[0]['id'],
            'ladung' => true, 'thoigianlam' => 999, 'ngaylam' => today(),
        ]);
        $this->actingAs($user)->get(route('learning.dashboard'))
            ->assertOk()->assertViewHas('tongCauLuyen', 3)
            ->assertViewHas('tongThoiGian', 90)
            ->assertViewHas('tyLeDung', 0.0)
            ->assertViewHas('chuoiHienTai', 2)
            ->assertViewHas('chuoiDaiNhat', 2)
            ->assertViewHas('goiYLuyenTap', fn ($items) => $items->count() === 1);
    }
}
