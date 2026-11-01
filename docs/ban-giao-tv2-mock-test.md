# Bàn giao dữ liệu Mock Test cho TV2

Cập nhật ngày 28/10/2026, từ nhánh `feature/admin-ai`.

## Trạng thái

- Đã cherry-pick commit `c328cfa` từ nhánh `feature/tv3-seed-data-mock-test`.
- Đã gộp `MockTestDataSeeder` vào `DatabaseSeeder`.
- Đã chạy `php artisan migrate:fresh --seed` thành công.
- Đã chạy test: **43 passed (191 assertions)**.

Thứ tự seeder hiện tại:

```text
NguoidungSeeder
AdminNguoidungSeeder
AdminCatalogSeeder
MockTestDataSeeder
FullMockTestSeeder
OperationalQuestionBankSeeder
LuyentapSeeder
DanhdauSeeder
```

## Đề thi đã publish

Sau khi seed trên database sạch, hai đề có ID:

| ID | Tên | Kiểu | Số câu | Trạng thái |
|---:|---|---|---:|---|
| 1 | `DET Mock Test - Fixed` | `fixed` | 8 | `published` |
| 2 | `DET Mock Test - Adaptive` | `adaptive` | 8 | `published` |
| 3 | `DET Mock Test - Full 01` | `fixed` | 20 | `published` |
| 4 | `DET Mock Test - Operational 01` | `fixed` | 20 | `published` |
| 5 | `DET Mock Test - Operational 02` | `fixed` | 20 | `published` |
| 6 | `DET Mock Test - Operational 03` | `fixed` | 20 | `published` |
| 7 | `DET Mock Test - Operational 04` | `fixed` | 20 | `published` |
| 8 | `DET Mock Test - Operational 05` | `fixed` | 20 | `published` |

Đề `Full 01` là đề hoàn chỉnh để test luồng làm cả bài:

- 20 câu cha.
- 9 câu con: 3 câu Interactive Reading, 6 câu Interactive Listening.
- 4 kỹ năng: Reading, Listening, Writing, Speaking.
- Thời gian: 3600 giây.
- Tổng điểm: 20.

Phân bố câu hỏi:

| Dạng | Số câu |
|---|---:|
| `read_and_select` | 5 |
| `fill_in_the_blanks` | 5 |
| `read_and_complete` | 2 |
| `interactive_reading` | 1 (3 câu con) |
| `interactive_listening` | 2 (6 câu con) |
| `write_about_photo` | 2 |
| `read_aloud` | 2 |
| `read_then_speak` | 1 |

## Gói vận hành tối thiểu

`OperationalQuestionBankSeeder` bổ sung:

- 120 câu cha mới.
- 60 câu con: 30 đọc, 30 nghe.
- 5 đề full `Operational 01` đến `Operational 05`.
- Mỗi đề 20 câu cha + 9 câu con, cùng phân bố với `Full 01`.
- 10 ảnh Writing và 10 audio Listening mới.

Sau khi seed, toàn bộ ngân hàng local có:

| Nhóm | Số lượng |
|---|---:|
| Câu hỏi cha | 155 |
| Câu hỏi con đọc | 37 |
| Câu hỏi con nghe | 40 |
| Đề thi | 8 |
| Đề full | 6 |

Đây là mức **tối thiểu để chạy thử/demo có nhiều đề**, chưa phải ngân hàng production.
Muốn vận hành thật, cần bổ sung thêm nội dung do giáo viên biên soạn và duyệt.

Truy vấn lại nếu database của bạn không phải database sạch:

```sql
SELECT id, ten, kieu, trangthai
FROM dethi
WHERE trangthai = 'published'
ORDER BY id;
```

## Câu hỏi trong hai đề

Hai đề đang dùng chung 8 câu hỏi mẫu, ID `9` đến `16`, lần lượt tương ứng 8 dạng bài:

1. `read_and_select` - độ khó 2
2. `fill_in_the_blanks` - độ khó 3
3. `read_and_complete` - độ khó 4
4. `interactive_reading` - độ khó 5, có 3 câu con
5. `interactive_listening` - độ khó 6, có 3 câu con
6. `write_about_photo` - độ khó 7
7. `read_aloud` - độ khó 8
8. `read_then_speak` - độ khó 9

Kiểm tra thứ tự câu trong đề:

```sql
SELECT dethi_id, cauhoi_id, thutu
FROM dethicauhoi
WHERE dethi_id IN (1, 2)
ORDER BY dethi_id, thutu;
```

## Media

```text
public/images/questions/photo-1.svg
public/images/questions/photo-2.svg
public/images/questions/photo-3.svg
public/images/questions/photo-4.svg ... photo-13.svg
public/audio/questions/listening-1.wav
public/audio/questions/listening-2.wav
public/audio/questions/listening-3.wav
public/audio/questions/listening-bank-01.wav ... listening-bank-10.wav
```

Giá trị tương ứng trong bảng `cauhoi`:

```text
anh   = images/questions/photo-2.svg
anh   = images/questions/photo-3.svg
audio = audio/questions/listening-2.wav
audio = audio/questions/listening-3.wav
```

Audio `listening-2.wav`, `listening-3.wav` và `listening-bank-*.wav` là audio TTS dùng cho dữ liệu mẫu.
Khi có bản ghi âm thật, chỉ cần thay file cùng đường dẫn hoặc cập nhật cột `cauhoi.audio`.
Ảnh `photo-4.svg` đến `photo-13.svg` là ảnh minh họa vector, có thể thay bằng ảnh thật cùng đường dẫn.

## Import thêm câu hỏi

Admin có thể import câu hỏi bằng JSON tại:

```text
/admin/questions/import
```

Hướng dẫn chi tiết và cấu trúc file: `docs/import-cau-hoi.md`.

## Tài khoản dùng để test

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| User | `lam@example.com` | `password` |

## Lưu ý tích hợp

- `MockTestDataSeeder`, `FullMockTestSeeder` và `OperationalQuestionBankSeeder` không tạo bảng `lanthi` / `lanthicauhoi`; TV2 tự tạo khi bắt đầu lượt thi.
- Seeder idempotent theo `loaicauhoi.ma`, `cauhoi` (loại + nội dung) và `dethi.ten`.
- Đề `Full 01` dùng ID `3` sau khi seed trên database sạch; trên database hiện có, hãy lấy ID theo `dethi.ten`.
- Dữ liệu câu con đã có trong `cauhoi_doc` / `cauhoi_nghe`; view MT02 hiện tại của TV2 cần bổ sung render câu con và media.
- Nhánh TV3 hiện chưa có route `/mock-test` (thuộc PR #16 của TV2), nên chưa xem được đề Full 01 qua web tại đây.
- Trên database đã có dữ liệu, số câu hỏi có thể nhiều hơn 20; hãy lấy câu theo `dethicauhoi`.
- Nếu schema cũ chưa có các bảng TV3, dùng `php artisan migrate:fresh --seed` trên môi trường local. Không chạy `migrate` tăng dần khi chưa đối chiếu migration.
