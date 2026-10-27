# Bàn giao dữ liệu Mock Test cho TV2

Cập nhật ngày 27/10/2026, từ nhánh `feature/admin-ai`.

## Trạng thái

- Đã cherry-pick commit `c328cfa` từ nhánh `feature/tv3-seed-data-mock-test`.
- Đã gộp `MockTestDataSeeder` vào `DatabaseSeeder`.
- Đã chạy `php artisan migrate:fresh --seed` thành công.
- Đã chạy test: **35 passed (117 assertions)**.

Thứ tự seeder hiện tại:

```text
NguoidungSeeder
AdminNguoidungSeeder
AdminCatalogSeeder
MockTestDataSeeder
LuyentapSeeder
DanhdauSeeder
```

## Đề thi đã publish

Sau khi seed trên database sạch, hai đề có ID:

| ID | Tên | Kiểu | Số câu | Trạng thái |
|---:|---|---|---:|---|
| 1 | `DET Mock Test - Fixed` | `fixed` | 8 | `published` |
| 2 | `DET Mock Test - Adaptive` | `adaptive` | 8 | `published` |

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
public/audio/questions/listening-1.wav
```

Giá trị tương ứng trong bảng `cauhoi`:

```text
anh   = images/questions/photo-1.svg
audio = audio/questions/listening-1.wav
```

## Tài khoản dùng để test

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| User | `lam@example.com` | `password` |

## Lưu ý tích hợp

- `MockTestDataSeeder` không tạo bảng `lanthi` / `lanthicauhoi`; TV2 tự tạo khi bắt đầu lượt thi.
- Seeder idempotent theo `loaicauhoi.ma` và `dethi.ten`.
- Trên database đã có dữ liệu, số câu hỏi có thể nhiều hơn 8; hãy lấy câu theo `dethicauhoi`.
- Nếu schema cũ chưa có các bảng TV3, dùng `php artisan migrate:fresh --seed` trên môi trường local. Không chạy `migrate` tăng dần khi chưa đối chiếu migration.
