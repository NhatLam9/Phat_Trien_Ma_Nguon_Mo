<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cauhoi;
use App\Models\Danhmuc;
use App\Models\Dethi;
use App\Models\Dethicauhoi;
use App\Models\Loaicauhoi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * AD07 - Quản lý đề thi (TV3 - Phi Phong).
 *
 * Ngoài việc nhập đề từ JSON, quản trị viên có thể tạo đề trực tiếp bằng
 * giao diện: khai báo thông tin đề, sau đó chọn câu hỏi từ ngân hàng và
 * sắp xếp thứ tự. Điểm từng câu lấy theo `cauhoi.diem`, tổng điểm của đề
 * được tính lại sau mỗi lần thêm/bớt câu.
 */
class ExamController extends Controller
{
    /**
     * Các kỹ năng hợp lệ cho đề thi theo từng phần.
     *
     * @var array<int, string>
     */
    private const KY_NANG = ['reading', 'listening', 'writing', 'speaking'];

    public function index(Request $request): View
    {
        $boLoc = [
            'q' => trim((string) $request->query('q', '')),
            'trangthai' => (string) $request->query('trangthai', ''),
            'kynang' => (string) $request->query('kynang', ''),
        ];

        $dethis = Dethi::query()
            ->with('nguoiTao')
            ->withCount('dethicauhoi')
            ->when($boLoc['q'] !== '', function (Builder $query) use ($boLoc): void {
                $like = '%'.$boLoc['q'].'%';

                $query->where(function (Builder $q) use ($like): void {
                    $q->where('ten', 'like', $like)
                        ->orWhere('mota', 'like', $like);
                });
            })
            ->when(
                in_array($boLoc['trangthai'], ['draft', 'published', 'hidden'], true),
                fn (Builder $query) => $query->where('trangthai', $boLoc['trangthai'])
            )
            ->when(
                in_array($boLoc['kynang'], self::KY_NANG, true),
                fn (Builder $query) => $query->where('kynang', $boLoc['kynang'])
            )
            ->orderByDesc('ngaytao')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.exams.index', [
            'dethis' => $dethis,
            'boLoc' => $boLoc,
            'coBoLoc' => collect($boLoc)->filter(fn ($giaTri) => $giaTri !== '')->isNotEmpty(),
            'thongKe' => [
                'tong' => Dethi::query()->count(),
                'published' => Dethi::query()->where('trangthai', 'published')->count(),
                'draft' => Dethi::query()->where('trangthai', 'draft')->count(),
                'hidden' => Dethi::query()->where('trangthai', 'hidden')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.exams.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $duLieu = $this->validateMeta($request);

        $dethi = Dethi::create([
            'ten' => $duLieu['ten'],
            'mota' => $duLieu['mota'] ?? null,
            'chedo' => $duLieu['chedo'],
            'kieu' => $duLieu['kieu'],
            'kynang' => $duLieu['chedo'] === 'section' ? ($duLieu['kynang'] ?? null) : null,
            'thoigian' => (int) $duLieu['thoigian_phut'] * 60,
            'tongdiem' => 0,
            'trangthai' => $duLieu['trangthai'],
            'nguoitao' => $request->user()?->id,
        ]);

        return redirect()
            ->route('admin.exams.edit', $dethi)
            ->with('success', "Đã tạo đề thi \"{$dethi->ten}\". Hãy thêm câu hỏi vào đề bên dưới.");
    }

    public function edit(Request $request, Dethi $exam): View
    {
        $boLoc = [
            'q' => trim((string) $request->query('q', '')),
            'kynang' => (string) $request->query('kynang', ''),
            'loai' => (string) $request->query('loai', ''),
            'danhmuc' => (string) $request->query('danhmuc', ''),
        ];

        $exam->load([
            'nguoiTao',
            'dethicauhoi.cauhoi.loai',
            'dethicauhoi.cauhoi.danhmuc',
        ]);

        $daChon = $exam->dethicauhoi->pluck('cauhoi_id')->all();

        $cauhois = Cauhoi::query()
            ->with(['loai', 'danhmuc'])
            ->whereNotIn('id', $daChon)
            ->timKiem($boLoc['q'])
            ->kyNang($boLoc['kynang'])
            ->loaiCauHoi($boLoc['loai'])
            ->when(
                $boLoc['danhmuc'] !== '' && ctype_digit($boLoc['danhmuc']),
                fn (Builder $query) => $query->where('danhmuc_id', (int) $boLoc['danhmuc'])
            )
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.exams.edit', [
            'dethi' => $exam,
            'cauhois' => $cauhois,
            'boLoc' => $boLoc,
            'coBoLoc' => collect($boLoc)->filter(fn ($giaTri) => $giaTri !== '')->isNotEmpty(),
            'loais' => Loaicauhoi::query()->orderBy('kynang')->orderBy('ten')->get(),
            'danhmucs' => Danhmuc::query()->orderBy('ten')->get(),
        ]);
    }

    public function update(Request $request, Dethi $exam): RedirectResponse
    {
        $duLieu = $this->validateMeta($request);

        $exam->update([
            'ten' => $duLieu['ten'],
            'mota' => $duLieu['mota'] ?? null,
            'chedo' => $duLieu['chedo'],
            'kieu' => $duLieu['kieu'],
            'kynang' => $duLieu['chedo'] === 'section' ? ($duLieu['kynang'] ?? null) : null,
            'thoigian' => (int) $duLieu['thoigian_phut'] * 60,
            'trangthai' => $duLieu['trangthai'],
        ]);

        return redirect()
            ->route('admin.exams.edit', $exam)
            ->with('success', "Đã cập nhật thông tin đề thi \"{$exam->ten}\".");
    }

    public function addQuestions(Request $request, Dethi $exam): RedirectResponse
    {
        $duLieu = $request->validate([
            'cauhoi_ids' => ['required', 'array', 'min:1'],
            'cauhoi_ids.*' => ['integer', 'exists:cauhoi,id'],
        ], [
            'cauhoi_ids.required' => 'Vui lòng chọn ít nhất một câu hỏi để thêm vào đề.',
            'cauhoi_ids.min' => 'Vui lòng chọn ít nhất một câu hỏi để thêm vào đề.',
            'cauhoi_ids.*.exists' => 'Có câu hỏi không tồn tại trong ngân hàng.',
        ]);

        $daChon = $exam->dethicauhoi()->pluck('cauhoi_id')->all();
        $thuTu = (int) $exam->dethicauhoi()->max('thutu');
        $them = 0;
        $boQua = 0;

        DB::transaction(function () use ($duLieu, $exam, $daChon, &$thuTu, &$them, &$boQua): void {
            $cauhois = Cauhoi::query()
                ->whereIn('id', array_unique(array_map('intval', $duLieu['cauhoi_ids'])))
                ->get()
                ->keyBy('id');

            foreach (array_unique(array_map('intval', $duLieu['cauhoi_ids'])) as $cauhoiId) {
                $cauhoi = $cauhois->get($cauhoiId);

                if ($cauhoi === null || in_array($cauhoiId, $daChon, true)) {
                    $boQua++;

                    continue;
                }

                $thuTu++;

                Dethicauhoi::create([
                    'dethi_id' => $exam->id,
                    'cauhoi_id' => $cauhoi->id,
                    'thutu' => $thuTu,
                    'diem' => $cauhoi->diem ?: 1,
                ]);

                $them++;
            }

            $this->capNhatTongDiem($exam);
        });

        $thongBao = "Đã thêm {$them} câu hỏi vào đề.";
        if ($boQua > 0) {
            $thongBao .= " Bỏ qua {$boQua} câu đã có hoặc không hợp lệ.";
        }

        return redirect()
            ->route('admin.exams.edit', $exam)
            ->with($them > 0 ? 'success' : 'error', $thongBao);
    }

    public function removeQuestion(Dethi $exam, int $question): RedirectResponse
    {
        $dong = Dethicauhoi::query()
            ->where('dethi_id', $exam->id)
            ->where('cauhoi_id', $question)
            ->first();

        if ($dong === null) {
            return redirect()
                ->route('admin.exams.edit', $exam)
                ->with('error', 'Câu hỏi này không nằm trong đề.');
        }

        DB::transaction(function () use ($dong, $exam): void {
            $dong->delete();
            $this->sapXepLai($exam);
            $this->capNhatTongDiem($exam);
        });

        return redirect()
            ->route('admin.exams.edit', $exam)
            ->with('success', 'Đã bỏ câu hỏi khỏi đề và cập nhật lại tổng điểm.');
    }

    public function moveQuestion(Request $request, Dethi $exam, int $question): RedirectResponse
    {
        $duLieu = $request->validate([
            'huong' => ['required', Rule::in(['up', 'down'])],
        ], [
            'huong.required' => 'Thiếu hướng di chuyển.',
            'huong.in' => 'Hướng di chuyển không hợp lệ.',
        ]);

        $rows = Dethicauhoi::query()
            ->where('dethi_id', $exam->id)
            ->orderBy('thutu')
            ->get()
            ->values();

        $viTri = $rows->search(fn (Dethicauhoi $dong) => (int) $dong->cauhoi_id === $question);

        if ($viTri === false) {
            return redirect()
                ->route('admin.exams.edit', $exam)
                ->with('error', 'Câu hỏi này không nằm trong đề.');
        }

        $viTriDoi = $duLieu['huong'] === 'up' ? $viTri - 1 : $viTri + 1;

        if ($viTriDoi < 0 || $viTriDoi >= $rows->count()) {
            return redirect()
                ->route('admin.exams.edit', $exam)
                ->with('error', 'Câu hỏi đã ở vị trí đầu hoặc cuối của đề.');
        }

        $hienTai = $rows[$viTri];
        $hangXom = $rows[$viTriDoi];

        DB::transaction(function () use ($hienTai, $hangXom): void {
            // Dùng một giá trị tạm để tránh vi phạm ràng buộc unique (dethi_id, thutu).
            DB::table('dethicauhoi')
                ->where('id', $hienTai->id)
                ->update(['thutu' => 60000]);

            DB::table('dethicauhoi')
                ->where('id', $hangXom->id)
                ->update(['thutu' => $hienTai->thutu]);

            DB::table('dethicauhoi')
                ->where('id', $hienTai->id)
                ->update(['thutu' => $hangXom->thutu]);
        });

        return redirect()
            ->route('admin.exams.edit', $exam)
            ->with('success', 'Đã đổi thứ tự câu hỏi.');
    }

    public function show(Dethi $exam): View
    {
        $exam->load([
            'nguoiTao',
            'dethicauhoi.cauhoi.loai',
            'dethicauhoi.cauhoi.danhmuc',
        ]);

        return view('admin.exams.show', [
            'dethi' => $exam,
        ]);
    }

    /**
     * @return array{ten: string, mota: string|null, chedo: string, kieu: string, kynang: string|null, thoigian_phut: int, trangthai: string}
     */
    private function validateMeta(Request $request): array
    {
        $duLieu = $request->validate([
            'ten' => ['required', 'string', 'max:150'],
            'mota' => ['nullable', 'string', 'max:2000'],
            'chedo' => ['required', Rule::in(['full', 'section'])],
            'kieu' => ['required', Rule::in(['fixed', 'adaptive'])],
            'kynang' => ['nullable', 'required_if:chedo,section', Rule::in(self::KY_NANG)],
            'thoigian_phut' => ['required', 'integer', 'min:1', 'max:600'],
            'trangthai' => ['required', Rule::in(['draft', 'published', 'hidden'])],
        ], [
            'ten.required' => 'Tên đề thi không được rỗng.',
            'ten.max' => 'Tên đề thi tối đa 150 ký tự.',
            'mota.max' => 'Mô tả tối đa 2000 ký tự.',
            'chedo.required' => 'Vui lòng chọn chế độ thi.',
            'chedo.in' => 'Chế độ thi không hợp lệ.',
            'kieu.required' => 'Vui lòng chọn kiểu đề thi.',
            'kieu.in' => 'Kiểu đề thi không hợp lệ.',
            'kynang.required_if' => 'Đề thi theo từng kỹ năng phải chọn kỹ năng.',
            'kynang.in' => 'Kỹ năng không hợp lệ.',
            'thoigian_phut.required' => 'Vui lòng nhập thời gian làm bài.',
            'thoigian_phut.integer' => 'Thời gian làm bài phải là số nguyên (phút).',
            'thoigian_phut.min' => 'Thời gian làm bài tối thiểu 1 phút.',
            'thoigian_phut.max' => 'Thời gian làm bài tối đa 600 phút.',
            'trangthai.required' => 'Vui lòng chọn trạng thái đề.',
            'trangthai.in' => 'Trạng thái đề không hợp lệ.',
        ]);

        return $duLieu;
    }

    /**
     * Đánh số lại thứ tự câu hỏi từ 1..n sau khi xoá.
     */
    private function sapXepLai(Dethi $exam): void
    {
        $rows = Dethicauhoi::query()
            ->where('dethi_id', $exam->id)
            ->orderBy('thutu')
            ->orderBy('id')
            ->get();

        // Tạm đẩy toàn bộ thứ tự lên cao để không đụng ràng buộc unique khi đánh lại.
        DB::table('dethicauhoi')
            ->where('dethi_id', $exam->id)
            ->update(['thutu' => DB::raw('thutu + 1000')]);

        foreach ($rows->values() as $chiSo => $dong) {
            DB::table('dethicauhoi')
                ->where('id', $dong->id)
                ->update(['thutu' => $chiSo + 1]);
        }
    }

    private function capNhatTongDiem(Dethi $exam): void
    {
        $tongDiem = (float) Dethicauhoi::query()
            ->where('dethi_id', $exam->id)
            ->sum('diem');

        $exam->update(['tongdiem' => $tongDiem]);
    }
}
