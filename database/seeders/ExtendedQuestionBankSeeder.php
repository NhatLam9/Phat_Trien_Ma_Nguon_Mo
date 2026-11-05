<?php

namespace Database\Seeders;

use App\Services\QuestionImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Bổ sung bộ câu hỏi mở rộng cho ngân hàng câu hỏi.
 *
 * Dữ liệu nằm trong database/seeders/data/extended_questions*.json, dùng đúng
 * định dạng import JSON của module quản trị nên có thể import lại thủ công
 * qua /admin/questions/import nếu cần.
 *
 * Seeder gộp tất cả file khớp mẫu extended_questions*.json, chạy lại nhiều lần
 * vẫn không tạo trùng vì mỗi câu có mã "ma" ổn định.
 * Yêu cầu: đã chạy OperationalQuestionBankSeeder (hoặc AdminCatalogSeeder) để
 * có sẵn bảng loaicauhoi và danhmuc.
 */
class ExtendedQuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $files = glob(database_path('seeders/data/extended_questions*.json')) ?: [];
        sort($files);

        if ($files === []) {
            throw new RuntimeException('Không tìm thấy file dữ liệu extended_questions*.json.');
        }

        $questions = [];

        foreach ($files as $file) {
            $json = file_get_contents($file);

            if ($json === false) {
                throw new RuntimeException("Không đọc được file dữ liệu: {$file}");
            }

            $data = json_decode($json, true);

            if (! is_array($data) || ! isset($data['questions']) || ! is_array($data['questions'])) {
                throw new RuntimeException("File dữ liệu không hợp lệ: {$file}");
            }

            $questions = array_merge($questions, $data['questions']);
        }

        $nguoiTaoId = $this->timNguoiTao();

        if ($nguoiTaoId === null) {
            $this->command?->warn(
                'Bỏ qua ExtendedQuestionBankSeeder: chưa có tài khoản người dùng nào để gán người tạo.'
            );

            return;
        }

        $service = app(QuestionImportService::class);
        $prepared = $service->prepare(json_encode(
            ['questions' => $questions],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));

        if (! empty($prepared['errors'])) {
            throw new RuntimeException(
                'Dữ liệu câu hỏi mở rộng không hợp lệ: '
                .json_encode($prepared['errors'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }

        $result = $service->import($prepared, $nguoiTaoId, 'extended_questions*.json');

        $this->command?->info(sprintf(
            'Đã nạp bộ câu hỏi mở rộng: %d câu mới, %d câu cập nhật, %d lựa chọn, %d câu con đọc, %d câu con nghe.',
            $result['stats']['questions_created'],
            $result['stats']['questions_updated'],
            $result['stats']['options'],
            $result['stats']['reading_children'],
            $result['stats']['listening_children'],
        ));

        if (! empty($prepared['warnings'])) {
            foreach ($prepared['warnings'] as $canhBao) {
                $this->command?->warn($canhBao);
            }
        }
    }

    private function timNguoiTao(): ?int
    {
        if (! Schema::hasTable('nguoidung')) {
            return null;
        }

        $adminId = (int) DB::table('nguoidung')->where('vaitro', 'admin')->value('id');

        if ($adminId > 0) {
            return $adminId;
        }

        $batKyId = (int) DB::table('nguoidung')->value('id');

        return $batKyId > 0 ? $batKyId : null;
    }
}
