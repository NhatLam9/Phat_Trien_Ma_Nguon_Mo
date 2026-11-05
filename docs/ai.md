# Phân hệ AI - DET Practice

Tài liệu mô tả kiến trúc, cách vận hành và cách kiểm thử phân hệ AI (AI Tutor, chấm Writing/Speaking, hạn mức, nhật ký AI).

## 1. Phạm vi

| Chức năng | Entry point | Trạng thái |
| --- | --- | --- |
| AI Tutor: sửa ngữ pháp, dịch, viết lại | `/tutor/*` | Đồng bộ + PRG, trừ/hoàn lượt ngay trong request |
| Luyện tập Writing/Speaking | `/practice/ai/*` | Bất đồng bộ qua queue, xem trạng thái bằng meta refresh |
| Chấm lại bài lỗi | `POST /practice/ai/retry/{id}` | Luôn tốn 1 lượt mới, chỉ khi `trangthai_ai = 'failed'` |
| Nghe lại audio Speaking | `GET /practice/ai/audio/{id}` | File private, kiểm tra quyền sở hữu |
| Nhật ký AI | `/admin/ai-logs` | Lọc theo loại, trạng thái, người dùng, ngày |

Nguyên tắc chung:

- Không dùng JavaScript phía client. Trang chờ dùng `<meta http-equiv="refresh" content="5">`.
- Mỗi câu chấm AI tiêu 1 lượt. Lượt chỉ được hoàn đúng 1 lần. "Chấm lại" luôn tiêu lượt mới.
- Không trả điểm giả khi thiếu API key. Thiếu cấu hình là lỗi thật và được ghi log.

## 2. Cấu hình môi trường

Các biến trong `.env` (xem mẫu ở `.env.example`):

```dotenv
APP_TIMEZONE=Asia/Ho_Chi_Minh

QUEUE_CONNECTION=database
DB_QUEUE_RETRY_AFTER=200

AI_PROVIDER=gemini          # gemini | openai | mock
GEMINI_API_KEY=
GEMINI_MODEL=gemini-1.5-flash
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
OPENAI_STT_MODEL=whisper-1

AI_DAILY_LIMIT=20
AI_MIN_SCORE=10
AI_MAX_SCORE=160
```

- `AI_PROVIDER=mock`: chế độ offline cho test/local, trả điểm và nhận xét mô phỏng, không gọi mạng.
- `QUEUE_CONNECTION=sync` chỉ nên dùng khi debug nhanh; production/staging phải dùng `database` và chạy worker.
- Múi giờ `Asia/Ho_Chi_Minh` quyết định ngày reset hạn mức (`aisudung.ngay`).

## 3. Cơ sở dữ liệu

### `aisudung`

- Khóa duy nhất `(nguoidung_id, ngay)`.
- `solan`: số lượt đã dùng trong ngày.
- Mọi thao tác trừ/hoàn đều dùng transaction ngắn + `lockForUpdate`.

### `traloi`

Các cột phục vụ AI:

| Cột | Ý nghĩa |
| --- | --- |
| `trangthai_ai` | `pending` → `done` hoặc `failed` |
| `diem_ai` | Điểm DET sau khi clamp (10-160, bội số 5) |
| `nhanxet_ai` | Nhận xét; với Speaking có thể chứa transcript hoặc marker lỗi |
| `luot_cham` | Số lần chấm; job cũ có số khác sẽ bị loại |
| `ngay_tru_luot` | Ngày đã trừ lượt, dùng để hoàn đúng ngày |
| `thoidiem_cham` | Mốc bắt đầu chấm, dùng cho timeout pending 5 phút |
| `audio` | Đường dẫn tương đối trong private disk (Speaking) |

### `ailog`

Ghi sau mỗi lần gọi API, cả thành công lẫn thất bại:

- `loai`: `writing`, `speaking`, `grammar`, `translate`, `rewrite`, ...
- `dauvao`, `daura`: nội dung gửi/nhận (không chứa API key).
- `model`, `token_vao`, `token_ra`.
- `trangthai`: `success` hoặc `failed`.
- `http_code`: mã HTTP từ nhà cung cấp (nếu có).

Migration bổ sung cột trạng thái: `database/migrations/2026_11_04_000001_add_status_columns_to_ailog_table.php`.

## 4. Vòng đời hạn mức

Các hàm lõi trong `app/Services/AIService.php`:

- `checkAndDeductLimit(int $userId)`: trừ 1 lượt, ném `AiLimitExceededException` khi hết.
- `checkAndDeductLimitBulk(int $userId, int $soLuot)`: trừ gộp N lượt trong một transaction. Không đủ N thì không trừ đồng nào. Dành cho Mock Test (TV2 gọi khi bắt đầu bài thi).
- `refundUsage(int $userId, $date)`: hoàn 1 lượt, không giảm dưới 0.
- `markFailedAndRefund(...)`: cập nhật có điều kiện `pending` + đúng `luot_cham` sang `failed`, chỉ hoàn lượt khi cập nhật đúng 1 dòng.

Quy tắc:

1. **Luyện tập:** trừ 1 lượt trong cùng transaction với việc tạo `lanthi`/`lanthicauhoi`/`traloi`.
2. **Mock Test:** trừ gộp đủ N lượt lúc bắt đầu thi; khi nộp từng câu không trừ nữa.
3. **Chấm lại:** chỉ khi `trangthai_ai = 'failed'`. Trừ lượt trước, sau đó cập nhật có điều kiện sang `pending`; nếu thua race thì hoàn lượt.
4. **Hoàn lượt:** mọi nhánh thất bại của bài chấm đều đi qua `markFailedAndRefund` để đảm bảo tối đa 1 lần hoàn.
5. **Timeout pending:** quá 5 phút vẫn `pending` thì Controller tự chuyển `failed` và hoàn lượt (an toàn khi hai tab cùng tải).

## 5. Luồng chấm Writing/Speaking

1. Controller validate đầu vào (Writing `max:3000`; Speaking `mimes:mp3,wav,m4a,ogg,webm`, `max:10240`).
2. Lưu audio Speaking vào `storage/app/private/audio`, chỉ lưu đường dẫn vào DB.
3. Trong transaction: trừ lượt + tạo `traloi` với `pending`, `luot_cham = 1`, `ngay_tru_luot`, `thoidiem_cham`.
4. Sau commit: `ProcessAIGradingJob::dispatch($traloiId, $userId, $date, $attempt)->afterCommit()`.
5. Job gọi API **ngoài transaction**, sau đó cập nhật có điều kiện:

```php
Traloi::where('id', $traloiId)
    ->where('trangthai_ai', 'pending')
    ->where('luot_cham', $attempt)
    ->update([...]);
```

6. View GET `/practice/ai/status/{id}` tự refresh mỗi 5 giây khi `pending`.

Job mặc định:

- `$tries = 2`, `$timeout = 150`, `$backoff = [10, 30]`.
- Quy tắc cấu hình: `retry_after (200) > worker --timeout (160) > Job $timeout (150)`.

## 6. Pipeline Speaking

1. Nếu `traloi.noidung` đã có transcript (lần chấm lại) thì bỏ qua STT.
2. Ngược lại gọi Whisper (OpenAI) hoặc Gemini Audio, timeout 40 giây.
3. Transcript dài quá 3000 ký tự:
   - Trả `status = 'failed'`, kèm marker `[TRANSCRIPT_TOO_LONG]` trong `nhanxet_ai`.
   - Job xóa `noidung` để buộc nộp audio mới.
   - View ẩn nút "Chấm lại"; route retry cũng chặn để không mất lượt vô ích.
4. Chấm transcript theo rubric DET, timeout 30 giây.
5. Giao diện luôn hiện ghi chú: "Điểm số dựa trên bản chép lời tự động, không đánh giá hoàn toàn độ chính xác của phát âm".

## 7. Phân loại lỗi

| Loại | Ví dụ | Xử lý |
| --- | --- | --- |
| Tạm thời | timeout, HTTP 429, 5xx, mất kết nối | Ném `AiTemporaryException` để Job retry; hết lượt thử thì `failed()` hoàn lượt |
| Vĩnh viễn | 4xx, JSON hỏng, thiếu `score`/`feedback`, transcript quá dài | Trả `status = 'failed'`; Job gọi `markFailedAndRefund` |
| Thiếu cấu hình | không có `GEMINI_API_KEY`/`OPENAI_API_KEY` | Trả `failed` rõ ràng, ghi `ailog` với `trangthai = 'failed'`, hoàn lượt |

Mỗi lần gọi API đều ghi `ailog` kèm `trangthai` và `http_code` (nếu có). Không log API key/header.

## 8. AI Tutor

- Luồng PRG: POST xử lý → redirect → GET hiển thị kết quả từ session flash.
- Trừ lượt trước khi gọi API; lỗi API thì `refundUsage($userId, $today)` và `withInput()` để giữ nội dung đã nhập.
- Khi hết hạn mức, không gọi API và trả lỗi `ai_limit`.
- Không dùng `markFailedAndRefund` cho Tutor vì không có bản ghi `traloi`.

## 9. Âm thanh Speaking

- Lưu tại `storage/app/private/audio` (private disk), không nằm trong `public/`.
- Phát qua route `practice.ai.audio` có kiểm tra chủ sở hữu; người khác nhận 403.
- Lệnh dọn dẹp: `php artisan ai:clean-audio --days=30`.
- Scheduler đã đăng ký trong `routes/console.php`, chạy hằng ngày lúc 02:00.
- Khi `audio = NULL`, view tự ẩn trình phát.

## 10. Hướng dẫn vận hành

```bash
# Cài đặt
php artisan migrate
php artisan storage:link

# Chạy worker (cửa sổ riêng)
php artisan queue:work --tries=2 --timeout=160

# Sau mỗi lần sửa code
php artisan queue:restart

# Dev: chạy scheduler (Windows dùng Task Scheduler gọi schedule:run mỗi phút)
php artisan schedule:work
```

Kiểm tra sức khỏe nhanh:

- `ailog` có dòng `trangthai = 'failed'` kèm `http_code` khi API lỗi.
- `aisudung.solan` không tăng khi bấm "Chấm lại" lúc bài đang `done`/`pending`.
- Bài quá 5 phút `pending` tự chuyển `failed` và hoàn lượt.
- Speaking quá dài: không có nút "Chấm lại", không bị trừ lượt khi gọi retry trực tiếp.

## 11. Kiểm thử

Các test liên quan:

```bash
php artisan test tests/Feature/AISubsystemTest.php
php artisan test tests/Feature/AIServiceGradingTest.php
php artisan test tests/Feature/AILimitTest.php
php artisan test tests/Feature/AIRetryAndPendingTest.php
php artisan test tests/Feature/AITutorTest.php
php artisan test tests/Feature/ProcessAIGradingJobTest.php
php artisan test tests/Feature/CleanOldAudioFilesTest.php
php artisan test tests/Feature/AdminAILogTest.php
```

`AISubsystemTest` bao phủ các hành vi then chốt:

- View grammar render mảng lồng nhau, không lỗi "Array to string conversion".
- Route audio chặn người không sở hữu (403) và phát được cho chủ sở hữu.
- Chấm lại khi bài `done`/`pending` không trừ lượt, không đổi trạng thái.
- Chấm lại khi hết hạn mức giữ nguyên `failed`, không trừ thêm.
- Thiếu API key trả `failed` rõ ràng, không trả điểm giả, có ghi `ailog` thất bại.
- Lỗi HTTP 5xx ghi `ailog` với `http_code = 500`.
- Trừ gộp N lượt chặn đúng khi không đủ (kể cả người dùng chưa có dòng hạn mức).
- Transcript quá dài ẩn nút "Chấm lại" và chặn route retry.

Test dùng SQLite in-memory, `RefreshDatabase`, `AI_PROVIDER=mock` trong `phpunit.xml`.

## 12. Ghi chú tích hợp

- **Mock Test (TV2):** gọi `AIService::checkAndDeductLimitBulk($userId, $n)` khi bắt đầu bài thi. Không gọi `checkAndDeductLimit` khi nộp từng câu.
- **Ngân hàng câu hỏi (PR #22):** có thể xung đột tên controller/view `Ailog*` với nhánh AI. Kiểm tra `routes/admin.php` và `resources/views/admin/ai-logs/*` khi merge.
- Audio câu hỏi nghe dùng đường dẫn tương đối trong `public/`; người vận hành tự copy file, không upload qua web.
