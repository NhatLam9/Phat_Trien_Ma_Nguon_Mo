<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class BanTest extends TestCase
{
    use RefreshDatabase;

    public function test_locked_user_is_prevented_from_logging_in(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Banned User',
            'email' => 'banned@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'locked',
            'vaitro' => 'user',
        ]);

        $this->assertTrue($user->isLocked());

        $response = $this->post('/login', [
            'email' => 'banned@example.com',
            'matkhau' => 'Password123@',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_locked_user_cannot_login_via_google_oauth(): void
    {
        Nguoidung::create([
            'hoten' => 'Banned Google User',
            'email' => 'bannedgoogle@example.com',
            'google_id' => 'google-banned-id',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'locked',
            'vaitro' => 'user',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-banned-id');
        $socialiteUser->shouldReceive('getName')->andReturn('Banned Google User');
        $socialiteUser->shouldReceive('getEmail')->andReturn('bannedgoogle@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_active_user_becomes_locked_cannot_login(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Normal User',
            'email' => 'normal@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->assertFalse($user->isLocked());

        // Quản trị viên khóa tài khoản
        $user->update(['trangthai' => 'locked']);
        $this->assertTrue($user->fresh()->isLocked());

        // Lần đăng nhập kế tiếp bị từ chối
        $response = $this->post('/login', [
            'email' => 'normal@example.com',
            'matkhau' => 'Password123@',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}
