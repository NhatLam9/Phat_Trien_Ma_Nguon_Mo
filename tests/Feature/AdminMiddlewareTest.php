<?php

namespace Tests\Feature;

use App\Http\Middleware\Admin;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Kiểm tra middleware chặn người không phải admin (TV3 - Phi Phong).
 */
class AdminMiddlewareTest extends TestCase
{
    private function chayMiddleware(?object $user): mixed
    {
        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => $user);

        return (new Admin)->handle($request, fn () => response('ok'));
    }

    public function test_khach_chua_dang_nhap_bi_chuyen_huong_ve_login(): void
    {
        $response = $this->chayMiddleware(null);

        $this->assertTrue($response->isRedirect());
        $this->assertSame(route('login'), $response->getTargetUrl());
    }

    public function test_nguoi_dung_thuong_bi_chan(): void
    {
        $user = (object) ['vaitro' => 'user'];

        try {
            $this->chayMiddleware($user);
            $this->fail('Middleware phải chặn người dùng thường.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_quan_tri_vien_duoc_di_qua(): void
    {
        $user = (object) ['vaitro' => 'admin'];

        $response = $this->chayMiddleware($user);

        $this->assertSame('ok', $response->getContent());
    }

    public function test_route_admin_chuyen_huong_khach_ve_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }
}
