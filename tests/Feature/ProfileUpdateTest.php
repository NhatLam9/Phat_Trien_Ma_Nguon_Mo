<?php

namespace Tests\Feature;

use App\Models\Nguoidung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(array $attributes = []): Nguoidung
    {
        return Nguoidung::create(array_merge([
            'hoten' => 'Nguyễn Văn A',
            'email' => 'user_' . uniqid() . '@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
            'muctieu' => 100,
        ], $attributes));
    }

    public function test_user_can_view_profile(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee($user->hoten);
        $response->assertSee($user->email);
    }

    public function test_user_can_update_profile_info(): void
    {
        $user = $this->createUser([
            'hoten' => 'Tên Cũ',
            'muctieu' => 100,
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'hoten' => 'Tên Mới Cập Nhật',
            'muctieu' => 125,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('nguoidung', [
            'id' => $user->id,
            'hoten' => 'Tên Mới Cập Nhật',
            'muctieu' => 125,
        ]);
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');

        $user = $this->createUser();
        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        // 1. Upload avatar
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'hoten' => $user->hoten,
            'anh' => $file,
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->anh);
        Storage::disk('public')->assertExists($user->anh);

        // 2. Remove avatar
        $response = $this->actingAs($user)->put(route('profile.update'), [
            'hoten' => $user->hoten,
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertNull($user->anh);
    }

    public function test_user_can_change_password_inside_profile_update(): void
    {
        $user = $this->createUser([
            'matkhau' => Hash::make('OldPassword@123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'hoten' => $user->hoten,
            'current_password' => 'OldPassword@123',
            'matkhau' => 'NewPassword@2026',
            'matkhau_confirmation' => 'NewPassword@2026',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword@2026', $user->matkhau));
    }
}
