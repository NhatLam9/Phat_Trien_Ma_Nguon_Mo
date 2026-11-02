<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loaicauhoi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AD02 - Quản lý dạng bài (TV3 - Phi Phong).
 *
 * Dạng bài là dữ liệu nền: import JSON tham chiếu tới `loaicauhoi.ma`.
 * Màn hình này chỉ bật/tắt, sửa tên hiển thị, cách chấm và thời gian mặc định.
 * Mã `ma` không cho sửa vì đang được file import và code dùng làm khóa.
 */
class QuestionTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.question-types.index', [
            'loais' => Loaicauhoi::query()
                ->withCount('cauhoi')
                ->orderBy('kynang')
                ->orderBy('ten')
                ->get(),
        ]);
    }

    public function update(Request $request, Loaicauhoi $loai): RedirectResponse
    {
        $duLieu = $request->validate([
            'ten' => ['required', 'string', 'max:100'],
            'cachcham' => ['required', Rule::in(['auto', 'ai', 'manual'])],
            'thoigian' => ['nullable', 'integer', 'min:1', 'max:7200'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'ten.required' => 'Tên dạng bài không được rỗng.',
            'ten.max' => 'Tên dạng bài tối đa 100 ký tự.',
            'cachcham.in' => 'Cách chấm không hợp lệ.',
            'thoigian.integer' => 'Thời gian phải là số giây.',
            'thoigian.min' => 'Thời gian tối thiểu 1 giây.',
            'thoigian.max' => 'Thời gian tối đa 7200 giây.',
        ]);

        $loai->update([
            'ten' => $duLieu['ten'],
            'cachcham' => $duLieu['cachcham'],
            'thoigian' => $duLieu['thoigian'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Đã cập nhật dạng bài \"{$loai->ten}\".");
    }
}
