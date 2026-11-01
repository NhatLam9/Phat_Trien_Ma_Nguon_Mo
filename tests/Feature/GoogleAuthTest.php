<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use App\Models\Nguoidung;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_returns_redirect_response(): void
    {
        $response = $this->get(route('auth.google'));

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_creates_and_authenticates_user(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-12345');
        $socialiteUser->shouldReceive('getName')->andReturn('Google User');
        $socialiteUser->shouldReceive('getEmail')->andReturn('googleuser@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('nguoidung', [
            'email' => 'googleuser@example.com',
            'google_id' => 'google-id-12345',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/practice');
    }

    public function test_google_callback_links_existing_user(): void
    {
        $existing = Nguoidung::create([
            'hoten' => 'Existing User',
            'email' => 'existing@example.com',
            'matkhau' => bcrypt('password123'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-existing');
        $socialiteUser->shouldReceive('getName')->andReturn('Existing User');
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $this->assertDatabaseHas('nguoidung', [
            'email' => 'existing@example.com',
            'google_id' => 'google-id-existing',
        ]);

        $this->assertAuthenticatedAs($existing->fresh());
        $response->assertRedirect('/practice');
    }

    public function test_google_callback_rejects_locked_user(): void
    {
        Nguoidung::create([
            'hoten' => 'Locked User',
            'email' => 'locked@example.com',
            'google_id' => 'google-locked',
            'matkhau' => bcrypt('password123'),
            'trangthai' => 'locked',
            'vaitro' => 'user',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-locked');
        $socialiteUser->shouldReceive('getName')->andReturn('Locked User');
        $socialiteUser->shouldReceive('getEmail')->andReturn('locked@example.com');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }
}
