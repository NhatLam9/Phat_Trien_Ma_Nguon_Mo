<?php

namespace Tests\Feature;

use App\Models\Ailog;
use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAILogTest extends TestCase
{
    use RefreshDatabase;

    protected Nguoidung $admin;
    protected Nguoidung $user;
    protected Ailog $log;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Nguoidung::create([
            'hoten' => 'Admin User',
            'email' => 'admin@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);

        $this->user = Nguoidung::create([
            'hoten' => 'Normal User',
            'email' => 'user@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->log = Ailog::create([
            'nguoidung_id' => $this->user->id,
            'loai' => 'writing',
            'dauvao' => 'Sample student essay submission',
            'daura' => 'Sample grading evaluation result',
            'model' => 'gemini-1.5-flash',
            'token_vao' => 150,
            'token_ra' => 80,
            'ngaytao' => now(),
        ]);
    }

    public function test_non_admin_cannot_access_ai_logs(): void
    {
        // Khách chưa đăng nhập
        $this->get('/admin/ai-logs')->assertRedirect('/login');

        // Học viên thông thường (vaitro = 'user')
        $this->actingAs($this->user)->get('/admin/ai-logs')->assertForbidden();
    }

    public function test_admin_can_view_ai_logs_with_filter(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/ai-logs?loai=writing');

        $response->assertOk();
        $response->assertSee('Nhật ký gọi dịch vụ AI');
        $response->assertSee('WRITING');
        $response->assertSee($this->user->hoten);
    }

    public function test_admin_can_view_single_ai_log_detail(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/ai-logs/{$this->log->id}");

        $response->assertOk();
        $response->assertSee("Chi tiết nhật ký gọi AI #{$this->log->id}");
        $response->assertSee('Sample student essay submission');
        $response->assertSee('Sample grading evaluation result');
        $response->assertSee('gemini-1.5-flash');
    }
}

