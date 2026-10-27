<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nguoidung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $validated = $request->validate([
            'hoten' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:nguoidung'],
            'matkhau' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = Nguoidung::create([
            'hoten' => $validated['hoten'],
            'email' => $validated['email'],
            'matkhau' => Hash::make($validated['matkhau']),
            'vaitro' => 'user',
            'trangthai' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('practice.index');
    }
}