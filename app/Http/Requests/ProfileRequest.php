<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'hoten' => ['required', 'string', 'max:100'],
            'muctieu' => ['nullable', 'integer', 'min:10', 'max:160'],
            'anh' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'remove_avatar' => ['nullable'],
        ];

        // Nếu người dùng nhập mật khẩu mới, kiểm tra độ an toàn và mật khẩu cũ
        if ($this->filled('matkhau')) {
            $user = $this->user();
            // Nếu tài khoản đã có mật khẩu trước đó và không phải tài khoản Google thuần
            if (!empty($user->matkhau) && empty($user->google_id)) {
                $rules['current_password'] = ['required', 'current_password'];
            }

            $rules['matkhau'] = [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                'confirmed',
            ];
        }

        return $rules;
    }

    /**
     * Get the custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'hoten.required' => 'Họ và tên không được để trống.',
            'hoten.max' => 'Họ và tên không được vượt quá 100 ký tự.',
            'muctieu.integer' => 'Điểm mục tiêu phải là số nguyên.',
            'muctieu.min' => 'Điểm mục tiêu DET tối thiểu là 10.',
            'muctieu.max' => 'Điểm mục tiêu DET tối đa là 160.',
            'anh.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'anh.mimes' => 'Định dạng hình ảnh hợp lệ: jpeg, png, jpg, webp, svg.',
            'anh.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại để đổi mật khẩu.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'matkhau.required' => 'Vui lòng nhập mật khẩu mới.',
            'matkhau.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'matkhau.regex' => 'Mật khẩu mới phải bao gồm chữ hoa, chữ thường, chữ số và ký tự đặc biệt.',
            'matkhau.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ];
    }
}

