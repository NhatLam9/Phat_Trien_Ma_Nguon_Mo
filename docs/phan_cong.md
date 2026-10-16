# PHÂN CÔNG CÔNG VIỆC - DET PRACTICE

> Dự án: Nền tảng luyện thi Duolingo English Test
> Thời gian: 20 ngày
> Số thành viên: 3

---

## 1. Nguyên tắc phân chia

| Nguyên tắc | Giải thích |
|---|---|
| **Module-based** | Mỗi người 1 module, làm từ đầu đến cuối |
| **Không xen** | Không ai khác đụng vào module của người kia |
| **End-to-end** | Từ DB → Controller → View → Test |
| **AI là phần cuối** | Nếu không kịp tiến độ, AI sẽ bị loại bỏ |

---

## 2. Thứ tự ưu tiên

| Thứ tự | Module | Ưu tiên | Lý do |
|---|---|---|---|
| **1** | Auth | 🔴 Bắt buộc | Không có auth thì không có hệ thống |
| **2** | Practice | 🔴 Bắt buộc | Chức năng chính, luyện tập |
| **3** | Mock Test | 🟡 Quan trọng | Thi thử, đánh giá năng lực |
| **4** | Dashboard | 🟡 Quan trọng | Theo dõi tiến độ |
| **5** | Admin | 🟢 Cần thiết | Quản lý nội dung |
| **6** | **AI** | ⚪ **Loại bỏ nếu không kịp** | Chỉ nâng cao trải nghiệm |

---

## 3. Phân chia theo module

| Thành viên | Module chính | Module phụ (nếu còn thời gian) |
|---|---|---|
| **Thành viên 1** | Auth + Practice | - |
| **Thành viên 2** | Mock Test + Dashboard | - |
| **Thành viên 3** | Admin | AI (phần cuối) |

---

## 4. Chi tiết từng thành viên

### Thành viên 1: Auth + Practice

| Công việc | Chi tiết |
|---|---|
| **Database** | Tạo bảng users, practice_attempts, bookmarks |
| **Auth** | Đăng ký, đăng nhập, đăng xuất, quên mật khẩu, Google OAuth |
| **Practice** | Luyện tập theo dạng bài, chấm tự động, đánh dấu câu yếu |
| **View** | Trang đăng nhập, đăng ký, luyện tập, kết quả |
| **Test** | Test auth, test practice |

**Git branch:** `feature/auth-practice`

---

### Thành viên 2: Mock Test + Dashboard

| Công việc | Chi tiết |
|---|---|
| **Database** | Tạo bảng tests, test_questions, user_progress, user_streaks, certificates |
| **Mock Test** | Bắt đầu thi, adaptive, chấm điểm, báo cáo kết quả |
| **Dashboard** | Thống kê, biểu đồ, streak, tiến độ |
| **View** | Trang thi, báo cáo, dashboard |
| **Test** | Test mock test, test dashboard |

**Git branch:** `feature/mock-test-dashboard`

---

### Thành viên 3: Admin + AI (phần cuối)

| Công việc | Chi tiết |
|---|---|
| **Database** | Tạo bảng ai_logs, ai_usage, questions, question_types, categories, interactive_reading_questions, interactive_listening_questions |
| **Admin** | Quản lý câu hỏi, dạng bài, chủ đề, người dùng, chứng chỉ |
| **AI (phụ)** | Chấm Writing, chấm Speaking, AI Tutor, giới hạn lượt |
| **View** | Trang admin dashboard, AI correction |
| **Test** | Test admin, test AI |

**Git branch:** `feature/admin-ai`

**Lưu ý:** AI là phần cuối cùng. Nếu không kịp tiến độ, bỏ AI và chỉ giữ Admin.

---

## 5. Lịch trình 20 ngày

### Tuần 1 (Ngày 1-5): Nền tảng

| Ngày | Thành viên 1 (Auth + Practice) | Thành viên 2 (Mock Test) | Thành viên 3 (Admin) |
|---|---|---|---|
| 1 | Setup DB, migration | Setup DB, migration | Setup DB, migration |
| 2 | Model users, Auth login | Model tests, bắt đầu thi | Model questions, CRUD |
| 3 | Đăng ký, xác minh email | Adaptive câu hỏi | Quản lý dạng bài |
| 4 | Đăng nhập Google, quên mật khẩu | Chấm điểm, báo cáo | Quản lý chủ đề |
| 5 | Practice: danh sách dạng bài | Dashboard: thống kê | Quản lý người dùng |

**Checklist cuối tuần 1:**
- [ ] Auth: đăng nhập/đăng ký hoạt động
- [ ] Mock Test: làm bài thi được
- [ ] Admin: thêm câu hỏi được

---

### Tuần 2 (Ngày 6-10): Hoàn thiện

| Ngày | Thành viên 1 | Thành viên 2 | Thành viên 3 |
|---|---|---|---|
| 6 | Practice: làm bài, chấm tự động | Dashboard: biểu đồ | Admin: chứng chỉ |
| 7 | Practice: đánh dấu câu yếu | Dashboard: streak | Admin: thống kê |
| 8 | Practice: lịch sử làm bài | Mock Test: lịch sử thi | **Bắt đầu AI** |
| 9 | Fix bug, tối ưu | Fix bug, tối ưu | AI: chấm Writing |
| 10 | Test module | Test module | AI: chấm Speaking |

**Checklist cuối tuần 2:**
- [ ] Practice hoàn chỉnh
- [ ] Mock Test hoàn chỉnh
- [ ] Admin hoàn chỉnh
- [ ] AI: chấm Writing hoạt động

---

### Tuần 3 (Ngày 11-15): Testing

| Ngày | Thành viên 1 | Thành viên 2 | Thành viên 3 |
|---|---|---|---|
| 11 | Test auth, test practice | Test mock test | Test AI, test admin |
| 12 | Fix bug | Fix bug | Fix bug |
| 13 | Test integration | Test integration | Test integration |
| 14 | Fix bug integration | Fix bug integration | Fix bug integration |
| 15 | Chuẩn bị deploy | Chuẩn bị deploy | Chuẩn bị deploy |

**Checklist cuối tuần 3:**
- [ ] Test đầy đủ các module
- [ ] Fix bug integration
- [ ] Sẵn sàng deploy

---

### Tuần 4 (Ngày 16-20): Deploy

| Ngày | Công việc chung |
|---|---|
| 16 | Deploy lên XAMPP |
| 17 | Cấu hình .env, API key |
| 18 | Test trên môi trường thật |
| 19 | Fix bug cuối |
| 20 | Hoàn thành, bàn giao |

---

## 6. Quy tắc làm việc

| Quy tắc | Giải thích |
|---|---|
| **Git branch** | Mỗi người 1 branch: `feature/auth-practice`, `feature/mock-test-dashboard`, `feature/admin-ai` |
| **Không sửa code người khác** | Chỉ sửa trong module của mình |
| **Họp hàng tuần** | Cập nhật tiến độ, giải quyết vấn đề chung |
| **Merge vào main** | Sau khi hoàn thành module |
| **Commit message** | Rõ ràng, mô tả thay đổi |

---

## 7. Bảng tổng hợp

| Module | Thành viên | Số bảng | Số Controller | Số View | Ưu tiên |
|---|---|---|---|---|---|
| Auth + Practice | TV1 | 3 | 2 | 6 | 🔴 Bắt buộc |
| Mock Test + Dashboard | TV2 | 5 | 3 | 6 | 🟡 Quan trọng |
| Admin | TV3 | 5 | 1 | 4 | 🟢 Cần thiết |
| AI | TV3 | 2 | 1 | 2 | ⚪ Phần cuối |

---

## 8. Checklist hoàn thành

### Thành viên 1
- [ ] Auth: Đăng ký, đăng nhập, đăng xuất
- [ ] Auth: Google OAuth
- [ ] Auth: Quên mật khẩu
- [ ] Practice: Luyện tập theo dạng bài
- [ ] Practice: Chấm tự động
- [ ] Practice: Đánh dấu câu yếu
- [ ] Test: Unit test auth + practice

### Thành viên 2
- [ ] Mock Test: Bắt đầu thi
- [ ] Mock Test: Adaptive câu hỏi
- [ ] Mock Test: Chấm điểm
- [ ] Mock Test: Báo cáo kết quả
- [ ] Mock Test: Cấp chứng chỉ
- [ ] Dashboard: Thống kê
- [ ] Dashboard: Biểu đồ
- [ ] Test: Unit test mock test + dashboard

### Thành viên 3
- [ ] Admin: Quản lý câu hỏi
- [ ] Admin: Quản lý người dùng
- [ ] Admin: Quản lý chứng chỉ
- [ ] AI: Chấm Writing (nếu còn thời gian)
- [ ] AI: Chấm Speaking (nếu còn thời gian)
- [ ] Test: Unit test admin + AI

---

## 9. Quyết định dựa trên tiến độ

| Tiến độ cuối tuần 2 | Quyết định |
|---|---|
| **Hoàn thành Auth + Practice + Mock Test** | Làm AI |
| **Hoàn thành Auth + Practice** | Làm AI Writing đơn giản |
| **Chưa hoàn thành Auth + Practice** | Bỏ AI, tập trung chức năng chính |
| **Chỉ hoàn thành Auth** | Bỏ AI + Dashboard + Admin, chỉ giữ Practice cơ bản |

---

## 10. Liên hệ

| Thành viên | Vai trò | Module |
|---|---|---|
| Thành viên 1 | Backend + Frontend | Auth + Practice |
| Thành viên 2 | Backend + Frontend | Mock Test + Dashboard |
| Thành viên 3 | Backend + Frontend | Admin + AI (phần cuối) |

---

*Cập nhật: 15/10/2026*
