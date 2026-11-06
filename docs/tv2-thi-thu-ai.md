# TV2 – Hoàn thiện tích hợp Thi thử và AI

## Phạm vi

- Sử dụng AIService và ProcessAIGradingJob của TV3, không gọi API nhà cung cấp từ controller Thi thử.
- Khi xác nhận bắt đầu: giữ trước một lượt AI cho mỗi câu AI published trong đề; mở trang chuẩn bị không trừ lượt.
- Tiếp tục phiên không trừ lại; nộp từng câu dùng lượt đã giữ. Phiên cũ trước migration trừ từng lượt khi nộp.
- Hoàn lượt chưa dùng khi chốt, hết giờ hoặc bỏ dở. Lượt chấm thất bại được job hoàn theo ngày đã trừ.
- Speaking nhận file private tối đa 10 MB; Writing tối đa 3000 ký tự. Gửi job sau khi transaction lưu câu trả lời hoàn tất.
- Giữ điểm AI DET trong diem_ai; điểm có trọng số trong diem = (diem_ai - 10) / 150 × diemtoida, làm tròn 2 chữ số, đảo công thức tổng hợp của dự án. Đây là công thức thi thử của dự án, không phải công thức chính thức của DET.
- Job cũ hoặc lặp không ghi đè điểm mới. Khi job hoàn tất, điểm tổng được tính lại cho lượt thi completed.
- Trang kết quả liên kết tới trang trạng thái/chấm lại hiện có của AI. Audio đi qua route kiểm tra chủ sở hữu.
- Backend cấp chứng chỉ chặn cả pending, failed và câu đã nộp nhưng chưa có điểm. Mỗi lượt thi chỉ có một chứng chỉ; kết quả đã cấp chứng chỉ không bị sửa qua hàm ghi điểm.

## Cập nhật và chạy

```powershell
php artisan migrate
php artisan optimize:clear
php artisan queue:restart
php artisan queue:work --tries=2 --timeout=160
```

Cấu hình nhà cung cấp và queue theo docs/ai.md. Chế độ kiểm thử dùng AI_PROVIDER=mock, không gọi API thật.
Không dùng migrate:fresh trên database đang có dữ liệu.

## Kiểm thử

```powershell
php artisan test --filter="MockTestIntegrationTest|LearningDashboardTest|ProcessAIGradingJobTest|AISubsystemTest"
php artisan test
```

Các ca mới bao phủ chuẩn bị/bắt đầu, trừ lượt gộp, tiếp tục phiên, hết giờ, bỏ dở, adaptive, nộp trùng, điểm cuối, job AI và chứng chỉ, quyền sở hữu, thống kê, chuỗi ngày và gợi ý học tập.
