<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;

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
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'matkhau' => 'Password123@',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/practice');
    }

    public function test_user_can_login_with_remember_me_stores_cookie(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Remember User',
            'email' => 'remember@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'remember@example.com',
            'matkhau' => 'Password123@',
            'remember' => '1',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertCookie('remember_email', 'remember@example.com');

        // Khi truy cập lại trang login kèm cookie remember_email
        $loginPage = $this->withCookie('remember_email', 'remember@example.com')->get('/login');
        $loginPage->assertOk();
        $loginPage->assertSee('value="remember@example.com"', false);
        $loginPage->assertSee('checked', false);
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
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'locked',
            'vaitro' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'locked@example.com',
            'matkhau' => 'Password123@',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'hoten' => 'New User',
            'email' => 'newuser@example.com',
            'matkhau' => 'Password123@',
            'matkhau_confirmation' => 'Password123@',
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
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * 3.1 Test phân quyền: Khách vãng lai bị chặn truy cập các route yêu cầu đăng nhập
     */
    public function test_guest_is_redirected_to_login_when_accessing_protected_routes(): void
    {
        $this->get('/practice')->assertRedirect('/login');
        $this->get('/practice/bookmarks')->assertRedirect('/login');
        $this->get('/practice/history')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    /**
     * 3.1 Test phân quyền: Học viên (User) truy cập được các chức năng luyện tập và hồ sơ
     */
    public function test_authenticated_user_can_access_practice_and_profile(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Regular User',
            'email' => 'regular@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->assertFalse($user->isAdmin());

        $this->actingAs($user)->get('/practice')->assertOk();
        $this->actingAs($user)->get('/profile')->assertOk();
        $this->actingAs($user)->get('/practice/bookmarks')->assertOk();
        $this->actingAs($user)->get('/practice/history')->assertOk();
    }

    /**
     * 3.1 Test phân quyền: Quản trị viên (Admin) nhận diện đúng vai trò
     */
    public function test_admin_user_has_admin_privileges(): void
    {
        $admin = Nguoidung::create([
            'hoten' => 'Admin Manager',
            'email' => 'admin@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'admin',
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->actingAs($admin)->get('/practice')->assertOk();
        $this->actingAs($admin)->get('/profile')->assertOk();
    }

    /**
     * Test đăng nhập Google redirect
     */
    public function test_google_redirect_returns_redirect_response(): void
    {
        $response = $this->get(route('auth.google'));
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    /**
     * Test màn hình và yêu cầu quên mật khẩu
     */
    public function test_forgot_password_screen_and_request(): void
    {
        Notification::fake();

        $user = Nguoidung::create([
            'hoten' => 'Forgot User',
            'email' => 'forgot@example.com',
            'matkhau' => Hash::make('Password123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $this->get(route('password.request'))->assertOk();

        $response = $this->post(route('password.email'), [
            'email' => 'forgot@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Test hoàn tất quy trình đặt lại mật khẩu mới với token và mật khẩu đạt độ phức tạp
     */
    public function test_user_can_reset_password_with_valid_token_and_complex_password(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Reset Password User',
            'email' => 'resetuser@example.com',
            'matkhau' => Hash::make('OldPassword123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $token = Password::broker()->createToken($user);

        // Hiển thị form reset mật khẩu
        $this->get(route('password.reset', ['token' => $token, 'email' => 'resetuser@example.com']))->assertOk();

        // Gửi mật khẩu mới hợp lệ (đủ 8 ký tự, chữ hoa, thường, số, ký tự đặc biệt)
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'resetuser@example.com',
            'matkhau' => 'NewSecurePassword123@',
            'matkhau_confirmation' => 'NewSecurePassword123@',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Kiểm tra mật khẩu mới đã được cập nhật thành công vào CSDL
        $this->assertTrue(Hash::check('NewSecurePassword123@', $user->fresh()->matkhau));
    }

    /**
     * Test đặt lại mật khẩu thất bại nếu không đáp ứng độ phức tạp yêu cầu
     */
    public function test_reset_password_fails_if_password_lacks_complexity(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Complexity User',
            'email' => 'complexity@example.com',
            'matkhau' => Hash::make('OldPassword123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        $token = Password::broker()->createToken($user);

        // Mật khẩu thiếu ký tự đặc biệt và chữ hoa
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'complexity@example.com',
            'matkhau' => 'simplepassword123',
            'matkhau_confirmation' => 'simplepassword123',
        ]);

        $response->assertSessionHasErrors('matkhau');
    }

    /**
     * Test đổi mật khẩu trong trang hồ sơ cá nhân với yêu cầu mật khẩu phức tạp
     */
    public function test_user_can_update_password_in_profile_with_complex_password(): void
    {
        $user = Nguoidung::create([
            'hoten' => 'Profile User',
            'email' => 'profile@example.com',
            'matkhau' => Hash::make('CurrentPassword123@'),
            'trangthai' => 'active',
            'vaitro' => 'user',
        ]);

        // Đổi mật khẩu thành công khi cung cấp đủ 8 ký tự, hoa, thường, số, ký tự đặc biệt
        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'CurrentPassword123@',
            'matkhau' => 'BrandNewPassword123@',
            'matkhau_confirmation' => 'BrandNewPassword123@',
        ]);

        $response->assertSessionHas('status_password');
        $this->assertTrue(Hash::check('BrandNewPassword123@', $user->fresh()->matkhau));

        // Đổi thất bại nếu mật khẩu mới không đạt chuẩn phức tạp
        $failResponse = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'BrandNewPassword123@',
            'matkhau' => 'weakpass',
            'matkhau_confirmation' => 'weakpass',
        ]);

        $failResponse->assertSessionHasErrors('matkhau');
    }
}
