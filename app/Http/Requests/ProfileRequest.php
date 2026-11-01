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
        return [
            'hoten' => ['required', 'string', 'max:100'],
            'muctieu' => ['nullable', 'integer', 'min:10', 'max:160'],
            'anh' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
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
            'muctieu.integer' => 'Điểm mục tiêu phải là số nguyên.',
            'muctieu.min' => 'Điểm mục tiêu DET tối thiểu là 10.',
            'muctieu.max' => 'Điểm mục tiêu DET tối đa là 160.',
            'anh.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'anh.mimes' => 'Định dạng hình ảnh hợp lệ: jpeg, png, jpg, webp, svg.',
            'anh.max' => 'Kích thước ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}

