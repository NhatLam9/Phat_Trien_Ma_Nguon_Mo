<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Test User',
            'email' => 'test@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'matkhau' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/practice');
    }

    public function test_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        $admin = Nguoidung::create([
            'hoten' => 'Admin User',
            'email' => 'admin-test@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin-test@example.com',
            'matkhau' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_locked_user_cannot_login(): void
    {
        Nguoidung::create([
            'hoten' => 'Locked User',
            'email' => 'locked@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'locked',
            'vaitro' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'locked@example.com',
            'matkhau' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'hoten' => 'New User',
            'email' => 'newuser@example.com',
            'matkhau' => 'password123',
            'matkhau_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('nguoidung', [
            'email' => 'newuser@example.com',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/practice');
    }

    public function test_user_can_logout(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Test User',
            'email' => 'test@example.com',
            'matkhau' => Hash::make('password123'),
            'trangthai' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}