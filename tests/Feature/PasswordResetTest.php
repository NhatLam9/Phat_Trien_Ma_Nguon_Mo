<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_request_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Khôi phục mật khẩu');
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = Nguoidung::create([
            'hoten' => 'Reset User',
            'email' => 'reset@example.com',
            'matkhau' => Hash::make('oldpassword'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'reset@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'sample-token']));
        $response->assertStatus(200);
        $response->assertSee('Đặt lại mật khẩu');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Reset User',
            'email' => 'reset@example.com',
            'matkhau' => Hash::make('oldpassword'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'reset@example.com',
            'matkhau' => 'newpassword123',
            'matkhau_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->matkhau));
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Reset User',
            'email' => 'reset@example.com',
            'matkhau' => Hash::make('oldpassword'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'reset@example.com',
            'matkhau' => 'newpassword123',
            'matkhau_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('email');
        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword', $user->matkhau));
    }
}

