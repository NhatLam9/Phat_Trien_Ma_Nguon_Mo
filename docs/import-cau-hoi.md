# Import câu hỏi bằng JSON

Cập nhật ngày 28/10/2026, thuộc module TV3.

## Đường dẫn

Sau khi đăng nhập bằng tài khoản admin:

```text
/admin/questions/import
```

Màn hình cho phép:

1. Chọn file JSON.
2. Xem trước số câu, số lựa chọn, câu con và đề thi.
3. Kiểm tra lỗi/cảnh báo.
4. Xác nhận ghi database trong một transaction.

File mẫu tải tại:

```text
/admin/questions/import/template
```

## Định dạng

```json
{
  "questions": [
    {
      "ma": "reading-001",
      "loai": "read_and_select",
      "danhmuc": "Daily Life",
      "noidung": "The museum is free on Sundays, so many families ___ there.",
      "giaithich": "Chủ ngữ số nhiều nên dùng visit.",
      "dokho": 2,
      "thoigian": 60,
      "diem": 1,
      "trangthai": "draft",
      "anh": null,
      "audio": null,
      "luachon": [
        { "noidung": "visit", "ladung": true },
        { "noidung": "visits", "ladung": false }
      ]
    }
  ],
  "exam": {
    "ten": "Đề import mẫu",
    "mota": "Mô tả ngắn",
    "chedo": "full",
    "kieu": "fixed",
    "kynang": null,
    "thoigian": 3600,
    "trangthai": "draft",
    "questions": ["reading-001"]
  }
}
```

## Trường của câu hỏi

| Trường | Bắt buộc | Ghi chú |
|---|---|---|
| `ma` | Không | Mã nội bộ trong file, dùng để tham chiếu từ đề. Nếu bỏ trống sẽ tự sinh. |
| `loai` | Có | Mã trong bảng `loaicauhoi`, ví dụ `read_and_select`. |
| `noidung` | Có | Nội dung câu hỏi hoặc đoạn văn cha. |
| `danhmuc` | Không | Tên chủ đề. Nếu chưa có, hệ thống tạo mới. |
| `giaithich` | Không | Giải thích đáp án. |
| `dokho` | Không | Số nguyên 1–10, mặc định 5. |
| `thoigian` | Không | Giây, mặc định theo loại câu hỏi. |
| `diem` | Không | Mặc định 1. |
| `trangthai` | Không | `draft`, `review`, `published`, `archived`; mặc định `draft`. |
| `anh` | Không | Đường dẫn tương đối trong `public/`. |
| `audio` | Không | Đường dẫn tương đối trong `public/`. |
| `luachon` | Tùy dạng | Mảng lựa chọn của câu cha. |

## Quy tắc lựa chọn

- `read_and_select`, `fill_in_the_blanks`, `read_and_complete`: cần ít nhất 2 lựa chọn và đúng 1 lựa chọn có `ladung: true`.
- Dạng tự luận/AI như `write_about_photo`, `read_aloud`, `read_then_speak`: không cần `luachon`.
- Nếu một câu có `luachon`, phải có đúng 1 đáp án đúng.

## Câu hỏi con

### Interactive Reading

Dùng `cauhoi_doc`:

```json
{
  "cauhoi_doc": [
    {
      "loai": "identify_idea",
      "noidung": "What is the main idea?",
      "luachon": [
        { "noidung": "Đáp án đúng", "ladung": true },
        { "noidung": "Đáp án sai", "ladung": false }
      ]
    },
    {
      "loai": "title_passage",
      "noidung": "Choose the best title.",
      "dapandung": "Protecting the Reef"
    }
  ]
}
```

Loại con đọc hợp lệ:

```text
complete_sentence
complete_passage
highlight_answer
identify_idea
title_passage
```

### Interactive Listening

Dùng `cauhoi_nghe`:

```json
{
  "cauhoi_nghe": [
    {
      "loai": "listen_and_complete",
      "noidung": "The woman wants to book a room for ___.",
      "luachon": [
        { "noidung": "two nights", "ladung": true },
        { "noidung": "three nights", "ladung": false }
      ]
    },
    {
      "loai": "summarise_conversation",
      "noidung": "Summarise the conversation.",
      "dapandung": "The woman books a room for two nights."
    }
  ]
}
```

Loại con nghe hợp lệ:

```text
listen_and_complete
listen_and_respond
summarise_conversation
```

Câu con không có `luachon` bắt buộc phải có `dapandung`.

## Đề thi

Phần `exam` là tùy chọn. Nếu có:

- `ten`: bắt buộc, tối đa 150 ký tự.
- `kieu`: `fixed` hoặc `adaptive`.
- `chedo`: `full` hoặc `section`.
- `thoigian`: bắt buộc, tính bằng giây.
- `trangthai`: `draft`, `published`, `hidden`; mặc định `draft`.
- `questions`: mảng mã `ma` hoặc vị trí câu trong mảng `questions`.

Nếu đề đã tồn tại theo `ten`, hệ thống cập nhật đề và thay toàn bộ danh sách câu.

## Ghi chú

- Import chạy trong transaction: lỗi giữa chừng sẽ rollback.
- Câu hỏi được nhận diện để cập nhật theo cặp `loai_id + noidung`.
- Media chỉ được kiểm tra đường dẫn; file thật cần có trong `public/`.
- Chưa hỗ trợ đọc trực tiếp Word/PDF/Excel. Các định dạng đó cần chuyển sang JSON trước để giữ cấu trúc câu hỏi lồng nhau.
