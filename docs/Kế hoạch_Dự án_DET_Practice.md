# KẾ HOẠCH DỰ ÁN: NỀN TẢNG LUYỆN THI DUOLINGO ENGLISH TEST

> Công nghệ: **Chỉ PHP + MySQL + Laravel** (không React, không Node.js)
> Dành cho: Dự án học tập | Không liên kết với Duolingo

---

## 1. TỔNG QUAN DỰ ÁN

### 1.1. Tên dự án
**DET Practice Platform** — Nền tảng luyện thi Duolingo English Test trực tuyến (dự án học tập)

### 1.2. Mục tiêu
- Xây dựng nền tảng luyện thi DET với các dạng bài tương tự detpractice.com
- Cung cấp ngân hàng câu hỏi, mock test, AI chấm bài
- Tối ưu cho người Việt Nam

### 1.3. Phạm vi thu gọn (MVP)

| Giai đoạn | Nội dung |
|---|---|
| **MVP bắt buộc** | Auth (email + Google), Practice với các dạng chấm tự động, Mock test (từng câu một, adaptive đơn giản), Writing AI (nhập text), Dashboard, Admin |
| **Làm nếu còn thời gian** | Speaking AI (upload file), AI Tutor |
| **Hoãn lại** | Courses (có thể chỉ là trang tĩnh), membership, app |

### 1.4. Điểm khác biệt
- **Dành cho người Việt**: Giao diện song ngữ, hướng dẫn tiếng Việt
- **Nguồn câu hỏi**: Tự xây dựng theo cấu trúc chính thức của Duolingo

---

## 2. CÔNG NGHỆ (Chỉ PHP + MySQL)

| Thành phần | Công nghệ | Ghi chú |
|---|---|---|
| **Backend** | PHP 8.2 + Laravel 11 | Framework chính, xử lý toàn bộ logic |
| **Frontend** | Blade + CSS thuần + JS tối thiểu | Không Tailwind, không build step |
| **Database** | MySQL 8.0 | Cơ sở dữ liệu chính |
| **Cache** | File cache / Database cache | Không cần Redis |
| **Queue** | Laravel Queue (database driver) | Hoặc gọi AI đồng bộ |
| **File Storage** | Local storage | Lưu ảnh, audio trên server |
| **AI** | OpenAI API (GPT-4o) | Chấm bài, AI Tutor |
| **Auth** | Laravel session auth + Socialite | Đăng nhập Google bằng Socialite (thuần PHP) |
| **Deployment** | XAMPP / Shared hosting / VPS | Chạy trực tiếp PHP, không cần Node.js |

**Ràng buộc cứng:**
- ❌ Không dùng React, Vue, Angular
- ❌ Không dùng Node.js, npm, Vite
- ❌ Không dùng Tailwind (cần build bằng Node)
- ❌ Không dùng Laravel Sanctum (dành cho API/SPA)
- ❌ Không dùng Breeze/Jetstream (đi kèm Vite)
- ✅ Chỉ dùng PHP + MySQL + Blade + CSS thuần + JS tối thiểu

---

## 3. CHỨC NĂNG HỆ THỐNG

### 3.1. Sơ đồ phân hệ chức năng

```
DET Practice Platform
├── 1. Hệ thống tài khoản
│   ├── Đăng ký / Đăng nhập (Email + Google)
│   ├── Quản lý hồ sơ người dùng
│   └── Phân quyền (User / Admin)
│
├── 2. Ngân hàng câu hỏi (Practice)
│   ├── Luyện tập theo dạng bài
│   ├── Luyện tập theo chủ đề
│   ├── Xem giải thích + đáp án
│   └── Đánh dấu câu hỏi yếu
│
├── 3. Mock Test
│   ├── Thi thử full-length (từng câu một, adaptive)
│   ├── Báo cáo kết quả chi tiết
│   └── Lịch sử các lần thi
│
├── 4. AI Correction
│   ├── Chấm bài Writing (Write about the Photo, Writing Sample)
│   ├── Chấm bài Speaking (upload file audio)
│   └── Phản hồi chi tiết + gợi ý cải thiện
│
├── 5. AI Tutor
│   ├── Sửa ngữ pháp
│   ├── Dịch thuật
│   └── Viết lại bài
│
├── 6. Bảng điều khiển (Dashboard)
│   ├── Thống kê tiến độ học tập
│   ├── Biểu đồ điểm số (SVG/CSS)
│   └── Lộ trình học tập cá nhân
│
└── 7. Quản trị (Admin)
    ├── Quản lý câu hỏi
    ├── Quản lý người dùng
    ├── Thống kê hệ thống
    └── Kiểm duyệt câu hỏi cộng đồng
```

### 3.2. Chi tiết từng module

#### Module 1: Hệ thống tài khoản
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Đăng ký bằng email | Xác minh email | Cao |
| Đăng nhập Google | Socialite OAuth | Cao |
| Quên mật khẩu | Gửi link reset qua email | Trung bình |
| Hồ sơ người dùng | Ảnh, tên, mục tiêu điểm | Trung bình |
| Phân quyền | User / Admin | Cao |

#### Module 2: Ngân hàng câu hỏi (Practice)
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Luyện theo dạng bài | Các dạng bài DET | Cao |
| Luyện theo chủ đề | Ngành học, đời sống, khoa học... | Trung bình |
| Xem đáp án + giải thích | Sau khi trả lời | Cao |
| Đánh dấu câu yếu | Để ôn lại sau | Trung bình |
| Lọc theo độ khó | 1-10 | Trung bình |
| Tìm kiếm câu hỏi | Theo từ khóa | Thấp |

#### Module 3: Mock Test
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Thi full-length | Từng câu một, adaptive | Cao |
| Hạn giờ | Lưu started_at, từ chối bài quá giờ | Cao |
| AI scoring | Chấm điểm tự động | Cao |
| Báo cáo chi tiết | Điểm từng phần, điểm tổng | Cao |
| Lịch sử thi | Xem lại các lần thi trước | Trung bình |

#### Module 4: AI Correction
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Chấm Writing | Nhập text → AI chấm | Trung bình |
| Chấm Speaking | Upload file → AI chấm | Trung bình |
| Phản hồi chi tiết | Điểm từng tiêu chí | Trung bình |
| Gợi ý cải thiện | Cách viết/nói tốt hơn | Trung bình |

#### Module 5: AI Tutor
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Sửa ngữ pháp | Nhập text → sửa lỗi | Trung bình |
| Dịch thuật | Anh ↔ Việt | Trung bình |
| Viết lại bài | Improve writing | Trung bình |

#### Module 6: Dashboard
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Thống kê học tập | Số câu đã làm, thời gian | Trung bình |
| Biểu đồ điểm | SVG/CSS | Trung bình |
| Lộ trình học | Gợi ý bài tập hàng ngày | Thấp |

#### Module 7: Admin
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Quản lý câu hỏi | CRUD câu hỏi | Cao |
| Quản lý người dùng | Xem, khóa tài khoản | Trung bình |
| Thống kê hệ thống | Số user, câu hỏi, lượt thi | Trung bình |
| Kiểm duyệt câu hỏi | Duyệt câu hỏi cộng đồng | Trung bình |

---

## 4. THIẾT KẾ CƠ SỞ DỮ LIỆU (ERD)

### 4.1. Các bảng chính

#### Bảng `users`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID người dùng |
| name | VARCHAR(100) | Họ tên |
| email | VARCHAR(100) | Email (unique) |
| password | VARCHAR(255) | Mật khẩu (hash), NULL cho tài khoản Google |
| role | ENUM('user','admin') | Vai trò |
| avatar | VARCHAR(255) | Ảnh đại diện |
| google_id | VARCHAR(100) | ID Google (nếu đăng nhập GG) |
| created_at | TIMESTAMP | Ngày tạo |

#### Bảng `question_types`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID dạng bài |
| code | VARCHAR(50) | Mã dạng bài (read_aloud, listen_repeat...) |
| name | VARCHAR(100) | Tên dạng bài |
| description | TEXT | Mô tả |
| scoring_method | ENUM('auto','ai') | Cách chấm: tự động hay AI |

#### Bảng `questions`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID câu hỏi |
| type_id | INT (FK) | Dạng bài |
| category_id | INT (FK) | Chủ đề |
| content | TEXT | Nội dung câu hỏi |
| options | JSON | Các lựa chọn (nếu có) |
| answer | TEXT | Đáp án |
| explanation | TEXT | Giải thích |
| difficulty | INT (1-10) | Độ khó (dùng cho adaptive) |
| image_url | VARCHAR(255) | Hình ảnh (nếu có) |
| audio_url | VARCHAR(255) | Âm thanh (nếu có) |
| created_at | TIMESTAMP | Ngày tạo |

#### Bảng `categories`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID chủ đề |
| name | VARCHAR(100) | Tên chủ đề |
| description | TEXT | Mô tả |

#### Bảng `practice_attempts`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID |
| user_id | INT (FK) | Người dùng |
| question_id | INT (FK) | Câu hỏi |
| user_answer | TEXT | Câu trả lời |
| is_correct | BOOLEAN | Đúng/Sai |
| created_at | TIMESTAMP | Thời gian |

#### Bảng `tests`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID bài thi |
| user_id | INT (FK) | Người thi |
| score | INT | Điểm tổng (thang 10-160) |
| literacy | INT | Điểm Literacy |
| comprehension | INT | Điểm Comprehension |
| conversation | INT | Điểm Conversation |
| production | INT | Điểm Production |
| started_at | TIMESTAMP | Thời gian bắt đầu |
| completed_at | TIMESTAMP | Thời gian kết thúc |
| status | ENUM('in_progress','completed','abandoned') | Trạng thái |

#### Bảng `test_questions`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID |
| test_id | INT (FK) | Bài thi |
| question_id | INT (FK) | Câu hỏi |
| user_answer | TEXT | Câu trả lời |
| user_audio_path | VARCHAR(255) | Đường dẫn file ghi âm (nếu có) |
| is_correct | BOOLEAN | Đúng/Sai |
| ai_score | INT | Điểm AI (nếu có) |
| ai_feedback | TEXT | Phản hồi AI |

#### Bảng `ai_usage`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID |
| user_id | INT (FK) | Người dùng |
| type | VARCHAR(50) | Loại (speaking/writing/grammar/translate) |
| input | TEXT | Dữ liệu đầu vào |
| output | TEXT | Kết quả AI |
| created_at | TIMESTAMP | Thời gian |

#### Bảng `bookmarks`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID |
| user_id | INT (FK) | Người dùng |
| question_id | INT (FK) | Câu hỏi |
| created_at | TIMESTAMP | Thời gian |

#### Bảng `user_progress`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID |
| user_id | INT (FK) | Người dùng |
| total_done | INT | Tổng số câu đã làm |
| correct_count | INT | Số câu đúng |
| avg_score | FLOAT | Điểm trung bình |
| streak | INT | Số ngày học liên tiếp |
| updated_at | TIMESTAMP | Cập nhật gần nhất |

#### Bảng `courses` (Hoãn lại)
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID khóa học |
| title | VARCHAR(200) | Tiêu đề |
| description | TEXT | Mô tả |
| content | TEXT | Nội dung (có thể chỉ là trang tĩnh) |

---

## 5. KIẾN TRÚC HỆ THỐNG

### 5.1. Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────────┐
│                        Client (Browser)                     │
│              HTML + CSS thuần + JS tối thiểu                │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    Web Server (Apache/Nginx)                │
│                    Chạy PHP trực tiếp                       │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 PHP Application (Laravel)                   │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │  Controllers│  │   Models    │  │   Views     │         │
│  │  (HTTP)     │  │  (Eloquent) │  │  (Blade)    │         │
│  └──────┬──────┘  └──────┬──────┘  └─────────────┘         │
│         │                │                                   │
│         └────────┬───────┘                                   │
│                  │                                           │
│         ┌────────┴────────┐                                  │
│         │   Services      │                                  │
│         │  - AuthService  │                                  │
│         │  - TestService  │                                  │
│         │  - AIService    │                                  │
│         └────────┬────────┘                                  │
└──────────────────┼───────────────────────────────────────────┘
                   │
         ┌─────────┴─────────┐
         │                   │
         ▼                   ▼
┌─────────────────┐  ┌─────────────────┐
│   MySQL DB      │  │  AI Service     │
│   (Database)    │  │  (OpenAI API)   │
└─────────────────┘  └─────────────────┘
                   │
                   ▼
         ┌─────────────────┐
         │  File Storage   │
         │  (Local)        │
         └─────────────────┘
```

### 5.2. Cấu trúc thư mục Laravel

```
det-practice/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── PracticeController.php
│   │   │   ├── MockTestController.php
│   │   │   ├── AICorrectionController.php
│   │   │   ├── DashboardController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Question.php
│   │   ├── QuestionType.php
│   │   ├── Category.php
│   │   ├── PracticeAttempt.php
│   │   ├── Test.php
│   │   ├── TestQuestion.php
│   │   ├── AIUsage.php
│   │   ├── Bookmark.php
│   │   ├── UserProgress.php
│   │   └── Course.php
│   └── Services/
│       ├── AIService.php
│       ├── TestService.php
│       └── ScoringService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js       # JS tối thiểu (nếu cần)
│   ├── images/
│   └── audio/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       ├── practice/
│       ├── mock-test/
│       ├── ai-correction/
│       ├── dashboard/
│       └── admin/
├── routes/
│   └── web.php
└── .env
```

---

## 6. GIAO DIỆN NGƯỜI DÙNG (UI/UX)

### 6.1. Các trang chính

| Trang | Mô tả | Độ ưu tiên |
|---|---|---|
| Trang chủ (Landing) | Giới thiệu, CTA, tính năng nổi bật | Cao |
| Đăng nhập / Đăng ký | Form đăng nhập, nút Google | Cao |
| Dashboard | Tổng quan tiến độ, biểu đồ | Cao |
| Practice | Chọn dạng bài, làm bài | Cao |
| Mock Test | Màn hình thi, báo cáo kết quả | Cao |
| AI Correction | Nhập bài, xem kết quả AI | Trung bình |
| AI Tutor | Chat với AI | Trung bình |
| Admin | Quản lý câu hỏi, user | Trung bình |

### 6.2. Cách làm không cần JS

| Tính năng | Cách làm thuần PHP/CSS |
|---|---|
| Mock test adaptive | Mỗi câu là một trang form, server chọn câu tiếp theo |
| Hạn giờ | Lưu started_at, từ chối bài quá giờ |
| Biểu đồ điểm | Vẽ SVG hoặc thanh CSS ngay trong Blade |
| Video | Dùng thẻ `<iframe>` YouTube |
| Audio | Dùng thẻ `<audio controls>` |
| Ghi âm Speaking | `<input type="file" accept="audio/*" capture>` |

---

## 7. NGÂN HÀNG CÂU HỎI

### 7.1. Nguồn câu hỏi

| Nguồn | Đúng format DET? | Miễn phí? | Cách dùng |
|---|---|---|---|
| Duolingo Official PDF | ✅ Đúng | ✅ Có | Download, parse PDF |
| DET Practice free | ✅ Đúng | ✅ Có | Tham khảu cấu trúc |
| Open Trivia DB API | ❌ Sai format | ✅ Có | Dùng cho trắc nghiệm |
| Tự viết | ✅ Đúng | ✅ Có | Dựa trên cấu trúc chính thức |

### 7.2. Cấu trúc câu hỏi

Mỗi câu hỏi nên có:
- **Loại bài** (type): read_aloud, listen_repeat, write_photo...
- **Chủ đề** (category): Ngành học, Đời sống, Khoa học...
- **Nội dung** (content): Câu hỏi, đoạn văn, hình ảnh
- **Lựa chọn** (options): Đáp án A, B, C, D (nếu có)
- **Đáp án** (answer): Đáp án đúng
- **Giải thích** (explanation): Giải thích chi tiết
- **Độ khó** (difficulty): 1-10 (dùng cho adaptive)
- **Hình ảnh** (image_url): Hình ảnh minh họa
- **Âm thanh** (audio_url): File âm thanh (cho bài nghe)

### 7.3. Kế hoạch xây dựng câu hỏi

| Tuần | Số câu hỏi mục tiêu | Dạng bài ưu tiên |
|---|---|---|
| Tuần 1-2 | 50 câu | Read Aloud, Read and Select |
| Tuần 3-4 | 100 câu | Write about the Photo, Listen and Repeat |
| Tuần 5-6 | 150 câu | Read then Write, Read then Speak |
| Tuần 7-8 | 200 câu | Tổng hợp + bổ sung các dạng còn lại |
| **Tổng cộng** | **500+ câu** | Đủ các dạng bài |

### 7.4. Quy trình tạo câu hỏi

```
1. Chọn dạng bài → 2. Viết nội dung → 3. Thêm đáp án + giải thích
    → 4. Chụp hình/record audio → 5. Upload hệ thống → 6. Kiểm duyệt → 7. Publish
```

---

## 8. AI CHẤM BÀI

### 8.1. Chấm Writing
- **Bước 1:** Nhập text của người dùng
- **Bước 2:** Gọi OpenAI API (GPT-4o) để chấm
- **Bước 3:** Trả về điểm + gợi ý cải thiện

### 8.2. Chấm Speaking
- **Bước 1:** Speech-to-text (chuyển audio thành văn bản)
- **Bước 2:** Gọi LLM để chấm nội dung + độ trôi chảy
- **Bước 3:** Trả về điểm + gợi ý cải thiện

### 8.3. Lưu ý quan trọng
- **Điểm AI chỉ mang tính tham khảo** (không thể đánh giá phát âm thật sự)
- **Tên model:** Ghi rõ model OpenAI hiện hạn trong tài liệu
- **Chính sách quyền riêng tư:** Cần có chính sách khi gửi audio của người dùng cho bên thứ ba
- **HTTPS:** Ghi âm (getUserMedia) chỉ chạy trên HTTPS hoặc localhost
- **Giới hạn chi phí:** Dùng bảng `ai_usage` để kiểm soát

---

## 9. LỊCH TRÌNH DỰ ÁN (8 TUẦN)

### 9.1. Tiến độ chi tiết

| Tuần | Công việc | Output |
|---|---|---|
| **Tuần 1** | Khảo sát, phân tích yêu cầu, thiết kế DB | Tài liệu phân tích, ERD |
| **Tuần 2** | Setup môi trường, auth, layout chính | Hệ thống auth hoàn chỉnh |
| **Tuần 3** | Module Practice (CRUD câu hỏi, làm bài) | Practice hoạt động |
| **Tuần 4** | Module Mock Test (thi, chấm điểm) | Mock test cơ bản |
| **Tuần 5** | AI Correction (tích hợp OpenAI) | AI chấm bài |
| **Tuần 6** | Dashboard + Admin + Tối ưu | Dashboard, trang admin |
| **Tuần 7** | Testing, bug fixing | Sản phẩm hoàn chỉnh |
| **Tuần 8** | Triển khai, viết báo cáo | Sản phẩm + báo cáo |

### 9.2. Biểu đồ Gantt (Text)

```
Tuần:     1    2    3    4    5    6    7    8
          ├────┼────┼────┼────┼────┼────┼────┼────┤
Phân tích ████
Auth           ████
Practice           ████████
Mock Test               ████████
AI Correction               ████████
Dashboard + Admin               ████████
Testing + Deploy                    ██████
```

---

## 10. PHÂN CÔNG CÔNG VIỆC

### 10.1. Bảng phân công (4 thành viên)

| Thành viên | Vai trò | Module chính |
|---|---|---|
| **Thành viên 1** (Nhóm trưởng) | Backend + Auth + Practice | Auth, Practice, quản lý nhóm |
| **Thành viên 2** | Backend + Mock Test | Mock Test, Scoring |
| **Thành viên 3** | AI + Dashboard | AI Correction, Dashboard |
| **Thành viên 4** | Frontend + Admin | UI/UX, Admin |

### 10.2. Phân công theo tuần

| Tuần | TV1 | TV2 | TV3 | TV4 |
|---|---|---|---|---|
| 1 | Phân tích, ERD | Phân tích, ERD | Phân tích, ERD | Phân tích, ERD |
| 2 | Auth backend | Auth backend | Layout frontend | Layout frontend |
| 3 | Practice backend | Practice backend | Practice frontend | Practice frontend |
| 4 | Mock Test backend | Mock Test backend | Mock Test frontend | Mock Test frontend |
| 5 | AI backend | AI backend | Dashboard frontend | Dashboard frontend |
| 6 | Dashboard backend | Dashboard backend | Admin frontend | Admin frontend |
| 7 | Testing | Testing | Testing | Testing |
| 8 | Triển khai | Triển khai | Triển khai | Triển khai |

---

## 11. DỰ TOÁN

### 11.1. Chi phí phát triển

| Hạng mục | Chi phí (VNĐ) | Ghi chú |
|---|---|---|
| Lương nhóm (4 người × 2 tháng) | 40.000.000 | Ở mức sinh viên |
| Server (VPS) | 2.000.000 | 2 tháng |
| Tên miền | 500.000 | .com |
| OpenAI API | 3.000.000 | Ước tính |
| Dự phòng | 5.000.000 | |
| **Tổng cộng** | **50.500.000** | |

### 11.2. Chi phí vận hành hàng tháng

| Hạng mục | Chi phí (VNĐ) |
|---|---|
| Server | 1.000.000 |
| OpenAI API | 2.000.000 |
| Bảo trì | 1.000.000 |
| **Tổng cộng** | **4.000.000** |

---

## 12. YÊU CẦU KỸ THUẬT & HỆ THỐNG

### 12.1. Yêu cầu chức năng

| STT | Yêu cầu | Mô tả |
|---|---|---|
| 1 | Phù hợp | Đủ module chính |
| 2 | Chính xác | Tính điểm, thống kê đúng |
| 3 | Tương tác | Phản hồi hợp lý |
| 4 | Thực hiện đúng | Luồng xử lý tuần tự, nhất quán |
| 5 | An toàn | Phân quyền User/Admin |
| 6 | Tin cậy | Hoạt động ổn định |
| 7 | Khả năng kháng lỗi | Thông báo lỗi rõ ràng |
| 8 | Dùng được | Giao diện tiếng Việt, dễ hiểu |
| 9 | Dễ học | Dễ sử dụng |
| 10 | Dễ dùng | Nút bấm lớn, tối ưu thao tác |
| 11 | Hiệu quả | Tải trang hợp lý |
| 12 | Bảo trì được | Mã nguồn có chú thích |
| 13 | Thay đổi được | Kiến trúc module hóa |
| 14 | Kiểm thử được | Có test case cho từng module |
| 15 | Khả chuyển | Chuẩn mở, dễ migrate |

### 12.2. Yêu cầu phi chức năng

| STT | Yêu cầu | Mô tả |
|---|---|---|
| 1 | Hiệu năng | Hỗ trợ nhiều user đồng thời |
| 2 | Bảo mật | Mật khẩu hash, HTTPS, chống SQL injection |
| 3 | Tương thích | Chrome, Firefox, Safari, Edge |
| 4 | Responsive | Desktop, tablet, mobile |
| 5 | SEO | Meta tags, sitemap |

---

## 13. BẢO MẬT

| STT | Yêu cầu | Mô tả |
|---|---|---|
| 1 | XSS | Blade tự escape, không dùng `{!! !!}` cho nội dung user |
| 2 | CSRF | Thêm `@csrf` vào form |
| 3 | Throttle | Giới hạn tần suất gọi AI |
| 4 | Upload | Kiểm tra loại và dung lượng file |
| 5 | Đáp án | Không lộ đáp án trong HTML trước khi nộp bài |
| 6 | API key | Đặt trong `.env`, không đưa vào Git |
| 7 | SQL Injection | Dùng Eloquent ORM, tránh raw query |

---

## 14. RỦI RO & GIẢI PHÁP

| Rủi ro | Xác suất | Ảnh hưởng | Giải pháp |
|---|---|---|---|
| Ngân hàng câu hỏi không đủ | Cao | Cao | Ưu tiên dạng bài chính, tăng tốc tạo câu hỏi |
| OpenAI API tốn phí | Trung bình | Trung bình | Giới hạn số lần gọi, cache kết quả |
| Nhóm thiếu kinh nghiệm Laravel | Trung bình | Cao | Dùng Laravel 11, tài liệu nhiều |
| Tiến độ chậm | Trung bình | Cao | Họp hàng tuần, linh hoạt điều chỉnh phạm vi |
| Xung đột git | Thấp | Trung bình | Quy trình Git flow rõ ràng |

---

## 15. KẾT LUẬN

Dự án xây dựng nền tảng luyện thi DET Practice là khả thi với nhóm 4 người, sử dụng **chỉ PHP + MySQL + Laravel**. Sản phẩm sẽ cung cấp:

- Ngân hàng câu hỏi 500+ (MVP)
- Mock test mô phỏng bài thi thật
- AI chấm bài Writing/Speaking
- Dashboard theo dõi tiến độ

Với việc tập trung vào trải nghiệm người Việt Nam và chi phí thấp, đây là dự án học tập có giá trị.

---

## 16. PHỤ LỤC

### 16.1. Tài liệu tham khảo
- Duolingo Official PDF — https://englishtest-static.duolingo.com
- Laravel Documentation — https://laravel.com/docs
- detpractice.com — Tham khảo cấu trúc

### 16.2. Công cụ cần chuẩn bị
- [ ] Git + GitHub/GitLab
- [ ] PHP 8.2 + Composer
- [ ] MySQL 8.0 (XAMPP hoặc cài đặt riêng)
- [ ] Postman (test API)
- [ ] Figma (thiết kế UI)
- [ ] Trello/Notion (quản lý công việc)

### 16.3. Checklist trước khi bắt đầu
- [ ] Chốt phạm vi sản phẩm (MVP)
- [ ] Chốt công nghệ (Laravel + MySQL)
- [ ] Chia nhóm, phân công công việc
- [ ] Setup môi trường phát triển
- [ ] Tạo repo Git
- [ ] Thiết kế DB (ERD)
- [ ] Tạo wireframe UI
- [ ] Lập kế hoạch chi tiết từng tuần

---

## 17. QUYẾT ĐỊNH CẦN CHỐT

| STT | Quyết định | Lựa chọn |
|---|---|---|
| 1 | JavaScript | ☐ Không dùng / ☐ Tối thiểu / ☐ Linh hoạt |
| 2 | Số câu hỏi MVP | ☐ 200 / ☐ 300 / ☐ 500 |
| 3 | Thời gian | ☐ 2 tháng / ☐ 3 tháng |
| 4 | Nhóm | ☐ 4 người / ☐ 5 người |

---

*Tài liệu này được tạo dựa trên các yêu cầu từ cuộc thảo luận nhóm.*
