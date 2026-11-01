<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Nguoidung;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Tạo tài khoản DET Practice');
    }

    public function test_user_can_register_with_valid_information(): void
    {
        $response = $this->post('/register', [
            'hoten' => 'Nguyen Van A',
            'email' => 'nguyenvana@example.com',
            'matkhau' => 'Password123@',
            'matkhau_confirmation' => 'Password123@',
        ]);

        $this->assertDatabaseHas('nguoidung', [
            'hoten' => 'Nguyen Van A',
            'email' => 'nguyenvana@example.com',
            'vaitro' => 'user',
            'trangthai' => 'active',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('practice.index'));
    }

    public function test_registration_fails_when_name_is_missing(): void
    {
        $response = $this->post('/register', [
            'hoten' => '',
            'email' => 'valid@example.com',
            'matkhau' => 'Password123@',
            'matkhau_confirmation' => 'Password123@',
        ]);

        $response->assertSessionHasErrors('hoten');
        $this->assertGuest();
    }

    public function test_registration_fails_with_invalid_or_duplicate_email(): void
    {
        Nguoidung::create([
            'hoten' => 'Existing User',
            'email' => 'existing@example.com',
            'matkhau' => bcrypt('Password123@'),
        ]);

        // Trùng email
        $response = $this->post('/register', [
            'hoten' => 'Another User',
            'email' => 'existing@example.com',
            'matkhau' => 'Password123@',
            'matkhau_confirmation' => 'Password123@',
        ]);

        $response->assertSessionHasErrors('email');

        // Định dạng email sai
        $invalidEmailResponse = $this->post('/register', [
            'hoten' => 'Another User',
            'email' => 'not-an-email',
            'matkhau' => 'Password123@',
            'matkhau_confirmation' => 'Password123@',
        ]);

        $invalidEmailResponse->assertSessionHasErrors('email');
    }

    public function test_registration_fails_when_password_does_not_meet_complexity_requirements(): void
    {
        // 1. Mật khẩu dưới 8 ký tự
        $responseShort = $this->post('/register', [
            'hoten' => 'User Short',
            'email' => 'short@example.com',
            'matkhau' => 'Pass1@',
            'matkhau_confirmation' => 'Pass1@',
        ]);
        $responseShort->assertSessionHasErrors('matkhau');

        // 2. Thiếu chữ hoa (toàn chữ thường, số, ký tự đặc biệt)
        $responseNoUpper = $this->post('/register', [
            'hoten' => 'User No Upper',
            'email' => 'noupper@example.com',
            'matkhau' => 'password123@',
            'matkhau_confirmation' => 'password123@',
        ]);
        $responseNoUpper->assertSessionHasErrors('matkhau');

        // 3. Thiếu chữ thường (toàn chữ hoa, số, ký tự đặc biệt)
        $responseNoLower = $this->post('/register', [
            'hoten' => 'User No Lower',
            'email' => 'nolower@example.com',
            'matkhau' => 'PASSWORD123@',
            'matkhau_confirmation' => 'PASSWORD123@',
        ]);
        $responseNoLower->assertSessionHasErrors('matkhau');

        // 4. Thiếu chữ số (toàn chữ hoa, chữ thường, ký tự đặc biệt)
        $responseNoNumber = $this->post('/register', [
            'hoten' => 'User No Number',
            'email' => 'nonumber@example.com',
            'matkhau' => 'Password@@@',
            'matkhau_confirmation' => 'Password@@@',
        ]);
        $responseNoNumber->assertSessionHasErrors('matkhau');

        // 5. Thiếu ký tự đặc biệt (chỉ có chữ hoa, thường và số)
        $responseNoSymbol = $this->post('/register', [
            'hoten' => 'User No Symbol',
            'email' => 'nosymbol@example.com',
            'matkhau' => 'Password123',
            'matkhau_confirmation' => 'Password123',
        ]);
        $responseNoSymbol->assertSessionHasErrors('matkhau');

        // 6. Xác nhận mật khẩu không khớp
        $responseMismatch = $this->post('/register', [
            'hoten' => 'User Mismatch',
            'email' => 'mismatch@example.com',
            'matkhau' => 'Password123@',
            'matkhau_confirmation' => 'DifferentPassword123@',
        ]);
        $responseMismatch->assertSessionHasErrors('matkhau');
    }
}
