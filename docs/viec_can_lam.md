# DANH SÁCH CÔNG VIỆC CHI TIẾT - DET PRACTICE

> Vai trò: Trưởng nhóm kỹ thuật
> Ngày: 16/10/2026
> **CSDL chính:** `docs/duoling_csdl_remake.sql` (24 bảng, tên tiếng Việt)

## Thành viên

| Thành viên | Mã | Module |
|---|---|---|
| **Thế Hào** | TV1 | Tài khoản (TK) + Luyện tập (LT) |
| **Nhật Lâm** | TV2 | Thi thử (MT) + Dashboard (DB) |
| **Phi Phong** | TV3 | Quản trị (AD) + AI (AI + TU) |

---

## CẤU TRÚC CÂY THƯ MỤC

```
det-practice/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    [TV1]
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── LogoutController.php
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── VerificationController.php
│   │   │   │   └── GoogleController.php
│   │   │   ├── Practice/                [TV1]
│   │   │   │   └── PracticeController.php
│   │   │   ├── MockTest/                [TV2]
│   │   │   │   └── MockTestController.php
│   │   │   ├── Dashboard/               [TV2]
│   │   │   │   └── DashboardController.php
│   │   │   ├── Admin/                   [TV3]
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CauhoiController.php
│   │   │   │   ├── LoaicauhoiController.php
│   │   │   │   ├── DanhmucController.php
│   │   │   │   ├── NguoidungController.php
│   │   │   │   └── DethiController.php
│   │   │   ├── Tutor/                    [TV3]
│   │   │   │   └── TutorController.php
│   │   │   ├── Certificate/             [TV2]
│   │   │   │   └── CertificateController.php
│   │   │   ├── ProfileController.php     [TV1]
│   │   │   └── BookmarkController.php    [TV1]
│   │   ├── Middleware/
│   │   │   ├── Admin.php                 [TV3]
│   │   │   └── Verified.php              [TV1]
│   │   ├── Requests/
│   │   │   ├── LoginRequest.php          [TV1]
│   │   │   ├── RegisterRequest.php       [TV1]
│   │   │   ├── ProfileRequest.php        [TV1]
│   │   │   └── QuestionRequest.php       [TV3]
│   │   └── Resources/
│   │       └── AIResource.php            [TV3]
│   ├── Models/
│   │   ├── Nguoidung.php                 [TV1]
│   │   ├── Luyentap.php                  [TV1]
│   │   ├── Danhdau.php                   [TV1]
│   │   ├── Lanthi.php                    [TV2]
│   │   ├── Lanthicauhoi.php              [TV2]
│   │   ├── Traloi.php                    [TV2]
│   │   ├── Chamdiem.php                  [TV2]
│   │   ├── Vipham.php                    [TV2]
│   │   ├── Chungchi.php                  [TV2]
│   │   ├── Tiendo.php                    [TV2]
│   │   ├── Chuoingay.php                 [TV2]
│   │   ├── Cauhoi.php                    [TV3]
│   │   ├── Loaicauhoi.php                [TV3]
│   │   ├── Danhmuc.php                   [TV3]
│   │   ├── Luachon.php                   [TV3]
│   │   ├── Dapan.php                     [TV3]
│   │   ├── CauhoiDoc.php                 [TV3]
│   │   ├── CauhoiNghe.php                [TV3]
│   │   ├── LuachonDoc.php                [TV3]
│   │   ├── LuachonNghe.php               [TV3]
│   │   ├── Dethi.php                     [TV3]
│   │   ├── Dethicauhoi.php               [TV3]
│   │   ├── Ailog.php                     [TV3]
│   │   └── Aisudung.php                  [TV3]
│   ├── Services/
│   │   ├── ScoringService.php            [TV1]
│   │   ├── AIService.php                 [TV3]
│   │   └── TestService.php               [TV2]
│   ├── Jobs/
│   │   └── ProcessAIJob.php             [TV3]
│   └── Providers/
│       └── AppServiceProvider.php       [TV1]
├── database/
│   ├── migrations/
│   │   ├── xxxx_xx_xx_create_nguoidung_table.php           [TV1]
│   │   ├── xxxx_xx_xx_create_luyentap_table.php            [TV1]
│   │   ├── xxxx_xx_xx_create_danhdau_table.php             [TV1]
│   │   ├── xxxx_xx_xx_create_lanthi_table.php              [TV2]
│   │   ├── xxxx_xx_xx_create_lanthicauhoi_table.php        [TV2]
│   │   ├── xxxx_xx_xx_create_traloi_table.php              [TV2]
│   │   ├── xxxx_xx_xx_create_chamdiem_table.php            [TV2]
│   │   ├── xxxx_xx_xx_create_vipham_table.php              [TV2]
│   │   ├── xxxx_xx_xx_create_chungchi_table.php            [TV2]
│   │   ├── xxxx_xx_xx_create_tiendo_table.php              [TV2]
│   │   ├── xxxx_xx_xx_create_chuoingay_table.php           [TV2]
│   │   ├── xxxx_xx_xx_create_cauhoi_table.php              [TV3]
│   │   ├── xxxx_xx_xx_create_loaicauhoi_table.php          [TV3]
│   │   ├── xxxx_xx_xx_create_danhmuc_table.php             [TV3]
│   │   ├── xxxx_xx_xx_create_luachon_table.php             [TV3]
│   │   ├── xxxx_xx_xx_create_dap_an_table.php               [TV3]
│   │   ├── xxxx_xx_xx_create_cauhoi_doc_table.php          [TV3]
│   │   ├── xxxx_xx_xx_create_cauhoi_nghe_table.php         [TV3]
│   │   ├── xxxx_xx_xx_create_luachon_doc_table.php         [TV3]
│   │   ├── xxxx_xx_xx_create_luachon_nghe_table.php        [TV3]
│   │   ├── xxxx_xx_xx_create_dethi_table.php               [TV3]
│   │   ├── xxxx_xx_xx_create_dethicauhoi_table.php         [TV3]
│   │   ├── xxxx_xx_xx_create_ailog_table.php               [TV3]
│   │   └── xxxx_xx_xx_create_aisudung_table.php             [TV3]
│   └── seeders/
│       └── DatabaseSeeder.php            [TV3]
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php              [TV1]
│   │   ├── auth/                         [TV1]
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── verify.blade.php
│   │   │   └── passwords/
│   │   │       └── email.blade.php
│   │   ├── practice/                     [TV1]
│   │   │   ├── types.blade.php
│   │   │   ├── select.blade.php
│   │   │   ├── show.blade.php
│   │   │   ├── result.blade.php
│   │   │   ├── danhdau.blade.php
│   │   │   └── history.blade.php
│   │   ├── mock-test/                    [TV2]
│   │   │   ├── start.blade.php
│   │   │   ├── question.blade.php
│   │   │   ├── result.blade.php
│   │   │   └── history.blade.php
│   │   ├── certificate/                 [TV2]
│   │   │   └── show.blade.php
│   │   ├── dashboard/                    [TV2]
│   │   │   ├── index.blade.php
│   │   │   ├── progress.blade.php
│   │   │   ├── chart.blade.php
│   │   │   ├── streak.blade.php
│   │   │   └── suggest.blade.php
│   │   ├── admin/                        [TV3]
│   │   │   ├── dashboard.blade.php
│   │   │   ├── cauhoi/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── edit.blade.php
│   │   │   ├── loaicauhoi/
│   │   │   │   └── index.blade.php
│   │   │   ├── danhmuc/
│   │   │   │   └── index.blade.php
│   │   │   ├── nguoidung/
│   │   │   │   └── index.blade.php
│   │   │   └── ailog/
│   │   │       └── index.blade.php
│   │   ├── tutor/                        [TV3]
│   │   │   ├── grammar.blade.php
│   │   │   ├── translate.blade.php
│   │   │   ├── rewrite.blade.php
│   │   │   └── history.blade.php
│   │   ├── profile/
│   │   │   └── edit.blade.php             [TV1]
│   │   └── welcome.blade.php              [TV1]
│   ├── css/
│   │   ├── app.css                       [TV1]
│   │   ├── auth.css                      [TV1]
│   │   ├── practice.css                  [TV1]
│   │   ├── mock-test.css                 [TV2]
│   │   ├── dashboard.css                 [TV2]
│   │   ├── admin.css                     [TV3]
│   │   └── tutor.css                     [TV3]
│   └── js/                               (KHÔNG CÓ - dự án không dùng JS)
├── public/
│   ├── css/                              (link từ resources/css)
│   ├── images/
│   │   └── uploads/                      [TV3]
│   └── audio/
│       └── uploads/                      [TV3]
├── storage/
│   └── app/
│       └── private/
│           └── audio/                    [TV1]
├── routes/
│   └── web.php                           [TV1]
├── tests/
│   └── Feature/
│       ├── AuthTest.php                  [TV1]
│       ├── RegisterTest.php              [TV1]
│       ├── BanTest.php                   [TV1]
│       ├── PracticeScoringTest.php       [TV1]
│       ├── PracticeTest.php              [TV1]
│       ├── AdaptiveTest.php              [TV2]
│       ├── TestScoringTest.php           [TV2]
│       ├── TimeoutTest.php               [TV2]
│       ├── SubmitTest.php                [TV2]
│       ├── CertificateTest.php           [TV2]
│       ├── ViphamTest.php                [TV2]
│       ├── AdminQuestionTest.php         [TV3]
│       └── AILimitTest.php               [TV3]
├── .env                                  [TV1]
├── composer.json
└── README.md
```

**Chú thích:**
- `[TV1]` = Thế Hào sở hữu
- `[TV2]` = Nhật Lâm sở hữu
- `[TV3]` = Phi Phong sở hữu

---

## GIẢI THÍCH Ý NGHĨA CÁC FOLDER

### 1. `app/` — Thư mục gốc của Laravel

| Folder | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `app/Http/Controllers/` | Nhận request → xử lý → trả view | Request → Controller → Model → View |
| `app/Http/Middleware/` | Bộ lọc request trước khi vào Controller | Request → Middleware → Controller |
| `app/Http/Requests/` | Validate dữ liệu đầu vào | Request → Validate → Controller |
| `app/Models/` | Đại diện bảng CSDL | Model ↔ MySQL (Eloquent) |
| `app/Services/` | Logic nghiệp vụ phức tạp | Controller → Service → Model |
| `app/Jobs/` | Tác vụ chạy nền | Queue → Job → Model |
| `app/Providers/` | Đăng ký service, cấu hình | Khởi động → Provider → Container |

---

### 2. `database/` — Cơ sở dữ liệu

| Folder | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `database/migrations/` | Tạo bảng, cột, index | `php artisan migrate` → MySQL |
| `database/seeders/` | Dữ liệu mẫu | `php artisan db:seed` → MySQL |

---

### 3. `resources/` — Tài nguyên hiển thị

| Folder | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `resources/views/` | Template Blade | Controller → View → HTML |
| `resources/views/layouts/` | Layout chung | View con kế thừa layout |
| `resources/views/auth/` | Trang đăng nhập, đăng ký | Controller → View |
| `resources/views/practice/` | Trang luyện tập | Controller → View |
| `resources/views/mock-test/` | Trang thi thử | Controller → View |
| `resources/views/dashboard/` | Trang thống kê | Controller → View |
| `resources/views/admin/` | Trang quản lý | Controller → View |
| `resources/views/tutor/` | Trang AI Tutor | Controller → View |
| `resources/css/` | File CSS | Link vào layout → trình duyệt |

---

### 4. `public/` — Thư mục public (truy cập trực tiếp)

| Folder | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `public/css/` | File CSS từ resources | Symlink → trình duyệt |
| `public/images/uploads/` | Hình ảnh tải lên | Upload → storage → public → trình duyệt |
| `public/audio/uploads/` | File audio tải lên | Upload → storage → public → trình duyệt |

---

### 5. `storage/` — Lưu trữ nội bộ

| Folder | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `storage/app/private/audio/` | File ghi âm người dùng | Upload → Controller → storage → Controller (kiểm tra quyền) → trình duyệt |
| `storage/app/private/images/` | Hình ảnh upload | Upload → Controller → storage → Controller (kiểm tra quyền) → trình duyệt |

**Tại sao không lưu trực tiếp vào `public/`:**
- Bảo mật: không ai truy cập trực tiếp được
- Kiểm soát: controller có thể check quyền truy cập

---

### 6. `routes/` — Định tuyến

| File | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `routes/web.php` | Định nghĩa URL → Controller | Request → Route → Controller |

**Ví dụ:**
```
GET  /login       → Auth\LoginController@showLoginForm
POST /login       → Auth\LoginController@login
GET  /practice    → Practice\PracticeController@index
POST /practice/submit → Practice\PracticeController@submit
```

---

### 7. `tests/` — Kiểm thử

| Folder | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `tests/Feature/` | Test toàn bộ luồng | Test → Route → Controller → kiểm tra response |

**Luồng test:**
```
Test → gọi route → Controller xử lý → kiểm tra kết quả → assert đúng/sai
```

---

### 8. File gốc

| File | Ý nghĩa | Luồng dữ liệu |
|---|---|---|
| `.env` | Cấu hình: DB, SMTP, Google OAuth, API key AI | Laravel đọc → Container |
| `composer.json` | Khai báo thư viện, autoload | Composer → autoload |
| `README.md` | Hướng dẫn cài đặt, sử dụng | Người dùng đọc |

---

## TÓM TẮT LUỒNG DỮ LIỆU

```
1. Request    → routes/web.php → Controller
2. Validate   → FormRequest
3. Xử lý      → Model (Eloquent) → MySQL
4. Logic phức → Service (ScoringService, AIService, TestService)
5. Trả kết quả → View (Blade) + CSS → HTML
6. File upload → storage/app/private/ → public/ (symlink)
```

---

## Thế Hào (TV1): Tài khoản (TK) + Luyện tập (LT)

**Bảng sở hữu:** `nguoidung`, `luyentap`, `danhdau`
**Service sở hữu:** `ScoringService`

### Giai đoạn 0: Khung dựng (Cả nhóm)

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 0.1 | Cài Laravel 11, cấu hình .env | `.env` | `php artisan serve` chạy được |
| 0.2 | Chốt hợp đồng giao tiếp (JSON, Service) | `docs/hop_dong.md` | Cả nhóm đồng ý |
| 0.3 | Tạo repo Git + 3 nhánh | - | `feature/tv1`, `feature/tv2`, `feature/tv3` |

---

### Giai đoạn 1: Tài khoản (TK)

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 1.1 | Migration nguoidung | `database/migrations/xxxx_create_nguoidung_table.php` | Đúng schema, có index |
| 1.2 | Model User | `app/Models/User.php` | Có relationship, fillable |
| 1.3 | TK01 Đăng ký email | `app/Http/Controllers/Auth/RegisterController.php`<br>`resources/views/auth/register.blade.php` | Validate, bcrypt, kiểm tra trùng email |
| 1.4 | TK02 Xác minh email | `app/Http/Controllers/Auth/VerificationController.php`<br>`resources/views/auth/verify.blade.php` | Gửi email có chữ ký, chưa xác minh không vào được |
| 1.5 | TK03 Đăng nhập | `app/Http/Controllers/Auth/LoginController.php`<br>`resources/views/auth/login.blade.php` | Từ chối trangthai='locked', throttle |
| 1.6 | TK04 Đăng nhập Google | `app/Http/Controllers/Auth/GoogleController.php` | Socialite, trùng email liên kết google_id |
| 1.7 | TK05 Đăng xuất | `app/Http/Controllers/Auth/LogoutController.php` | Hủy session, tạo lại CSRF |
| 1.8 | TK06 Quên mật khẩu | `app/Http/Controllers/Auth/ForgotPasswordController.php`<br>`resources/views/auth/passwords/email.blade.php` | Link có thời hạn, tài khoản Google phải đặt mật khẩu |
| 1.9 | TK07 Hồ sơ cá nhân | `app/Http/Controllers/ProfileController.php`<br>`resources/views/profile/edit.blade.php` | Sửa tên, ảnh, target_score, đổi mật khẩu |
| 1.10 | TK08 Middleware verified | `app/Http/Middleware/Verified.php` | Chặn chưa xác minh |
| 1.11 | Layout + CSS nền | `resources/views/layouts/app.blade.php`<br>`resources/css/app.css` | Thanh điều hướng theo vai trò |

---

### Giai đoạn 2: Luyện tập (LT)

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 2.1 | Migration luyentap, danhdau | `database/migrations/xxxx_create_luyentap_table.php`<br>`database/migrations/xxxx_create_danhdau_table.php` | Đúng schema |
| 2.2 | Model PracticeAttempt, Bookmark | `app/Models/PracticeAttempt.php`<br>`app/Models/Bookmark.php` | Có relationship |
| 2.3 | ScoringService | `app/Services/ScoringService.php` | `scoreAuto($question, $answer)` trả về is_correct |
| 2.4 | LT01 Danh sách dạng bài | `app/Http/Controllers/Practice/PracticeController.php`<br>`resources/views/practice/types.blade.php` | Nhóm theo kỹ năng |
| 2.5 | LT02 Chọn câu luyện | `resources/views/practice/select.blade.php` | Lọc theo dạng, chủ đề, độ khó |
| 2.6 | LT03 Trang làm bài | `resources/views/practice/show.blade.php` | Hiển thị đề, ảnh, audio, không lộ đáp án |
| 2.7 | LT04 Nộp bài | `app/Http/Controllers/Practice/PracticeController@submit` | POST, validate, lưu luyentap |
| 2.8 | LT05 Hiển thị kết quả | `resources/views/practice/result.blade.php` | Đáp án, giải thích sau khi nộp |
| 2.9 | LT06 Chuyển bài AI | `app/Http/Controllers/Practice/PracticeController@submitAI` | Gọi AIService, lưu kết quả |
| 2.10 | LT07 Đánh dấu câu yếu | `app/Http/Controllers/Practice/BookmarkController.php`<br>`resources/views/practice/danhdau.blade.php` | Thêm/bỏ đánh dấu |
| 2.11 | LT08 Lịch sử luyện tập | `resources/views/practice/history.blade.php` | Lọc theo dạng bài, ngày |
| 2.12 | LT09 Tìm kiếm câu hỏi (mở rộng) | `resources/views/practice/history.blade.php` | Theo từ khóa |

---

### Giai đoạn 3: Testing

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 3.1 | Test phân quyền | `tests/Feature/AuthTest.php` | Khách, User, Admin |
| 3.2 | Test đăng ký, đăng nhập | `tests/Feature/RegisterTest.php` | Đủ case |
| 3.3 | Test khóa tài khoản | `tests/Feature/BanTest.php` | trangthai='locked' bị đăng xuất |
| 3.4 | Test chấm tự động | `tests/Feature/PracticeScoringTest.php` | Đúng/sai các dạng bài |
| 3.5 | Test luyện tập | `tests/Feature/PracticeTest.php` | Chống nộp lặp, lỗi API |

---

## Nhật Lâm (TV2): Thi thử (MT) + Dashboard (DB)

**Bảng sở hữu:** `lanthi`, `lanthicauhoi`, `traloi`, `chamdiem`, `vipham`, `chungchi`, `tiendo`, `chuoingay`
**Service sở hữu:** `TestService`

### Giai đoạn 1: Thi thử cơ bản

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 1.1 | Migration lanthi, lanthicauhoi, traloi, chamdiem, vipham, chungchi, tiendo, chuoingay | `database/migrations/xxxx_create_lanthi_table.php`<br>`database/migrations/xxxx_create_chungchi_table.php` | CHECK điểm 10-160, UNIQUE |
| 1.2 | Model Test, TestQuestion, UserProgress, UserStreak, Certificate | `app/Models/Test.php`<br>`app/Models/TestQuestion.php`<br>`app/Models/UserProgress.php`<br>`app/Models/UserStreak.php`<br>`app/Models/Certificate.php` | Có relationship |
| 1.3 | TestService | `app/Services/TestService.php` | `nextQuestion($test)` adaptive |
| 1.4 | MT01 Bắt đầu thi | `app/Http/Controllers/MockTest/MockTestController.php`<br>`resources/views/mock-test/start.blade.php` | Chế độ full/section, 1 bài in_progress |
| 1.5 | MT02 Cấp câu kế tiếp | `resources/views/mock-test/question.blade.php` | Ghi served_at, không lặp câu |
| 1.6 | MT03 Nộp từng câu | `app/Http/Controllers/MockTest/MockTestController@submit` | POST-redirect-GET, chống nộp lặp |

---

### Giai đoạn 2: Thi thử nâng cao

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 2.1 | MT04 Kiểm soát thời gian | `resources/views/mock-test/question.blade.php` | Hiển thị giờ hết hạn, meta refresh |
| 2.2 | MT05 Chốt bài | `app/Http/Controllers/MockTest/MockTestController@finish` | Gọi ScoringService + AIService, tính điểm |
| 2.3 | MT06 Báo cáo kết quả | `resources/views/mock-test/result.blade.php` | Điểm tổng, thành phần, từng câu |
| 2.4 | MT07 Lịch sử thi | `resources/views/mock-test/history.blade.php` | So sánh điểm |
| 2.5 | MT08 Bỏ dở bài thi | `app/Http/Controllers/MockTest/MockTestController@abandon` | status = abandoned |
| 2.6 | Cấp chứng chỉ | `app/Http/Controllers/Certificate/CertificateController.php`<br>`resources/views/certificate/show.blade.php` | Tạo mã chứng chỉ, hiển thị điểm |

---

### Giai đoạn 3: Dashboard + Testing

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 3.1 | DB01 Tổng quan | `app/Http/Controllers/Dashboard/DashboardController.php`<br>`resources/views/dashboard/index.blade.php` | Số câu, tỷ lệ đúng, thời gian |
| 3.2 | DB02 Tiến độ theo dạng bài | `resources/views/dashboard/progress.blade.php` | Thanh CSS |
| 3.3 | DB03 Biểu đồ điểm | `resources/views/dashboard/chart.blade.php` | SVG trong Blade |
| 3.4 | DB04 Chuỗi ngày học | `resources/views/dashboard/streak.blade.php` | current_streak, longest_streak |
| 3.5 | DB05 Gợi ý bài hôm nay | `resources/views/dashboard/suggest.blade.php` | Dạng bài tỷ lệ đúng thấp |
| 3.6 | Test adaptive | `tests/Feature/AdaptiveTest.php` | Luật chọn câu |
| 3.7 | Test tính điểm | `tests/Feature/TestScoringTest.php` | Điểm 10-160 |
| 3.8 | Test hết giờ | `tests/Feature/TimeoutTest.php` | Tự chốt bài |
| 3.9 | Test chống nộp lặp | `tests/Feature/SubmitTest.php` | Mỗi câu 1 lần |
| 3.10 | Test chứng chỉ | `tests/Feature/CertificateTest.php` | Mã chứng chỉ hợp lệ |

---

## Phi Phong (TV3): Quản trị (AD) + AI (AI + TU)

**Bảng sở hữu:** `cauhoi`, `loaicauhoi`, `danhmuc`, `luachon`, `dap_an`, `cauhoi_doc`, `cauhoi_nghe`, `luachon_doc`, `luachon_nghe`, `dethi`, `dethicauhoi`, `ailog`, `aisudung`
**Service sở hữu:** `AIService`

### Giai đoạn 1: Quản trị (AD)

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 1.1 | Migration danhmuc, loaicauhoi, cauhoi | `database/migrations/xxxx_create_cauhoi_table.php` | Đúng schema, CHECK dokho 1-10 |
| 1.2 | Migration luachon, dap_an, cauhoi_doc, cauhoi_nghe, luachon_doc, luachon_nghe | `database/migrations/xxxx_create_cauhoi_doc_table.php`<br>`database/migrations/xxxx_create_cauhoi_nghe_table.php` | Đúng schema, FK cascade |
| 1.3 | Model Category, QuestionType, Question, InteractiveReadingQuestion, InteractiveListeningQuestion | `app/Models/Category.php`<br>`app/Models/QuestionType.php`<br>`app/Models/Question.php`<br>`app/Models/InteractiveReadingQuestion.php`<br>`app/Models/InteractiveListeningQuestion.php` | Có relationship, FK |
| 1.4 | AD01 Quản lý câu hỏi | `app/Http/Controllers/Admin/CauhoiController.php`<br>`resources/views/admin/cauhoi/index.blade.php`<br>`resources/views/admin/cauhoi/create.blade.php`<br>`resources/views/admin/cauhoi/edit.blade.php` | CRUD, lưu nháp, review, published, archived, tải ảnh/audio an toàn |
| 1.5 | AD02 Quản lý dạng bài | `app/Http/Controllers/Admin/LoaicauhoiController.php`<br>`resources/views/admin/loaicauhoi/index.blade.php` | Bật/tắt, cachcham, thoigian |
| 1.6 | AD03 CRUD chủ đề | `app/Http/Controllers/Admin/DanhmucController.php`<br>`resources/views/admin/danhmuc/index.blade.php` | CRUD |
| 1.7 | AD04 Quản lý người dùng | `app/Http/Controllers/Admin/NguoidungController.php`<br>`resources/views/admin/nguoidung/index.blade.php` | Tìm, khóa/mở, đổi vai trò |
| 1.8 | Middleware admin | `app/Http/Middleware/Admin.php` | Chặn không phải admin |
| 1.9 | Seeder dữ liệu mẫu | `database/seeders/DatabaseSeeder.php` | Admin, user, dạng bài, chủ đề, 5 câu hỏi/dạng bài |

---

### Giai đoạn 2: AI (Chấm bài + Tutor)

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 2.1 | Migration ailog, aisudung | `database/migrations/xxxx_create_ailog_table.php`<br>`database/migrations/xxxx_create_aisudung_table.php` | Đúng schema |
| 2.2 | Model AILog, AIUsage | `app/Models/AILog.php`<br>`app/Models/AIUsage.php` | Có relationship |
| 2.3 | AIService | `app/Services/AIService.php` | `grade($type, $question, $answer)` trả về score, feedback, status |
| 2.4 | AI03 Giới hạn lượt | `app/Services/AIService.php` | Đếm aisudung, vượt hạn từ chối |
| 2.5 | AI04 Ghi ailog | `app/Services/AIService.php` | Lưu input, output, model, tokens |
| 2.6 | AI05 Trạng thái trangthai_ai | `app/Services/AIService.php` | pending/done/failed, cho phép chấm lại |
| 2.7 | AI06 Bảo vệ chèn lệnh | `app/Services/AIService.php` | Prompt đặt bài làm trong khối dữ liệu riêng |
| 2.8 | AI01 Chấm Writing | `app/Services/AIService.php` | Gọi API, nhận điểm + nhận xét |
| 2.9 | AI02 Chấm Speaking (mở rộng) | `app/Services/AIService.php` | Speech-to-text, chấm nội dung |

---

### Giai đoạn 3: AI Tutor + Thống kê + Testing

| STT | Công việc | File cần tạo | Tiêu chí hoàn thành |
|---|---|---|---|
| 3.1 | TU01 Sửa ngữ pháp | `app/Http/Controllers/Tutor/TutorController.php`<br>`resources/views/tutor/grammar.blade.php` | Gọi AIService, lưu ailog |
| 3.2 | TU02 Dịch Anh-Việt | `resources/views/tutor/translate.blade.php` | 2 chiều |
| 3.3 | TU03 Viết lại bài | `resources/views/tutor/rewrite.blade.php` | Gợi ý bản tốt hơn |
| 3.4 | TU04 Lịch sử hội thoại | `resources/views/tutor/history.blade.php` | Đọc từ ailog |
| 3.5 | AD05 Thống kê hệ thống | `app/Http/Controllers/Admin/DashboardController.php`<br>`resources/views/admin/dashboard.blade.php` | Số user, câu hỏi, lượt thi, lượt AI |
| 3.6 | AD06 Xem nhật ký AI | `resources/views/admin/ai-logs/index.blade.php` | Lọc theo user, loại, lỗi |
| 3.7 | Test quản lý câu hỏi | `tests/Feature/AdminQuestionTest.php` | CRUD, upload an toàn |
| 3.8 | Test giới hạn AI | `tests/Feature/AILimitTest.php` | Vượt hạn từ chối |

---

## HỢP ĐỒNG GIAO TIẾP

| Hợp đồng | Người viết | Người gọi | Tiêu chí |
|---|---|---|---|
| `ScoringService::scoreAuto($question, $answer)` | TV1 | TV2 | Trả về is_correct (bool) |
| `AIService::grade($type, $question, $answer)` | TV3 | TV1, TV2 | Trả về score, feedback, status |
| JSON options/answer | TV3 chốt | TV1, TV2 đọc | Theo dạng bài |
| Layout + CSS nền | TV1 | TV2, TV3 dùng | Mỗi người thêm CSS riêng |

**Phụ thuộc:**
- TV2 cần `ScoringService` (TV1) và `AIService` (TV3)
- TV1 cần `AIService` (TV3) khi làm LT06
- TV3 cần `cauhoi` (TV3) — không phụ thuộc

---

## TỔNG KẾT

| Thành viên | Module | Số bảng | Số công việc | Số file ước tính |
|---|---|---|---|---|
| Thế Hào (TV1) | Auth + Practice | 3 | 30+ | 40+ |
| Nhật Lâm (TV2) | Mock Test + Dashboard | 8 | 25+ | 35+ |
| Phi Phong (TV3) | Admin + AI | 13 | 25+ | 35+ |

---

*Cập nhật: 16/10/2026*
