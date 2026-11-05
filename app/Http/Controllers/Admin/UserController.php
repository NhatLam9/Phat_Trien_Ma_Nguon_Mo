<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nguoidung;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AD04 - Quản lý người dùng (TV3 - Phi Phong).
 *
 * Cho phép tìm kiếm, lọc theo vai trò/trạng thái, khóa/mở tài khoản và đổi vai trò.
 * Không cho admin tự khóa, và chỉ cho admin tự hạ quyền khi hệ thống còn
 * ít nhất một quản trị viên khác đang hoạt động để tránh mất quyền quản trị.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $boLoc = [
            'q' => trim((string) $request->query('q', '')),
            'vaitro' => (string) $request->query('vaitro', ''),
            'trangthai' => (string) $request->query('trangthai', ''),
        ];

        $nguoidungs = Nguoidung::query()
            ->when($boLoc['q'] !== '', function (Builder $query) use ($boLoc): void {
                $like = '%'.$boLoc['q'].'%';

                $query->where(function (Builder $q) use ($like): void {
                    $q->where('hoten', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->when(
                in_array($boLoc['vaitro'], ['user', 'admin'], true),
                fn (Builder $query) => $query->where('vaitro', $boLoc['vaitro'])
            )
            ->when(
                in_array($boLoc['trangthai'], ['active', 'locked'], true),
                fn (Builder $query) => $query->where('trangthai', $boLoc['trangthai'])
            )
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'nguoidungs' => $nguoidungs,
            'boLoc' => $boLoc,
            'coBoLoc' => collect($boLoc)->filter(fn ($giaTri) => $giaTri !== '')->isNotEmpty(),
        ]);
    }

    public function updateStatus(Request $request, Nguoidung $user): RedirectResponse
    {
        $duLieu = $request->validate([
            'trangthai' => ['required', Rule::in(['active', 'locked'])],
        ], [
            'trangthai.required' => 'Vui lòng chọn trạng thái.',
            'trangthai.in' => 'Trạng thái không hợp lệ.',
        ]);

        if ($user->is($request->user()) && $duLieu['trangthai'] === 'locked') {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của mình.');
        }

        $user->update(['trangthai' => $duLieu['trangthai']]);

        return back()->with(
            'success',
            $duLieu['trangthai'] === 'locked'
                ? "Đã khóa tài khoản {$user->email}."
                : "Đã mở khóa tài khoản {$user->email}."
        );
    }

    public function updateRole(Request $request, Nguoidung $user): RedirectResponse
    {
        $duLieu = $request->validate([
            'vaitro' => ['required', Rule::in(['user', 'admin'])],
        ], [
            'vaitro.required' => 'Vui lòng chọn vai trò.',
            'vaitro.in' => 'Vai trò không hợp lệ.',
        ]);

        if ($user->is($request->user()) && $duLieu['vaitro'] !== 'admin') {
            $conAdminKhac = Nguoidung::query()
                ->where('vaitro', 'admin')
                ->where('trangthai', 'active')
                ->where('id', '!=', $user->getKey())
                ->exists();

            if (! $conAdminKhac) {
                return back()->with(
                    'error',
                    'Bạn là quản trị viên cuối cùng. Hãy cấp quyền quản trị cho một tài khoản khác trước khi tự hạ quyền.'
                );
            }
        }

        $user->update(['vaitro' => $duLieu['vaitro']]);

        return back()->with(
            'success',
            $duLieu['vaitro'] === 'admin'
                ? "Đã cấp quyền quản trị cho {$user->email}."
                : "Đã chuyển {$user->email} về vai trò học viên."
        );
    }
}
