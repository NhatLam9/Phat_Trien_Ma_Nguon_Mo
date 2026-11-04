# Ngân hàng câu hỏi (TV3)

Cập nhật ngày 01/11/2026. Module quản trị câu hỏi thuộc TV3 - Phi Phong.

## Mục tiêu

Ngân hàng câu hỏi là nơi duy nhất để:

- Xem toàn bộ câu hỏi đã import.
- Lọc theo từ khóa, loại câu hỏi, kỹ năng, độ khó, trạng thái.
- Duyệt và xuất bản câu hỏi trước khi đưa vào đề thi.
- Xóa câu hỏi nháp không còn dùng.

Việc **tạo** câu hỏi lồng nhau (câu cha, câu con, lựa chọn) thực hiện bằng import JSON,
không làm form nhập tay. Lý do: câu hỏi DET có cấu trúc nhiều tầng, form HTML thuần
sẽ rất dài và dễ sai; JSON giữ đúng cấu trúc và kiểm tra được trước khi ghi.

## Đường dẫn

| Trang | URL | Route |
|---|---|---|
| Danh sách | `/admin/questions` | `admin.questions.index` |
| Chi tiết | `/admin/questions/{id}` | `admin.questions.show` |
| Đổi trạng thái | `PATCH /admin/questions/{id}/status` | `admin.questions.status` |
| Duyệt hàng loạt | `POST /admin/questions/bulk-status` | `admin.questions.bulk-status` |
| Xóa câu hỏi | `DELETE /admin/questions/{id}` | `admin.questions.destroy` |
| Nhập câu hỏi (JSON) | `/admin/questions/import` | `admin.questions.import.create` |

Tất cả đều nằm trong middleware `admin`, chỉ tài khoản `vaitro = admin` mới vào được.
Trên giao diện, mục **Nhập câu hỏi** là một tab trong Ngân hàng câu hỏi, không còn là mục riêng ở menu trái.

## Quy trình trạng thái

```text
draft ──► review ──► published ──► archived
  │          │                         │
  │          └──► draft                │
  └──────────► archived ◄──────────────┘
```

Bảng chuyển trạng thái hợp lệ nằm trong `App\Services\QuestionBankService::TRANSITIONS`:

| Từ | Được chuyển sang |
|---|---|
| `draft` | `review`, `archived` |
| `review` | `published`, `draft`, `archived` |
| `published` | `archived` |
| `archived` | `draft` |

Không cho phép nhảy bước, ví dụ `draft → published`. Câu đã xuất bản chỉ được lưu trữ,
không xóa, để không làm hỏng đề thi và lịch sử làm bài.

## Điều kiện xuất bản

Trước khi chuyển sang `published`, `QuestionBankService::loiKhiXuatBan()` kiểm tra:

- Câu hỏi có loại câu hỏi và nội dung không rỗng.
- Đường dẫn `anh`/`audio` (nếu có) phải tồn tại trong `public/`.
- Câu trắc nghiệm tự động (`read_and_select`, `fill_in_the_blanks`, `read_and_complete`):
  ít nhất 2 lựa chọn và đúng 1 đáp án đúng.
- `interactive_reading`: có ít nhất 1 câu con; mỗi câu con có lựa chọn thì đúng 1 đáp án,
  không có lựa chọn thì phải có `dapandung`.
- `interactive_listening`: có `audio` và ít nhất 1 câu con, điều kiện câu con như trên.
- `write_about_photo`: phải có `anh`.

Nếu vi phạm, câu hỏi giữ nguyên trạng thái và màn hình chi tiết hiển thị danh sách lỗi.
Duyệt hàng loạt sẽ bỏ qua câu không đủ điều kiện và báo lại mã câu + lý do.

## Khóa ổn định `ma`

- Cột `cauhoi.ma` (unique, nullable) được thêm bằng migration
  `2026_11_01_000001_add_ma_to_cauhoi_table.php`.
- Dữ liệu cũ được backfill `q-000001`, `q-000002`, ... theo id.
- Model `Cauhoi::booted()` tự sinh `ma` sau khi tạo nếu người tạo không truyền.
- Import JSON dùng `ma` để tìm câu cũ và cập nhật; không đổi `ma` của câu đã có.
- File không khai báo `ma` thì fallback theo cặp `loai_id + noidung` như trước.
- Khi cập nhật, hệ thống không ghi đè `trangthai` và `nguoitao` của câu đã có.

Nhờ vậy import lại cùng file sẽ không tạo câu trùng và không làm mất liên kết
`dethicauhoi`, `lanthicauhoi`.

## Lịch sử import

Bảng `import_batches` ghi mỗi lần import:

| Cột | Ý nghĩa |
|---|---|
| `ten_file` | Tên file JSON |
| `nguoitao` | Admin thực hiện |
| `tong_cau` | Số câu trong file |
| `tao_moi` / `cap_nhat` | Số câu tạo mới / cập nhật |
| `so_canhbao` | Số cảnh báo khi kiểm tra |
| `trangthai` | `success` hoặc `failed` |
| `ghichu` | Thông báo lỗi nếu thất bại |

Trang import hiển thị 10 lần gần nhất. Khi transaction lỗi, controller ghi một bản ghi
`failed` để vẫn còn dấu vết.

## Xóa câu hỏi

Chỉ xóa được khi:

- Câu hỏi không nằm trong đề thi nào (`dethicauhoi`).
- Câu hỏi không ở trạng thái `published`.

Xóa câu hỏi sẽ xóa dây chuyền lựa chọn, câu con và đáp án theo khóa ngoại hiện có.

## Dữ liệu demo

Các seeder liên quan:

- `AdminCatalogSeeder`: bộ danh mục + câu hỏi mẫu ban đầu (đã publish).
- `OperationalQuestionBankSeeder`: 120 câu cha, 60 câu con, 5 đề full.
- `ExtendedQuestionBankSeeder`: 23 câu mở rộng thuộc đủ 8 dạng, 10 câu con, lấy từ
  `database/seeders/data/extended_questions.json` (có mã `ma` ổn định `ext-*`).
- `FullMockTestSeeder`: đề Full 01 với 20 câu cha + 9 câu con.

Chạy lại seeder:

```bash
php artisan db:seed --class=OperationalQuestionBankSeeder
php artisan db:seed --class=ExtendedQuestionBankSeeder
php artisan db:seed --class=FullMockTestSeeder
```

File `extended_questions.json` dùng đúng định dạng import JSON, nên có thể nạp lại
thủ công qua `/admin/questions/import` nếu không muốn chạy seeder. Seeder chạy lại
nhiều lần không tạo trùng vì mỗi câu có `ma` riêng.

## Kiểm thử

```bash
php artisan test --filter=QuestionBankTest
php artisan test --filter=QuestionImportTest
```

`QuestionBankTest` kiểm tra: danh sách + lọc, chi tiết, chặn xuất bản thiếu đáp án,
xuất bản hợp lệ, chặn nhảy bước, duyệt hàng loạt bỏ qua câu lỗi, chặn xóa câu trong đề,
chặn xóa câu đã xuất bản, xóa câu nháp, `ma` tự sinh và không đổi, chặn người không phải admin.

`QuestionImportTest` kiểm tra: preview + import, file rỗng, tải template, ghi lịch sử import,
import lại cùng `ma` cập nhật thay vì tạo trùng.

## Giới hạn hiện tại

- Chưa có form sửa nội dung câu hỏi trong admin; muốn sửa thì import lại JSON cùng `ma`.
- Chưa có màn hình quản lý dạng bài / chủ đề / đề thi riêng (đang để ở bước tiếp theo).
- Chưa có phân quyền chi tiết theo từng kỹ năng; mọi admin đều duyệt được.
