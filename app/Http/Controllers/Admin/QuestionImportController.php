<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportBatch;
use App\Services\QuestionImportService;
use Illuminate\Http\Request;

class QuestionImportController extends Controller
{
    public function create()
    {
        return view('admin.questions.import', [
            'batches' => ImportBatch::query()
                ->with('nguoiTao')
                ->orderByDesc('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function template()
    {
        $path = public_path('downloads/import-cau-hoi-template.json');

        abort_unless(file_exists($path), 404);

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="import-cau-hoi-template.json"',
        ]);
    }

    public function preview(Request $request, QuestionImportService $service)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json,txt', 'max:10240'],
        ], [
            'file.required' => 'Vui lòng chọn file JSON.',
            'file.file' => 'File tải lên không hợp lệ.',
            'file.mimes' => 'Chỉ hỗ trợ file .json hoặc .txt.',
            'file.max' => 'File không được vượt quá 10 MB.',
        ]);

        $json = file_get_contents($request->file('file')->getRealPath());

        if ($json === false) {
            return back()->withErrors(['file' => 'Không đọc được nội dung file.']);
        }

        $prepared = $service->prepare($json);

        session([
            'admin.question_import' => [
                'json' => $json,
                'file_name' => $request->file('file')->getClientOriginalName(),
            ],
        ]);

        return view('admin.questions.preview', $prepared + [
            'file_name' => $request->file('file')->getClientOriginalName(),
        ]);
    }

    public function store(Request $request, QuestionImportService $service)
    {
        $payload = session('admin.question_import');

        if (! is_array($payload) || empty($payload['json'])) {
            return redirect()
                ->route('admin.questions.import.create')
                ->with('error', 'Phiên import đã hết hạn. Vui lòng chọn lại file.');
        }

        $tenFile = $payload['file_name'] ?? 'file.json';
        $prepared = $service->prepare($payload['json']);

        if (! empty($prepared['errors'])) {
            return view('admin.questions.preview', $prepared + [
                'file_name' => $tenFile,
            ])->with('error', 'File còn lỗi, chưa thể import.');
        }

        try {
            $result = $service->import(
                $prepared,
                (int) $request->user()->getAuthIdentifier(),
                $tenFile
            );
        } catch (\Throwable $exception) {
            report($exception);

            ImportBatch::create([
                'ten_file' => $tenFile,
                'nguoitao' => (int) $request->user()->getAuthIdentifier(),
                'tong_cau' => count($prepared['questions']),
                'tao_moi' => 0,
                'cap_nhat' => 0,
                'so_canhbao' => count($prepared['warnings'] ?? []),
                'trangthai' => 'failed',
                'ghichu' => mb_substr($exception->getMessage(), 0, 500),
            ]);

            return redirect()
                ->route('admin.questions.import.create')
                ->with('error', 'Import thất bại. Dữ liệu đã được hoàn tác, vui lòng thử lại.');
        }

        session()->forget('admin.question_import');

        return view('admin.questions.result', [
            'result' => $result,
            'file_name' => $tenFile,
        ]);
    }
}
