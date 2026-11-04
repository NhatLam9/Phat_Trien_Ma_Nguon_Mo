<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'hoten' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:nguoidung,email'],
            'matkhau' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                'confirmed',
            ],
        ];
    }

    /**
     * Get the custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'hoten.required' => 'Họ và tên không được để trống.',
            'hoten.max' => 'Họ và tên không được vượt quá 100 ký tự.',
            'email.required' => 'Địa chỉ email không được để trống.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.max' => 'Địa chỉ email không được vượt quá 150 ký tự.',
            'email.unique' => 'Địa chỉ email này đã được sử dụng.',
            'matkhau.required' => 'Mật khẩu không được để trống.',
            'matkhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'matkhau.regex' => 'Mật khẩu phải bao gồm chữ hoa, chữ thường, chữ số và ký tự đặc biệt.',
            'matkhau.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
