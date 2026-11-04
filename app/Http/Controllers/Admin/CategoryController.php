<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Danhmuc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AD03 - Quản lý chủ đề (TV3 - Phi Phong).
 *
 * Chủ đề gắn vào `cauhoi.danhmuc_id`. Chỉ xóa được chủ đề chưa có câu hỏi,
 * tránh làm mất liên kết của ngân hàng câu hỏi.
 */
class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'danhmucs' => Danhmuc::query()
                ->withCount('cauhoi')
                ->orderBy('ten')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $duLieu = $this->validateData($request);

        $danhmuc = Danhmuc::create($duLieu);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Đã thêm chủ đề \"{$danhmuc->ten}\".");
    }

    public function edit(Danhmuc $danhmuc): View
    {
        return view('admin.categories.edit', [
            'danhmuc' => $danhmuc,
            'soCauHoi' => $danhmuc->cauhoi()->count(),
        ]);
    }

    public function update(Request $request, Danhmuc $danhmuc): RedirectResponse
    {
        $duLieu = $this->validateData($request, $danhmuc);

        $danhmuc->update($duLieu);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Đã cập nhật chủ đề \"{$danhmuc->ten}\".");
    }

    public function destroy(Danhmuc $danhmuc): RedirectResponse
    {
        $soCauHoi = $danhmuc->cauhoi()->count();

        if ($soCauHoi > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', "Không thể xóa: chủ đề \"{$danhmuc->ten}\" đang có {$soCauHoi} câu hỏi.");
        }

        $ten = $danhmuc->ten;
        $danhmuc->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Đã xóa chủ đề \"{$ten}\".");
    }

    /**
     * @return array{ten: string, mota: string|null}
     */
    private function validateData(Request $request, ?Danhmuc $danhmuc = null): array
    {
        $duLieu = $request->validate([
            'ten' => [
                'required',
                'string',
                'max:100',
                'unique:danhmuc,ten'.($danhmuc ? ','.$danhmuc->id : ''),
            ],
            'mota' => ['nullable', 'string', 'max:1000'],
        ], [
            'ten.required' => 'Tên chủ đề không được rỗng.',
            'ten.max' => 'Tên chủ đề tối đa 100 ký tự.',
            'ten.unique' => 'Tên chủ đề này đã tồn tại.',
            'mota.max' => 'Mô tả tối đa 1000 ký tự.',
        ]);

        $duLieu['mota'] = $duLieu['mota'] ?? null;

        return $duLieu;
    }
}
