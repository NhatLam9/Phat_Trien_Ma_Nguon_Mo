# Quản trị nội dung (TV3)

Tài liệu này mô tả các màn hình quản trị đã hoàn thiện trong module Quản trị (AD02–AD04, AD06).
Mọi màn hình đều nằm dưới tiền tố `/admin`, dùng middleware `admin` và giao diện Blade + CSS viết tay
(không JavaScript, không Node/npm).

## 1. Dạng bài — `/admin/question-types`

- File: `app/Http/Controllers/Admin/QuestionTypeController.php`
- View: `resources/views/admin/question-types/index.blade.php`
- Route: `admin.question-types.index`, `admin.question-types.update`

Dạng bài là dữ liệu nền cho import JSON: file import tham chiếu tới `loaicauhoi.ma`.

| Được sửa | Không được sửa |
|---|---|
| `ten` (tên hiển thị) | `ma` (khóa ổn định cho import và code) |
| `cachcham` (`auto` / `ai` / `manual`) | `kynang` |
| `thoigian` (1–7200 giây, có thể để trống) | |
| `is_active` (bật/tắt) | |

Mỗi dòng trong bảng là một form HTML5 gửi bằng thuộc tính `form="loai-{id}"`, không cần JavaScript.
Bảng hiển thị thêm số câu hỏi đang dùng dạng bài đó (`withCount('cauhoi')`).

## 2. Chủ đề — `/admin/categories`

- File: `app/Http/Controllers/Admin/CategoryController.php`
- View: `resources/views/admin/categories/index.blade.php`, `edit.blade.php`
- Route: `admin.categories.index|store|edit|update|destroy`

- Tên chủ đề bắt buộc, tối đa 100 ký tự và không trùng.
- Mô tả tối đa 1000 ký tự.
- Chỉ xóa được chủ đề chưa có câu hỏi. Chủ đề đang được dùng sẽ bị chặn và báo số câu hỏi liên quan,
  tránh làm mất liên kết `cauhoi.danhmuc_id`.
- Sau khi thêm/sửa/xóa đều quay về danh sách kèm thông báo.

## 3. Người dùng — `/admin/users`

- File: `app/Http/Controllers/Admin/UserController.php`
- View: `resources/views/admin/users/index.blade.php`
- Route: `admin.users.index|status|role`

- Tìm theo họ tên hoặc email; lọc theo vai trò (`user` / `admin`) và trạng thái (`active` / `locked`).
- Phân trang 20 dòng, giữ bộ lọc khi chuyển trang (`withQueryString`).
- Khóa/mở khóa tài khoản bằng `trangthai`.
- Đổi vai trò giữa học viên và quản trị.
- Chặn admin tự khóa hoặc tự hạ quyền chính mình để không mất quyền quản trị.
- Không cho gửi giá trị vai trò/trạng thái ngoài danh sách hợp lệ.

## 4. Nhật ký AI — `/admin/ai-logs`

- File: `app/Http/Controllers/Admin/AiLogController.php`
- View: `resources/views/admin/ai-logs/index.blade.php`
- Route: `admin.ai-logs.index`

Màn hình chỉ đọc, phục vụ kiểm tra lỗi chấm Writing/Speaking và phát hiện lạm dụng lượt gọi AI.

- Lọc theo người dùng (họ tên/email), loại yêu cầu, khoảng ngày.
- Hiển thị thời gian, người dùng, loại, model, token vào/ra.
- Đầu vào/đầu ra xem trong `<details>` để không làm nặng trang.
- Khoảng ngày không hợp lệ (đến ngày trước từ ngày) bị chặn kèm thông báo.

Giá trị `loai` hợp lệ: `speaking`, `writing`, `grammar`, `translate`, `rewrite`, `chat`.

## 5. Kiểm thử

| File | Nội dung |
|---|---|
| `tests/Feature/AdminCatalogTest.php` | Dạng bài + chủ đề: hiển thị, cập nhật, giữ mã, validate, chặn xóa chủ đề đang dùng |
| `tests/Feature/AdminUserTest.php` | Danh sách, lọc, khóa/mở khóa, đổi vai trò, chặn tự khóa/tự hạ quyền |
| `tests/Feature/AdminAiLogTest.php` | Hiển thị, lọc theo loại/người dùng/ngày, validate, chặn học viên |

Toàn bộ test của dự án chạy bằng:

```bash
php artisan test
```

## 6. Ghi chú kỹ thuật

- Không có JavaScript: form inline dùng thuộc tính `form` của HTML5 và các nút submit thường.
- CSS dùng chung trong `public/css/admin.css` (các lớp `admin-inline-form`, `admin-row-actions`,
  `admin-input--narrow`, `admin-details`).
- Các route dùng route model binding: `{loai}` → `Loaicauhoi`, `{danhmuc}` → `Danhmuc`,
  `{user}` → `Nguoidung`.
- Màn hình Đề thi (AD05) vẫn để ở trạng thái "sẽ làm ở bước tiếp theo" trong menu.

## 7. Bố cục giao diện

- Sidebar tối, chia nhóm **Tổng quan / Nội dung / Hệ thống**, mỗi mục có icon SVG nội tuyến.
- Mục đang mở được đánh dấu bằng nền xanh nhạt và vạch xanh bên trái.
- Cuối sidebar có thẻ người dùng đang đăng nhập.
- **Nhập câu hỏi** nằm trong tab của Ngân hàng câu hỏi (`admin/questions/import`), không còn là mục riêng trên menu.
- Topbar dính trên cùng, hiển thị nhãn “Quản trị nội dung”, tiêu đề trang và link về trang chủ.
- Nội dung giới hạn chiều rộng 1280px, bảng có header nền nhạt và bo góc.
- Responsive: dưới 960px sidebar chuyển thành thanh ngang; dưới 720px form lọc về một cột.
- Không dùng JavaScript; mọi tương tác dựa trên HTML/CSS và form submit thường.

