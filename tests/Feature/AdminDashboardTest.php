<?php

namespace Tests\Feature;

use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_loads_when_lanthi_table_is_missing(): void
    {
        $admin = Nguoidung::create([
            'hoten' => 'Admin Dashboard',
            'email' => 'admin-dashboard@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }
}
