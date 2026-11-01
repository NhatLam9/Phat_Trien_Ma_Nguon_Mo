# Dữ liệu mẫu cho MT01/MT02

Seeder: `database/seeders/MockTestDataSeeder.php`

## Mục đích

Cấp dữ liệu tối thiểu để TV2 test:

- Danh sách đề `published`.
- Bắt đầu lần thi.
- Cấp câu hỏi `fixed` và `adaptive`.
- Hiển thị câu hỏi thường, interactive reading, interactive listening.
- Hiển thị ảnh và audio.

## Chạy seeder

```bash
php artisan db:seed --class=MockTestDataSeeder
```

Hoặc chạy toàn bộ:

```bash
php artisan db:seed
```

Seeder tự bỏ qua nếu thiếu bảng:

```text
loaicauhoi, cauhoi, luachon,
cauhoi_doc, luachon_doc,
cauhoi_nghe, luachon_nghe,
dethi, dethicauhoi
```

## Dữ liệu được tạo

### 8 loại câu hỏi

| Mã | Tên | Kỹ năng | Cách chấm |
|---|---|---|---|
| `read_and_select` | Read and Select | reading | auto |
| `fill_in_the_blanks` | Fill in the Blanks | reading | auto |
| `read_and_complete` | Read and Complete | reading | auto |
| `interactive_reading` | Interactive Reading | reading | auto |
| `interactive_listening` | Interactive Listening | listening | auto |
| `write_about_photo` | Write About the Photo | writing | ai |
| `read_aloud` | Read Aloud | speaking | ai |
| `read_then_speak` | Read Then Speak | speaking | ai |

### 2 đề thi

| Tên | Kiểu | Trạng thái |
|---|---|---|
| `DET Mock Test - Fixed` | `fixed` | `published` |
| `DET Mock Test - Adaptive` | `adaptive` | `published` |

Mỗi đề có 8 câu, độ khó từ 2 đến 9.

### Câu hỏi đặc biệt

- `Interactive Reading`: 3 câu con
  - `complete_sentence`
  - `identify_idea`
  - `title_passage`
- `Interactive Listening`: 3 câu con
  - `listen_and_complete`
  - `listen_and_respond`
  - `summarise_conversation`

### Media

```text
public/images/questions/photo-1.svg
public/audio/questions/listening-1.wav
```

Giá trị lưu trong `cauhoi`:

```text
anh   = images/questions/photo-1.svg
audio = audio/questions/listening-1.wav
```

## Lấy ID đề để test

```sql
SELECT id, ten, kieu, trangthai
FROM dethi
WHERE trangthai = 'published'
ORDER BY id;
```

Hoặc:

```bash
php artisan tinker
```

```php
DB::table('dethi')->where('trangthai', 'published')->get(['id', 'ten', 'kieu']);
```

## Lưu ý

- Seeder không tạo `nguoidung`; cần tài khoản từ TV1 để đăng nhập.
- Seeder không tạo `lanthi`/`lanthicauhoi`; TV2 tự tạo khi bắt đầu thi.
- Seeder idempotent theo `loaicauhoi.ma` và `dethi.ten`.
- Khi chạy lại, `dethicauhoi` của 2 đề mẫu được tạo lại; không ảnh hưởng đề khác.
- Seeder không xóa câu hỏi/loại câu hỏi cũ. Trên DB đã có dữ liệu, tổng số câu có thể nhiều hơn 8.
- Nếu đã có `lanthi` đang dùng 2 đề mẫu, cân nhắc trước khi chạy lại seeder.
