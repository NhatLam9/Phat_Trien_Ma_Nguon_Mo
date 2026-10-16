# Ngữ cảnh sử dụng hệ thống DET Practice

## 1. Ngữ cảnh: Khách (chưa đăng nhập)

Khi truy cập vào trang chủ DET Practice, một người dùng mới vai trò **Khách** sẽ thấy:

- **Thanh điều hướng chỉ gồm:** Trang chủ, Đăng nhập, Đăng ký
- **Trang giới thiệu** hiển thị:
  - Giới thiệu về kỳ thi Duolingo English Test
  - Các tính năng chính của nền tảng (Luyện tập, Mock Test, AI chấm bài, Chứng chỉ thi thử)
  - Nút "Bắt đầu ngay" dẫn đến trang Đăng ký
- **Không thể:**
  - Xem danh sách câu hỏi
  - Làm bài Practice
  - Làm Mock Test
  - Sử dụng AI chấm bài
  - Xem Dashboard
  - Truy cập trang Admin

## 2. Ngữ cảnh: Quản trị viên (Admin)

Sau khi đăng nhập bằng tài khoản có `role = 'admin'`, **Quản trị viên** được phép:

- **Quản lý câu hỏi:**
  - Thêm câu hỏi mới (chọn dạng bài, chủ đề, độ khó, nội dung, đáp án, giải thích)
  - Sửa, ẩn (archive), hoặc xóa câu hỏi
  - Tải lên hình ảnh và file audio cho câu hỏi

- **Quản lý dạng bài:**
  - Bật/tắt dạng bài (`is_active`)
  - Đặt giới hạn thời gian mặc định (`time_limit_s`)
  - Chọn cách chấm (tự động / AI)

- **Quản lý chủ đề:** Thêm, sửa, xóa chủ đề câu hỏi

- **Quản lý người dùng:**
  - Xem danh sách người dùng
  - Khóa/mở tài khoản (`is_active`)
  - Thêm người dùng mới
  - Xóa người dùng (nếu không có dữ liệu liên quan)

- **Thống kê hệ thống:**
  - Số người dùng, số câu hỏi theo dạng bài
  - Số lượt thi, số lượt gọi AI theo ngày
  - Xem nhật ký AI

- **Kiểm soát chứng chỉ:**
  - Xem danh sách chứng chỉ thi thử
  - Tra cứu chứng chỉ theo mã
