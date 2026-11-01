<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang chỉnh sửa thông tin hồ sơ cá nhân.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Cập nhật thông tin cơ bản: họ tên, điểm mục tiêu DET, ảnh đại diện sử dụng ProfileRequest.
     */
    public function update(ProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->hoten = $validated['hoten'];
        $user->muctieu = $validated['muctieu'] ?? null;

        // Xử lý tải lên ảnh đại diện mới nếu có
        if ($request->hasFile('anh')) {
            // Xóa ảnh cũ nếu đã từng lưu trên đĩa public
            if ($user->anh && !str_starts_with($user->anh, 'http') && Storage::disk('public')->exists($user->anh)) {
                Storage::disk('public')->delete($user->anh);
            }

            $path = $request->file('anh')->store('avatars', 'public');
            $user->anh = $path;
        }

        $user->save();

        return back()->with('status', 'Cập nhật thông tin hồ sơ cá nhân thành công!');
    }

    /**
     * Cập nhật mật khẩu tài khoản (yêu cầu >= 8 ký tự, chữ hoa, thường, số và ký tự đặc biệt).
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $rules = [
            'matkhau' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                'confirmed',
            ],
        ];

        // Nếu tài khoản đã có mật khẩu trước đó, yêu cầu nhập mật khẩu hiện tại
        if (!empty($user->matkhau)) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules, [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'matkhau.required' => 'Vui lòng nhập mật khẩu mới.',
            'matkhau.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'matkhau.regex' => 'Mật khẩu mới phải bao gồm chữ hoa, chữ thường, chữ số và ký tự đặc biệt.',
            'matkhau.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user->forceFill([
            'matkhau' => Hash::make($request->matkhau),
        ])->save();

        return back()->with('status_password', 'Đổi mật khẩu thành công!');
    }
}
