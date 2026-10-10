# KẾ HOẠCH DỰ ÁN: NỀN TẢNG LUYỆN THI DUOLINGO ENGLISH TEST (DET PRACTICE)

> Dựa trên mô hình detpractice.com | Công nghệ: PHP (Laravel) + MySQL
> Nhóm phát triển: 4 thành viên | Thời gian: 2 tháng

---

## 1. TỔNG QUAN DỰ ÁN

### 1.1. Tên dự án
**DET Practice Platform** — Nền tảng luyện thi Duolingo English Test trực tuyến

### 1.2. Đơn vị chủ trì
Nhóm sinh viên — Đại học Nha Trang

### 1.3. Tính cấp thiết
- DET là kỳ thi tiếng Anh được hàng nghìn trường đại học trên thế giới công nhận, ngày càng phổ biến tại Việt Nam.
- Các nền tảng luyện thi hiện tại (detpractice, arno, det ready) đều trả phí và chủ yếu phục vụ thị trường quốc tế.
- Xây dựng nền tảng luyện thi DET miễn phí/giá rẻ, tối ưu cho người Việt Nam là nhu cầu thiết thực.

### 1.4. Mục tiêu
| STT | Mục tiêu | Tiêu chí đo lường |
|---|---|---|
| 1 | Xây dựng nền tảng luyện thi DET hoàn chỉnh | Đủ 5 module chính |
| 2 | Cung cấp ngân hàng câu hỏi đa dạng | ≥ 500 câu hỏi MVP |
| 3 | Mock test mô phỏng bài thi thật | Adaptive, AI scoring |
| 4 | AI chấm bài Speaking/Writing | Tích hợp API AI |
| 5 | Giao diện thân thiện, dễ sử dụng | Responsive, tiếng Việt/Anh |

### 1.5. Phạm vi
- **Nội dung**: Luyện thi Duolingo English Test (DET)
- **Không gian**: Web app, chạy trên trình duyệt (desktop + mobile)
- **Thời gian**: 2 tháng (8 tuần)

---

## 2. PHÂN TÍCH ĐỐI THỦ (detpractice.com)

### 2.1. Điểm mạnh detpractice cần học tập
| Tính năng | Chi tiết | Ưu tiên sao chép |
|---|---|---|
| Ngân hàng câu hỏi lớn | 18,000+ câu, cập nhật hàng ngày | Cao |
| Mock test adaptive | Mô phỏng bài thi thật, AI scoring | Cao |
| AI Correction | Chấm Speaking/Writing tự động | Trung bình (cần API AI) |
| AI Tutor | Sửa ngữ pháp, dịch, viết lại | Trung bình |
| Hệ thống membership | Free + Pro, coupon | Thấp (có thể bỏ qua) |
| App mobile | iOS + Android | Thấp (làm sau) |

### 2.2. Điểm khác biệt của dự án này
- **Miễn phí hoàn toàn** hoặc giá rẻ hơn đáng kể
- **Tối ưu cho người Việt**: giao diện song ngữ, hướng dẫn tiếng Việt
- **Cộng đồng**: chia sẻ kinh nghiệm, câu hỏi do cộng đồng đóng góp

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
│   ├── Thi thử full-length (adaptive)
│   ├── Báo cáo kết quả chi tiết
│   └── Lịch sử các lần thi
│
├── 4. AI Correction
│   ├── Chấm bài Speaking (Read Aloud, Speak about Photo...)
│   ├── Chấm bài Writing (Write about Photo, Writing Sample)
│   └── Phản hồi chi tiết + gợi ý cải thiện
│
├── 5. Khóa học (Courses)
│   ├── Hướng dẫn từng dạng bài
│   ├── Bài mẫu + chiến lược
│   └── Video bài giảng (tùy chọn)
│
├── 6. AI Tutor
│   ├── Sửa ngữ pháp
│   ├── Dịch thuật
│   └── Viết lại bài
│
├── 7. Bảng điều khiển (Dashboard)
│   ├── Thống kế tiến độ học tập
│   ├── Biểu đồ điểm số
│   └── Lộ trình học tập cá nhân
│
└── 8. Quản trị (Admin)
    ├── Quản lý câu hỏi
    ├── Quản lý người dùng
    ├── Quản lý khóa học
    └── Thống kê hệ thống
```

### 3.2. Chi tiết từng module

#### Module 1: Hệ thống tài khoản
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Đăng ký bằng email | Xác minh email | Cao |
| Đăng nhập Google | OAuth 2.0 | Cao |
| Quên mật khẩu | Gửi link reset qua email | Trung bình |
| Hồ sơ người dùng | Ảnh, tên, mục tiêu điểm | Trung bình |
| Phân quyền | User / Admin | Cao |

#### Module 2: Ngân hàng câu hỏi (Practice)
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Luyện theo dạng bài | 15+ dạng bài DET | Cao |
| Luyện theo chủ đề | Ngành học, đời sống, khoa học... | Trung bình |
| Xem đáp án + giải thích | Sau khi trả lời | Cao |
| Đánh dấu câu yếu | Để ôn lại sau | Trung bình |
| Lọc theo độ khó | Dễ / Trung bình / Khó | Trung bình |
| Tìm kiếm câu hỏi | Theo từ khóa | Thấp |

**Các dạng bài DET cần hỗ trợ:**
1. Read Aloud
2. Write about the Photo
3. Listen and Repeat
4. Read and Complete
5. Read and Select
6. Listen and Select
7. Read then Write
8. Read then Speak
9. Listen then Speak
10. Speaking Sample
11. Writing Sample
12. Interactive Reading
13. Interactive Listening
14. Writing (Interactive)
15. Speaking (Interactive)

#### Module 3: Mock Test
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Thi full-length | 60-90 phút, adaptive | Cao |
| AI scoring | Chấm điểm tự động | Cao |
| Báo cáo chi tiết | Điển từng phần, điểm tổng | Cao |
| Lịch sử thi | Xem lại các lần thi trước | Trung bình |
| Chế độ thi thử | Làm 1 phần bài thi | Trung bình |

#### Module 4: AI Correction
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Chấm Speaking | Ghi âm → AI chấm | Trung bình |
| Chấm Writing | Nhập text → AI chấm | Trung bình |
| Phản hồi chi tiết | Điểm từng tiêu chí | Trung bình |
| Gợi ý cải thiện | Cách viết/nói tốt hơn | Trung bình |

#### Module 5: Khóa học (Courses)
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Hướng dẫn từng dạng bài | Text + hình ảnh | Trung bình |
| Bài mẫu | Ví dụ chuẩn | Trung bình |
| Chiến lược làm bài | Mẹo, thủ thuật | Trung bình |
| Video bài giảng | Tùy chọn | Thấp |

#### Module 6: AI Tutor
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Sửa ngữ pháp | Nhập text → sửa lỗi | Trung bình |
| Dịch thuật | Anh ↔ Việt | Trung bình |
| Viết lại bài | Improve writing | Trung bình |

#### Module 7: Dashboard
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Thống kê học tập | Số câu đã làm, thời gian | Trung bình |
| Biểu đồ điểm | Line chart theo thời gian | Trung bình |
| Lộ trình học | Gợi ý bài tập hàng ngày | Thấp |
| Streak | Ngày học liên tiếp | Thấp |

#### Module 8: Admin
| Chức năng | Mô tả | Độ ưu tiên |
|---|---|---|
| Quản lý câu hỏi | CRUD câu hỏi | Cao |
| Quản lý người dùng | Xem, khóa tài khoản | Trung bình |
| Quản lý khóa học | CRUD khóa học | Trung bình |
| Thống kê hệ thống | Số user, câu hỏi, lượt thi | Trung bình |

---

## 4. SƠ ĐỒ LUỒNG DỮ LIỆU (DFD)

### 4.1. DFD Mức 0 (Context Diagram)

```
┌─────────────┐         ┌─────────────────────────┐         ┌─────────────┐
│             │         │                         │         │             │
│  Người dùng │────────▶│   DET Practice Platform │────────▶│  Admin      │
│  (User)     │◀────────│                         │◀────────│  (Quản trị) │
│             │         │                         │         │             │
└─────────────┘         └─────────────────────────┘         └─────────────┘
                                   │
                                   │
                                   ▼
                        ┌─────────────────────┐
                        │   AI Service        │
                        │   (OpenAI API)      │
                        └─────────────────────┘
```

### 4.2. DFD Mức 1

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        DET Practice Platform                                │
│                                                                             │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐              │
│  │ 1. Auth  │    │ 2. Practice│   │ 3. Mock  │    │ 4. AI    │              │
│  │          │    │          │    │   Test   │    │Correction│              │
│  └────┬─────┘    └────┬─────┘    └────┬─────┘    └────┬─────┘              │
│       │               │               │               │                     │
│       └───────┬───────┴───────┬───────┴───────┬───────┘                     │
│               │               │               │                             │
│               ▼               ▼               ▼                             │
│         ┌─────────────────────────────────────────┐                        │
│         │           Cơ sở dữ liệu (MySQL)        │                        │
│         │  - users, questions, tests, results     │                        │
│         │  - courses, lessons, ai_feedback       │                        │
│         └─────────────────────────────────────────┘                        │
│                                                                             │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐                              │
│  │ 5. Course│    │ 6. AI    │    │ 7. Dash  │                              │
│  │          │    │  Tutor   │    │  board   │                              │
│  └──────────┘    └──────────┘    └──────────┘                              │
│                                                                             │
│  ┌──────────┐                                                              │
│  │ 8. Admin │                                                              │
│  └──────────┘                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 4.3. DFD chi tiết — Module Practice (Ví dụ)

```
┌─────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  User   │────▶│ Chọn dạng bài│────▶│ Lấy câu hỏi  │────▶│ Hiển thị câu │
│         │     │              │     │ từ DB        │     │ hỏi + ghi âm │
└─────────┘     └──────────────┘     └──────────────┘     └──────┬───────┘
                                                                  │
                                                                  ▼
┌─────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  User   │◀────│ Hiển thị    │◀────│ Lưu kết quả  │◀────│ Nộp bài /   │
│         │     │ kết quả      │     │ vào DB       │     │ Trả lời     │
└─────────┘     └──────────────┘     └──────────────┘     └──────────────┘
```

---

## 5. THIẾT KẾ CƠ SỞ DỮ LIỆU (ERD)

### 5.1. Sơ đồ ERD tổng quan

```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    users     │       │  questions   │       │   tests      │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id (PK)      │       │ id (PK)      │       │ id (PK)      │
│ name         │       │ type         │       │ user_id (FK) │
│ email        │       │ category_id  │       │ score        │
│ password     │       │ content      │       │ started_at   │
│ role         │       │ options      │       │ completed_at │
│ avatar       │       │ answer       │       │ status       │
│ created_at   │       │ explanation  │       └──────┬───────┘
└──────┬───────┘       │ difficulty   │              │
       │               │ image_url    │              │
       │               └──────┬───────┘              │
       │                      │                      │
       │               ┌──────┴───────┐              │
       │               │  categories  │              │
       │               ├──────────────┤              │
       │               │ id (PK)      │              │
       │               │ name         │              │
       │               │ description  │              │
       │               └──────────────┘              │
       │                                             │
       │               ┌──────────────┐              │
       │               │test_questions│              │
       │               ├──────────────┤              │
       └───────────────│ test_id (FK) │◀─────────────┘
                       │ question_id  │
                       │ user_answer  │
                       │ is_correct  │
                       │ ai_score     │
                       └──────────────┘

┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│   courses    │       │   lessons    │       │  ai_logs     │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id (PK)      │       │ id (PK)      │       │ id (PK)      │
│ title        │       │ course_id(FK)│       │ user_id (FK) │
│ description  │       │ title        │       │ type         │
│ thumbnail    │       │ content      │       │ input        │
│ level        │       │ order        │       │ output       │
│ created_at   │       │ video_url    │       │ created_at   │
└──────────────┘       └──────────────┘       └──────────────┘

┌──────────────┐       ┌──────────────┐
│  bookmarks   │       │  user_progress│
├──────────────┤       ├──────────────┤
│ id (PK)      │       │ id (PK)      │
│ user_id (FK) │       │ user_id (FK) │
│ question_id  │       │ type         │
│ created_at   │       │ total_done   │
└──────────────┘       │ correct_count│
                       │ avg_score    │
                       │ updated_at   │
                       └──────────────┘
```

### 5.2. Mô tả các bảng chính

#### Bảng `users`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID người dùng |
| name | VARCHAR(100) | Họ tên |
| email | VARCHAR(100) | Email (unique) |
| password | VARCHAR(255) | Mật khẩu (hash) |
| role | ENUM('user','admin') | Vai trò |
| avatar | VARCHAR(255) | Ảnh đại diện |
| google_id | VARCHAR(100) | ID Google (nếu đăng nhập GG) |
| created_at | TIMESTAMP | Ngày tạo |

#### Bảng `questions`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID câu hỏi |
| type | VARCHAR(50) | Dạng bài (read_aloud, write_photo...) |
| category_id | INT (FK) | Chủ đề |
| content | TEXT | Nội dung câu hỏi |
| options | JSON | Các lựa chọn (nếu có) |
| answer | TEXT | Đáp án |
| explanation | TEXT | Giải thích |
| difficulty | ENUM('easy','medium','hard') | Độ khó |
| image_url | VARCHAR(255) | Hình ảnh (nếu có) |
| audio_url | VARCHAR(255) | Âm thanh (nếu có) |
| created_at | TIMESTAMP | Ngày tạo |

#### Bảng `categories`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID chủ đề |
| name | VARCHAR(100) | Tên chủ đề |
| description | TEXT | Mô tả |

#### Bảng `tests`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID bài thi |
| user_id | INT (FK) | Người thi |
| score | INT | Điểm số |
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
| is_correct | BOOLEAN | Đúng/Sai |
| ai_score | INT | Điểm AI (nếu có) |
| ai_feedback | TEXT | Phản hồi AI |

#### Bảng `courses`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID khóa học |
| title | VARCHAR(200) | Tiêu đề |
| description | TEXT | Mô tả |
| thumbnail | VARCHAR(255) | Ảnh đại diện |
| level | ENUM('beginner','intermediate','advanced') | Cấp độ |
| created_at | TIMESTAMP | Ngày tạo |

#### Bảng `lessons`
| Field | Type | Mô tả |
|---|---|---|
| id | INT (PK) | ID bài học |
| course_id | INT (FK) | Khóa học |
| title | VARCHAR(200) | Tiêu đề |
| content | TEXT | Nội dung |
| order | INT | Thứ tự |
| video_url | VARCHAR(255) | Video (nếu có) |

#### Bảng `ai_logs`
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

---

## 6. KIẾN TRÚC HỆ THỐNG & CÔNG NGHỆ

### 6.1. Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────────┐
│                        Client (Browser)                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │   Desktop   │  │   Mobile    │  │  Tablet     │         │
│  │   (Chrome)  │  │  (Safari)   │  │  (iPad)     │         │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘         │
└─────────┼────────────────┼────────────────┼─────────────────┘
          │                │                │
          └────────────────┼────────────────┘
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

### 6.2. Công nghệ chi tiết

| Thành phần | Công nghệ | Ghi chú |
|---|---|---|
| **Backend** | PHP 8.2 + Laravel 10 | Framework chính, xử lý toàn bộ logic |
| **Frontend** | Blade + Tailwind CSS + Vanilla JS | Server-side rendering, không cần build step |
| **Database** | MySQL 8.0 | Cơ sở dữ liệu chính |
| **Cache** | File cache / Database cache | Cache session, dữ liệu tạm (không cần Redis) |
| **Queue** | Laravel Queue (database driver) | Xử lý AI scoring bất đồng bộ |
| **File Storage** | Local storage | Lưu ảnh, audio trên server |
| **AI** | OpenAI API (GPT-4o) | Chấm bài, AI Tutor |
| **Auth** | Laravel Sanctum + Google OAuth | Xác thực |
| **Deployment** | XAMPP / Shared hosting / VPS | Chạy trực tiếp PHP, không cần Node.js |

**Lưu ý quan trọng:** Toàn bộ hệ thống chỉ sử dụng PHP + MySQL. Không sử dụng Node.js, React, Vue, hoặc bất kỳ JavaScript framework nào. Frontend sử dụng Blade template + Tailwind CSS + Vanilla JavaScript thuần túy.

### 6.3. Cấu trúc thư mục Laravel

```
det-practice/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── PracticeController.php
│   │   │   ├── MockTestController.php
│   │   │   ├── AICorrectionController.php
│   │   │   ├── CourseController.php
│   │   │   ├── DashboardController.php
│   │   │   └── AdminController.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   └── AuthMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Question.php
│   │   ├── Category.php
│   │   ├── Test.php
│   │   ├── TestQuestion.php
│   │   ├── Course.php
│   │   ├── Lesson.php
│   │   ├── AILog.php
│   │   ├── Bookmark.php
│   │   └── UserProgress.php
│   ├── Services/
│   │   ├── AIService.php
│   │   ├── TestService.php
│   │   └── ScoringService.php
│   └── Jobs/
│       └── ProcessAIScoring.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── practice/
│   │   ├── mock-test/
│   │   ├── ai-correction/
│   │   ├── courses/
│   │   ├── dashboard/
│   │   └── admin/
│   ├── css/
│   └── js/          # Vanilla JavaScript thuần túy, không framework
├── routes/
│   └── web.php
└── .env
```

---

## 7. GIAO DIỆN NGƯỜI DÙNG (UI/UX)

### 7.1. Các trang chính

| Trang | Mô tả | Độ ưu tiên |
|---|---|---|
| Trang chủ (Landing) | Giới thiệu, CTA, tính năng nổi bật | Cao |
| Đăng nhập / Đăng ký | Form đăng nhập, nút Google | Cao |
| Dashboard | Tổng quan tiến độ, biểu đồ | Cao |
| Practice | Chọn dạng bài, làm bài | Cao |
| Mock Test | Màn hình thi, báo cáo kết quả | Cao |
| AI Correction | Nhập bài, xem kết quả AI | Trung bình |
| Courses | Danh sách khóa học, chi tiết | Trung bình |
| AI Tutor | Chat với AI | Trung bình |
| Admin | Quản lý câu hỏi, user | Trung bình |

### 7.2. Wireframe tổng quan

```
┌─────────────────────────────────────────────────────────────┐
│  [Logo]  Dashboard  Practice  Mock Test  Courses  [Avatar]  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │           WELCOME BACK, [Tên]!                      │   │
│  │           Hôm nay bạn đã học 15 câu. Streak: 7 ngày │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  Practice    │  │  Mock Test   │  │  AI Tutor    │     │
│  │  150 câu hỏi │  │  Thi thử     │  │  Hỏi đáp     │     │
│  │  → Bắt đầu   │  │  → Bắt đầu   │  │  → Bắt đầu   │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │           BIỂU ĐỒ TIẾN ĐỘ                          │   │
│  │           [Line chart: điểm theo thời gian]         │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │           CÂU HỎI CỦA HÔM NAY                       │   │
│  │           [5 câu hỏi gợi ý]                         │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. TƯ VẤN XÂY DỰNG NGÂN HÀNG CÂU HỎI

### 8.1. Nguồn câu hỏi

| Nguồn | Ưu điểm | Nhược điểm | Khuyến nghị |
|---|---|---|---|
| **Tự viết** | Độc quyền, chất lượng cao | Tốn thời gian | Ưu tiên cho dạng bài chính |
| **Tài liệu công khai** | Miễn phí, nhiều nguồn | Chất lượng không đồng đều | Tham khảo, chọn lọc |
| **Cộng đồng** | Đa dáng, thực tế | Cần kiểm duyệt | Sau khi có user base |
| **Mua từ nguồn uy tín** | Chất lượng cao | Tốn phí | Nếu có ngân sách |

### 8.2. Cấu trúc câu hỏi DET

Mỗi câu hỏi nên có:
- **Loại bài** (type): read_aloud, write_photo, listen_repeat...
- **Chủ đề** (category): Ngành học, Đời sống, Khoa học, Công nghệ...
- **Nội dung** (content): Câu hỏi, đoạn văn, hình ảnh
- **Lựa chọn** (options): Đáp án A, B, C, D (nếu có)
- **Đáp án** (answer): Đáp án đúng
- **Giải thích** (explanation): Giải thích chi tiết
- **Độ khó** (difficulty): Dễ / Trung bình / Khó
- **Hình ảnh** (image_url): Hình ảnh minh họa
- **Âm thanh** (audio_url): File âm thanh (cho bài nghe)

### 8.3. Kế hoạch xây dựng câu hỏi

| Tuần | Số câu hồi mục tiêu | Dạng bài ưu tiên |
|---|---|---|
| Tuần 1-2 | 50 câu | Read Aloud, Read and Select |
| Tuần 3-4 | 100 câu | Write about the Photo, Listen and Repeat |
| Tuần 5-6 | 150 câu | Read then Write, Read then Speak |
| Tuần 7-8 | 200 câu | Tổng hợp + bổ sung các dạng còn lại |
| **Tổng cộng** | **500+ câu** | Đủ 15 dạng bài |

### 8.4. Quy trình tạo câu hỏi

```
1. Chọn dạng bài → 2. Viết nội dung → 3. Thêm đáp án + giải thích
    → 4. Chụp hình/record audio → 5. Upload hệ thống → 6. Kiểm duyệt → 7. Publish
```

---

## 9. LỊCH TRÌNH DỰ ÁN (8 TUẦN)

### 9.1. Tiến độ chi tiết

| Tuần | Công việc | Người phụ trách | Output |
|---|---|---|---|
| **Tuần 1** | Khảo sát, phân tích yêu cầu, thiết kế DB | Cả nhóm | Tài liệu phân tích, ERD |
| **Tuần 2** | Setup môi trường, auth, layout chính | Thành viên 1 | Hệ thống auth hoàn chỉnh |
| **Tuần 3** | Module Practice (CRUD câu hỏi, làm bài) | Thành viên 1 + 2 | Practice hoạt động |
| **Tuần 4** | Module Mock Test (thi, chấm điểm) | Thành viên 2 | Mock test cơ bản |
| **Tuần 5** | AI Correction (tích hợp OpenAI) | Thành viên 3 | AI chấm bài |
| **Tuần 6** | Courses + AI Tutor | Thành viên 3 + 4 | Khóa học, AI Tutor |
| **Tuần 7** | Dashboard + Admin + Tối ưu | Cả nhóm | Dashboard, trang admin |
| **Tuần 8** | Testing, bug fixing, triển khai | Cả nhóm | Sản phẩm hoàn chỉnh |

### 9.2. Biểu đồ Gantt (Text)

```
Tuần:     1    2    3    4    5    6    7    8
          ├────┼────┼────┼────┼────┼────┼────┼────┤
Phân tích ████
Auth           ████
Practice           ████████
Mock Test               ████████
AI Correction               ████████
Courses + AI Tutor              ████████
Dashboard + Admin                   ████████
Testing + Deploy                        ██████
```

---

## 10. PHÂN CÔNG CÔNG VIỆC

### 10.1. Bảng phân công (4 thành viên)

| Thành viên | Vai trò | Module chính |
|---|---|---|
| **Thành viên 1** (Nhóm trưởng) | Backend + Auth + Practice | Auth, Practice, quản lý nhóm |
| **Thành viên 2** | Backend + Mock Test | Mock Test, Scoring |
| **Thành viên 3** | AI + Courses | AI Correction, AI Tutor, Courses |
| **Thành viên 4** | Frontend + Admin | Dashboard, Admin, UI/UX |

### 10.2. Phân công theo tuần

| Tuần | TV1 | TV2 | TV3 | TV4 |
|---|---|---|---|---|
| 1 | Phân tích, ERD | Phân tích, ERD | Phân tích, ERD | Phân tích, ERD |
| 2 | Auth backend | Auth backend | Layout frontend | Layout frontend |
| 3 | Practice backend | Practice backend | Practice frontend | Practice frontend |
| 4 | Mock Test backend | Mock Test backend | Mock Test frontend | Mock Test frontend |
| 5 | AI backend | AI backend | AI frontend | AI frontend |
| 6 | Courses backend | Courses backend | Courses frontend | Courses frontend |
| 7 | Dashboard backend | Dashboard backend | Admin frontend | Admin frontend |
| 8 | Testing | Testing | Testing | Testing |

---

## 11. DỰ TOÁN

### 11.1. Chi phí phát triển

| Hạng mục | Chi phí (VNĐ) | Ghi chú |
|---|---|---|
| Lương nhóm (4 người × 2 tháng) | 40.000.000 | Ở mức sinh viên |
| Server (VPS) | 2.000.000 | 2 tháng |
| Tên miền | 500.000 | .com |
| OpenAI API | 3.000.000 | Ước tính |
| Thiết bị (máy in, màn hình) | 5.000.000 | Nếu cần |
| Dự phòng | 5.000.000 | |
| **Tổng cộng** | **55.500.000** | |

### 11.2. Chi phí vận hành hàng tháng (sau khi triển khai)

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
| 1 | Phù hợp | Đủ 8 module chính |
| 2 | Chính xác | Tính điểm, thống kê đúng 100% |
| 3 | Tương tác | Phản hồi < 2 giây |
| 4 | Thực hiện đúng | Lu�ng xử lý tuần tự, nhất quán |
| 5 | An toàn | Phân quyền User/Admin |
| 6 | Tin cậy | Uptime > 99% trong giờ học |
| 7 | Khả năng kháng lỗi | Thông báo lỗi rõ ràng, giữ dữ liệu |
| 8 | Dùng được | Giao diện tiếng Việt, dễ hiểu |
| 9 | Dễ học | Nhân viên mới dùng được sau 1 buổi |
| 10 | Dễ dùng | Nút bấm lớn, tối ưu thao tác |
| 11 | Hiệu quả | Tải trang < 3 giây |
| 12 | Bảo trì được | Mã nguồn có chú thích, module hóa |
| 13 | Thay đổi được | Kiến trúc module hóa |
| 14 | Kiểm thử được | Có test case cho từng module |
| 15 | Khả chuyển | Chuẩn mễ, dễ migrate |

### 12.2. Yêu cầu phi chức năng

| STT | Yêu cầu | Mô tả |
|---|---|---|
| 1 | Hiệu năng | Hỗ trợ 100+ user đồng thời |
| 2 | Bảo mật | Mật khẩu hash, HTTPS, chống SQL injection |
| 3 | Tương thích | Chrome, Firefox, Safari, Edge |
| 4 | Responsive | Desktop, tablet, mobile |
| 5 | SEO | Meta tags, sitemap |

---

## 13. RỦI RO & GIẢI PHÁP

| Rủi ro | Xác suất | Ảnh hưởng | Giải pháp |
|---|---|---|---|
| Ngân hàng câu hỏi không đủ | Cao | Cao | Ưu tiên dạng bài chính, tăng tốc tạo câu hỏi |
| OpenAI API tốn phí | Trung bình | Trung bình | Giới hạn số lần gọi, cache kết quả |
| Nhóm thiếu kinh nghiệm Laravel | Trung bình | Cao | Dùng Laravel 10 (tài liệu nhiều), pair programming |
| Tiến độ chậm | Trung bình | Cao | Họp hàng tuần, linh hoạt điều chỉnh phạm vi |
| Xung đột git | Thấp | Trung bình | Quy trình Git flow rõ ràng |

---

## 14. KẾT LUẬN

Dự án xây dựng nền tảng luyện thi DET Practice là khả thi trong 2 tháng với nhóm 4 người, sử dụng PHP (Laravel) + MySQL. Sản phẩm sẽ cung cấp:

- Ngân hàng câu hỏi 500+ (MVP)
- Mock test mô phỏng bài thi thật
- AI chấm bài Speaking/Writing
- Khóa học + AI Tutor
- Dashboard theo dõi tiến độ

Với việc tập trung vào trải nghiệm người Việt Nam và chi phí thấp, đây là sản phẩm có tiềm năng cạnh tranh với detpractice.com trong thị trường Việt Nam.

---

## 15. PHỤ LỤC

### 15.1. Tài liệu tham khảo
- detpractice.com — Đối tủ chính
- Laravel Documentation — https://laravel.com/docs
- Duolingo English Test — https://englishtest.duolingo.com

### 15.2. Công cụ cần chuẩn bị
- [ ] Git + GitHub/GitLab
- [ ] PHP 8.2 + Composer
- [ ] MySQL 8.0 (XAMPP hoặc cài đặt riêng)
- [ ] Postman (test API)
- [ ] Figma (thiết kế UI)
- [ ] Trello/Notion (quản lý công việc)

### 15.3. Checklist trước khi bắt đầu
- [ ] Chốt phạm vi sản phẩm (MVP)
- [ ] Chốt công nghệ (Laravel + MySQL)
- [ ] Chia nhóm, phân công công việc
- [ ] Setup môi trường phát triển
- [ ] Tạo repo Git
- [ ] Thiết kế DB (ERD)
- [ ] Tạo wireframe UI
- [ ] Lập kế hoạch chi tiết từng tuần

---

*Tài liệu này được tạo dựa trên cấu trúc từ 2 file Word: Đề cương dự án và Báo cáo học phần CNPM.*
