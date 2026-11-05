# BÁO CÁO TỔNG KẾT DỰ ÁN

## DET PRACTICE — NỀN TẢNG LUYỆN THI DUOLINGO ENGLISH TEST

---

### Thông tin chung

| Nội dung | Chi tiết |
| --- | --- |
| Tên dự án | DET Practice — Nền tảng luyện thi Duolingo English Test |
| Loại dự án | Đồ án môn học / Dự án phát triển phần mềm nhóm |
| Nhóm thực hiện | Nhóm 3 thành viên |
| Thành viên | Đoàn Phi Phong, Trần Thế Hào, Võ Nhật Lâm |
| Thời gian thực hiện | 20 ngày (tháng 10/2026 — tháng 11/2026) |
| Công nghệ chính | PHP 8.2, Laravel 12, MySQL/MariaDB, Blade, HTML5, CSS |
| Kho mã nguồn | `Phat_Trien_Ma_Nguon_Mo` |
| Môi trường triển khai thử nghiệm | XAMPP (Apache + MySQL/MariaDB) trên Windows |
| Phiên bản báo cáo | 1.0 — tháng 11/2026 |

---

## MỤC LỤC

1. [Lời mở đầu](#lời-mở-đầu)
2. [Chương 1 — Tổng quan về dự án](#chương-1--tổng-quan-về-dự-án)
3. [Chương 2 — Cơ sở lý thuyết và công nghệ sử dụng](#chương-2--cơ-sở-lý-thuyết-và-công-nghệ-sử-dụng)
4. [Chương 3 — Phân tích và thiết kế hệ thống](#chương-3--phân-tích-và-thiết-kế-hệ-thống)
5. [Chương 4 — Triển khai các phân hệ](#chương-4--triển-khai-các-phân-hệ)
6. [Chương 5 — Kiểm thử và đánh giá chất lượng](#chương-5--kiểm-thử-và-đánh-giá-chất-lượng)
7. [Chương 6 — Kết quả, hạn chế và hướng phát triển](#chương-6--kết-quả-hạn-chế-và-hướng-phát-triển)
8. [Chương 7 — Kết luận](#chương-7--kết-luận)
9. [Tài liệu tham khảo](#tài-liệu-tham-khảo)
10. [Phụ lục](#phụ-lục)

---

## LỜI MỞ ĐẦU

Duolingo English Test (DET) là một trong những chứng chỉ tiếng Anh được nhiều trường đại học trên thế giới công nhận, với hình thức thi trực tuyến, thời gian làm bài ngắn và kết quả trả về nhanh. Tuy nhiên, người học tại Việt Nam thường gặp khó khăn trong việc tìm một môi trường luyện tập mô phỏng sát cấu trúc đề thi, có phản hồi tức thì và theo dõi được tiến độ cá nhân.

Xuất phát từ thực tế đó, nhóm chúng em xây dựng **DET Practice** — một nền tảng web hỗ trợ luyện thi DET, cho phép người học luyện tập theo từng dạng bài, làm đề thi thử có tính giờ, nhận chấm điểm tự động hoặc bằng AI, xem thống kê tiến độ và nhận chứng chỉ thi thử. Hệ thống cũng cung cấp phân hệ quản trị để quản lý ngân hàng câu hỏi, đề thi, người dùng và nhật ký AI.

Báo cáo này trình bày toàn bộ quá trình thực hiện dự án: từ việc phân tích yêu cầu, lựa chọn công nghệ, thiết kế cơ sở dữ liệu và kiến trúc, đến triển khai các phân hệ, kiểm thử và đánh giá kết quả. Báo cáo được xây dựng dựa trên mã nguồn thực tế của dự án, các tài liệu thiết kế nội bộ và kết quả chạy kiểm thử tự động.

---

## CHƯƠNG 1 — TỔNG QUAN VỀ DỰ ÁN

### 1.1. Lý do chọn đề tài

Có bốn lý do chính dẫn đến việc lựa chọn đề tài này:

1. **Nhu cầu luyện thi DET ngày càng tăng.** DET được chấp nhận rộng rãi, nhưng số lượng nền tảng luyện thi miễn phí, có cấu trúc rõ ràng và giao diện tiếng Việt còn hạn chế.
2. **Thiếu môi trường luyện tập có phản hồi tức thì.** Người học thường chỉ làm đề giấy hoặc xem video, không được chấm ngay và không biết mình sai ở đâu.
3. **AI tạo ra cơ hội cá nhân hóa.** Việc chấm Writing/Speaking và giải thích ngữ pháp bằng mô hình ngôn ngữ lớn giúp người học nhận phản hồi nhanh với chi phí thấp, phù hợp với mục tiêu của đồ án.
4. **Phù hợp với định hướng kỹ thuật của nhóm.** Dự án cho phép vận dụng đầy đủ kiến thức về phát triển web, cơ sở dữ liệu quan hệ, bảo mật, kiểm thử và tích hợp dịch vụ ngoài.

### 1.2. Mục tiêu của dự án

**Mục tiêu tổng quát:** Xây dựng một nền tảng web hoàn chỉnh giúp người học luyện thi DET theo lộ trình, có phản hồi và theo dõi tiến độ; đồng thời cung cấp công cụ quản trị nội dung cho quản trị viên.

**Mục tiêu cụ thể:**

- Xây dựng hệ thống tài khoản an toàn: đăng ký, đăng nhập, xác minh email, quên mật khẩu, đăng nhập Google.
- Cung cấp ngân hàng câu hỏi đa dạng theo 4 kỹ năng Reading, Listening, Writing, Speaking và 11 dạng bài DET.
- Cho phép luyện tập theo dạng bài, độ khó, chấm tự động, xem đáp án và giải thích.
- Mô phỏng bài thi thử có tính giờ, chọn câu thích ứng đơn giản, tổng hợp điểm và cấp chứng chỉ thử.
- Tích hợp AI để chấm Writing/Speaking, sửa ngữ pháp, dịch và viết lại câu.
- Xây dựng dashboard thống kê tiến độ, chuỗi ngày học và điểm thi.
- Xây dựng phân hệ quản trị: câu hỏi, dạng bài, chủ đề, đề thi, người dùng, nhật ký AI và nhập dữ liệu bằng JSON.
- Đảm bảo chất lượng bằng kiểm thử tự động và quy trình làm việc nhóm trên Git.

### 1.3. Đối tượng và phạm vi

**Đối tượng người dùng:**

| Tác nhân | Mô tả |
| --- | --- |
| Khách | Người chưa đăng nhập, chỉ xem trang giới thiệu, đăng ký, đăng nhập |
| Học viên | Người dùng đã đăng nhập, luyện tập, thi thử, dùng AI Tutor, xem thống kê |
| Quản trị viên | Người dùng có `vaitro = 'admin'`, quản lý nội dung và người dùng |
| Dịch vụ AI | Hệ thống ngoài nhận văn bản và trả về điểm/nhận xét |
| Google OAuth / SMTP | Dịch vụ hỗ trợ đăng nhập và gửi email |

**Phạm vi chức năng:** Toàn bộ vòng đời học tập trên nền tảng, từ đăng ký, luyện tập, thi thử, chấm điểm, xem thống kê đến quản trị nội dung.

**Phạm vi kỹ thuật:**

- Chỉ dùng PHP thuần trên máy chủ (Laravel), không dùng Node.js, Python service, Livewire, Inertia, React, Vue hay Tailwind.
- Giao diện dùng Blade + HTML5 + CSS viết tay, **không dùng JavaScript phía client**. Các tương tác được xử lý bằng form POST/GET, redirect, `<details>`, CSS `:checked` và `<meta http-equiv="refresh">`.
- Cơ sở dữ liệu quan hệ MySQL/MariaDB, không dùng Redis hay MongoDB.
- Không upload media qua giao diện web cho câu hỏi; ảnh/audio là đường dẫn tương đối trong `public/`.

**Phạm vi dữ liệu:** Ngân hàng câu hỏi mẫu gồm **897 câu hỏi**, 11 dạng bài, 6 chủ đề, 8 đề thi thử và hơn 2.700 lựa chọn. Dữ liệu phục vụ mục đích học tập, không phải đề thi chính thức của Duolingo.

### 1.4. Phương pháp thực hiện

Nhóm áp dụng quy trình phát triển phần mềm theo mô hình **chia module — phát triển song song — tích hợp liên tục**:

1. **Khảo sát và đặc tả:** Đọc tài liệu về DET, xác định các dạng bài, thang điểm 10–160 và các yêu cầu chức năng.
2. **Thiết kế:** Thiết kế CSDL quan hệ, kiến trúc MVC, phân quyền và luồng nghiệp vụ.
3. **Phát triển theo module:** Mỗi thành viên phụ trách một nhóm module từ database đến controller, view và test.
4. **Kiểm thử:** Viết feature test với PHPUnit, dùng SQLite in-memory và `AI_PROVIDER=mock` để kiểm thử offline.
5. **Đánh giá và cải tiến:** Chạy toàn bộ test suite, sửa lỗi, chuẩn hóa mã nguồn bằng Laravel Pint, cập nhật tài liệu.

### 1.5. Bố cục báo cáo

Báo cáo gồm 7 chương: Chương 1 giới thiệu tổng quan; Chương 2 trình bày cơ sở lý thuyết và công nghệ; Chương 3 phân tích, thiết kế hệ thống; Chương 4 mô tả triển khai từng phân hệ; Chương 5 trình bày kiểm thử; Chương 6 đánh giá kết quả, hạn chế và hướng phát triển; Chương 7 kết luận. Phần phụ lục cung cấp tài khoản demo, lệnh vận hành và mô tả file JSON kiểm thử.

---

## CHƯƠNG 2 — CƠ SỞ LÝ THUYẾT VÀ CÔNG NGHỆ SỬ DỤNG

### 2.1. Kỳ thi Duolingo English Test (DET)

DET là bài kiểm tra tiếng Anh trực tuyến gồm các phần đánh giá kỹ năng Reading, Listening, Writing và Speaking. Điểm tổng nằm trong thang **10–160**, chia thành các mức năng lực. Bài thi có đặc điểm:

- Thời lượng ngắn, thích ứng theo năng lực người thi.
- Câu hỏi đa dạng: chọn đáp án, điền từ, đọc hiểu tương tác, nghe hiểu tương tác, viết mô tả ảnh, viết luận ngắn, đọc to, nói theo ảnh/chủ đề.
- Kết quả gồm điểm tổng và điểm thành phần.

Dự án mô phỏng các dạng bài này ở mức độ học tập, không sao chép đề thi chính thức.

### 2.2. PHP 8.2 và Laravel 12

**PHP 8.2** là ngôn ngữ phía máy chủ, hỗ trợ kiểu dữ liệu mạnh, thuộc tính readonly, enum và các cải tiến hiệu năng.

**Laravel 12** là framework MVC phổ biến, cung cấp:

- **Routing:** định nghĩa route theo tên, nhóm middleware và prefix.
- **Eloquent ORM:** ánh xạ bảng CSDL thành model, hỗ trợ quan hệ, scope và cast dữ liệu.
- **Blade:** template engine cho phép kế thừa layout, include partial, escape dữ liệu tự động.
- **Migration & Seeder:** quản lý phiên bản CSDL và dữ liệu mẫu.
- **Validation:** kiểm tra dữ liệu đầu vào tập trung.
- **Queue & Job:** xử lý tác vụ nền như chấm bài AI.
- **Testing:** tích hợp PHPUnit, `RefreshDatabase`, `Http::fake`.
- **Middleware:** kiểm soát đăng nhập, xác minh email và quyền admin.
- **Socialite:** đăng nhập OAuth qua Google.
- **Mail:** gửi email xác minh và đặt lại mật khẩu.

Mô hình MVC giúp tách biệt ba thành phần:

| Thành phần | Vai trò trong dự án |
| --- | --- |
| Model | Ánh xạ 26 bảng nghiệp vụ, định nghĩa quan hệ và scope |
| View | Blade templates, layout theo vai trò, CSS thuần |
| Controller | Tiếp nhận request, kiểm tra quyền, gọi service, trả về view/redirect |

Ngoài MVC, dự án còn dùng **Service layer** (`AIService`, `QuestionImportService`, `ScoringService`, `TestService`, `QuestionBankService`) để gom logic nghiệp vụ, giúp controller gọn và dễ kiểm thử.

### 2.3. Blade, HTML5 và CSS thuần

- **Blade** cung cấp cú pháp `@extends`, `@section`, `@include`, `@foreach`, `@if`, `@csrf`, `@method`. Dữ liệu được escape mặc định bằng `{{ }}`, giảm nguy cơ XSS.
- **HTML5** cung cấp form, input type mới (`email`, `number`, `file`, `search`), thẻ `<audio>`, `<details>`, `<table>` ngữ nghĩa.
- **CSS viết tay** nằm trong `public/css/admin.css`, `resources/css/app.css` và các style nội tuyến có kiểm soát. Không dùng framework CSS để đảm bảo yêu cầu môn học.
- **Không JavaScript:** mọi luồng nghiệp vụ dùng POST-redirect-GET; trạng thái chờ dùng meta refresh; biểu đồ dùng SVG/thanh CSS.

### 2.4. MySQL / MariaDB

CSDL quan hệ lưu trữ người dùng, câu hỏi, đề thi, lượt làm bài, điểm, nhật ký AI. Các đặc điểm thiết kế:

- Engine InnoDB, charset `utf8mb4` để hỗ trợ tiếng Việt và emoji.
- Khóa ngoại đảm bảo toàn vẹn tham chiếu.
- Chỉ mục trên các cột tìm kiếm/lọc phổ biến (`email`, `trangthai`, `loai_id`, `nguoidung_id`).
- Ràng buộc `UNIQUE` cho mã câu hỏi, cặp `dethi_id + cauhoi_id`, `dethi_id + thutu`.

### 2.5. Google OAuth và SMTP

- **Laravel Socialite** xử lý luồng OAuth 2.0 với Google: chuyển hướng, nhận callback, lấy thông tin email/tên, liên kết với tài khoản hiện có hoặc tạo mới.
- **SMTP** dùng để gửi email xác minh tài khoản và email đặt lại mật khẩu. Mật khẩu được băm bằng bcrypt, không lưu plaintext.

### 2.6. Trí tuệ nhân tạo và tích hợp API

Hệ thống hỗ trợ ba nhà cung cấp AI:

| Provider | Mục đích | Cấu hình |
| --- | --- | --- |
| `gemini` | Chấm bài, AI Tutor | `GEMINI_API_KEY`, `GEMINI_MODEL` |
| `openai` | Chấm bài, chuyển giọng nói thành văn bản | `OPENAI_API_KEY`, `OPENAI_MODEL`, `OPENAI_STT_MODEL` |
| `mock` | Kiểm thử/offline, không gọi mạng | `AI_PROVIDER=mock` |

**Nguyên tắc an toàn khi dùng AI:**

- Nội dung người học được đặt trong khối dữ liệu riêng của prompt, dặn mô hình không thực thi chỉ dẫn nằm trong bài làm (chống prompt injection).
- Không trả điểm giả khi thiếu API key; lỗi cấu hình được ghi log và trả trạng thái thất bại.
- Mỗi lượt gọi AI tiêu hao một lượt trong hạn mức ngày (`AI_DAILY_LIMIT`, mặc định 20).
- Khi chấm lỗi, lượt được hoàn lại và người dùng có thể chấm lại.
- Điểm AI chỉ mang tính tham khảo, đặc biệt với Speaking.

### 2.7. Git và GitHub

Nhóm sử dụng Git với các nhánh tính năng:

| Nhánh | Phạm vi |
| --- | --- |
| `feature/auth-practice-v2` | Tài khoản và luyện tập |
| `feature/mock-test` | Thi thử và dashboard |
| `Ai` | AI Tutor, chấm điểm AI |
| `test/ngan-hang-cau-hoi` | Mở rộng ngân hàng câu hỏi |
| `fix-giao-dien-admin` | Hoàn thiện giao diện và ràng buộc quản trị |

Quy trình: tạo nhánh từ `main`, commit theo đơn vị chức năng, chạy test trước khi push, tạo pull request và merge sau khi rà soát.

---

## CHƯƠNG 3 — PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG

### 3.1. Yêu cầu chức năng

Hệ thống được chia thành 7 phân hệ chính. Ký hiệu **BB** = bắt buộc (MVP), **MR** = mở rộng.

#### 3.1.1. Phân hệ Tài khoản (TK)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| TK01 | Đăng ký | Họ tên, email, mật khẩu; kiểm tra trùng email; băm bcrypt | BB |
| TK02 | Xác minh email | Gửi liên kết qua SMTP; chưa xác minh thì chặn khu vực học viên | BB |
| TK03 | Đăng nhập | Email + mật khẩu, session; từ chối tài khoản bị khóa | BB |
| TK04 | Đăng nhập Google | Socialite, liên kết `google_id` với tài khoản cũ | BB |
| TK05 | Đăng xuất | Hủy session, tạo lại CSRF token | BB |
| TK06 | Quên/đặt lại mật khẩu | Token có thời hạn, gửi qua email | BB |
| TK07 | Hồ sơ cá nhân | Sửa tên, ảnh đại diện, mục tiêu điểm DET (10–160), đổi mật khẩu | BB |
| TK08 | Phân quyền | Middleware `auth`, `verified`, `admin` | BB |

#### 3.1.2. Phân hệ Luyện tập (LT)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| LT01 | Danh sách dạng bài | Nhóm theo 4 kỹ năng, chỉ hiện dạng đang bật | BB |
| LT02 | Chọn câu luyện | Lọc theo dạng bài, chủ đề, độ khó 1–10; chỉ lấy câu `published` | BB |
| LT03 | Làm bài | Mỗi câu một trang; hiện đề, ảnh, audio; không lộ đáp án trong HTML | BB |
| LT04 | Nộp và chấm tự động | So đáp án phía máy chủ, lưu vào `luyentap` | BB |
| LT05 | Xem đáp án, giải thích | Hiện sau khi nộp | BB |
| LT06 | Gửi bài chấm AI | Dạng Writing/Speaking chuyển sang phân hệ AI | BB |
| LT07 | Đánh dấu câu yếu | Thêm/bỏ đánh dấu; trang câu đã lưu | BB |
| LT08 | Lịch sử luyện tập | Danh sách lượt làm, lọc theo dạng bài, ngày | BB |
| LT09 | Tìm kiếm | Theo từ khóa nội dung câu hỏi | MR |

#### 3.1.3. Phân hệ Thi thử (MT)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| MT01 | Bắt đầu bài thi | Chọn đề `full` hoặc `section`, tạo `lanthi`, chỉ một bài đang làm | BB |
| MT02 | Cấp câu tiếp theo | Chọn câu theo luật thích ứng đơn giản, ghi thời gian cấp câu | BB |
| MT03 | Nộp từng câu | POST câu trả lời, chặn nộp trễ/quá hạn, redirect sang câu kế | BB |
| MT04 | Kiểm soát thời gian | Tính thời gian còn lại từ `batdau`; hết giờ chốt bài | BB |
| MT05 | Kết thúc bài thi | Chấm câu tự động, gọi AI cho câu cần chấm, tính điểm 10–160 | BB |
| MT06 | Báo cáo kết quả | Điểm tổng, điểm thành phần, số câu đúng, đáp án và nhận xét | BB |
| MT07 | Lịch sử thi | Danh sách lần thi, so sánh điểm | BB |
| MT08 | Bỏ dở bài thi | Chuyển trạng thái `abandoned` khi hủy hoặc mở bài mới | BB |
| MT09 | Cấp chứng chỉ thử | Lưu `chungchi` gồm mã, điểm tổng, 4 điểm thành phần, cấp độ | BB |

#### 3.1.4. Phân hệ Chấm bài AI (AI)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| AI01 | Chấm Writing | Gửi đề + bài làm tới API, nhận điểm và góp ý | BB |
| AI02 | Chấm Speaking | Tải audio, chuyển thành văn bản, chấm nội dung và trôi chảy | MR |
| AI03 | Giới hạn lượt | Đếm theo ngày trong `aisudung`, vượt hạn mức thì từ chối | BB |
| AI04 | Ghi nhật ký | Lưu đầu vào, đầu ra, model, token vào `ailog` | BB |
| AI05 | Trạng thái chấm | `pending`, `done`, `failed`; cho phép chấm lại khi lỗi | BB |
| AI06 | Bảo vệ prompt | Đặt bài làm trong khối dữ liệu riêng, không thực thi chỉ dẫn | BB |

#### 3.1.5. Phân hệ AI Tutor (TU)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| TU01 | Sửa ngữ pháp | Nhận đoạn văn, trả bản sửa, danh sách lỗi và nhận xét | MR |
| TU02 | Dịch thuật | Dịch Anh–Việt hoặc Việt–Anh kèm từ vựng, ghi chú ngữ pháp | MR |
| TU03 | Viết lại | Viết lại theo phong cách học thuật/tự nhiên/DET 120+ | MR |
| TU04 | Lịch sử tương tác | Đọc lại từ `ailog`, lọc theo loại | MR |

#### 3.1.6. Phân hệ Dashboard (DB)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| DB01 | Tổng quan học tập | Số câu đã làm, tỉ lệ đúng, thời gian luyện | BB |
| DB02 | Tiến độ theo dạng bài | Từ `tiendo`, hiển thị thanh CSS | BB |
| DB03 | Biểu đồ điểm thi | Đường SVG từ `lanthi` | BB |
| DB04 | Chuỗi ngày học | Từ `chuoingay` | BB |
| DB05 | Gợi ý bài hôm nay | Chọn dạng có tỉ lệ đúng thấp nhất | MR |

#### 3.1.7. Phân hệ Quản trị (AD)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| AD01 | Quản lý câu hỏi | Xem, lọc, duyệt trạng thái, xóa có kiểm soát | BB |
| AD02 | Quản lý dạng bài | Bật/tắt, đặt cách chấm, giới hạn thời gian | BB |
| AD03 | Quản lý chủ đề | Thêm, sửa, xóa chủ đề | BB |
| AD04 | Quản lý người dùng | Tìm, khóa/mở, đổi vai trò; bảo vệ admin cuối cùng | BB |
| AD05 | Thống kê hệ thống | Số người dùng, câu hỏi, đề thi, lượt thi, lượt gọi AI | BB |
| AD06 | Nhật ký AI | Lọc theo loại, trạng thái, người dùng, ngày | MR |
| AD07 | Quản lý đề thi | Danh sách, bộ lọc, chi tiết cấu trúc đề | BB |
| AD08 | Nhập câu hỏi bằng JSON | Xem trước, kiểm tra, ghi CSDL trong transaction | MR |

#### 3.1.8. Các dạng bài DET được hỗ trợ

| Kỹ năng | Dạng bài | Cách chấm |
| --- | --- | --- |
| Reading | Read and Select | Tự động |
| Reading | Fill in the Blanks | Tự động |
| Reading | Read and Complete | Tự động |
| Reading | Interactive Reading | Tự động (câu con) |
| Listening | Listen and Type | Tự động |
| Listening | Interactive Listening | Tự động/AI (câu con) |
| Writing | Write About the Photo | AI |
| Writing | Write About Topic | AI |
| Speaking | Read Aloud | AI |
| Speaking | Speak About the Photo | AI |
| Speaking | Read Then Speak | AI |

### 3.2. Yêu cầu phi chức năng

| Mã | Nhóm | Yêu cầu |
| --- | --- | --- |
| NF01 | Không JavaScript | Không có thẻ `<script>`, thuộc tính sự kiện hay thư viện front-end |
| NF02 | Bảo mật | Băm mật khẩu, CSRF, escape output, kiểm tra quyền sở hữu, chống prompt injection |
| NF03 | Hiệu năng | Phân trang, chỉ mục CSDL, eager loading, giới hạn thời gian gọi AI |
| NF04 | Toàn vẹn dữ liệu | Transaction khi import/chấm, khóa ngoại, ràng buộc duy nhất |
| NF05 | Khả dụng | Giao diện tiếng Việt, phản hồi rõ ràng, hỗ trợ bàn phím, màu sắc tương phản |
| NF06 | Khả năng bảo trì | Tách service, đặt tên nhất quán, chú thích, kiểm thử tự động |
| NF07 | Cấu hình | Thông tin nhạy cảm đặt trong `.env`, không commit |
| NF08 | Tương thích | Chạy trên XAMPP/Apache, MySQL/MariaDB, PHP 8.2+ |

### 3.3. Tác nhân và ma trận phân quyền

| Chức năng | Khách | Học viên | Quản trị viên |
| --- | --- | --- | --- |
| Xem trang chủ, đăng ký, đăng nhập | Có | Có | Có |
| Cập nhật hồ sơ, đổi mật khẩu | Không | Có | Có |
| Luyện tập theo dạng bài | Không | Có | Có |
| Đánh dấu câu yếu | Không | Có | Có |
| Thi thử, xem lịch sử thi | Không | Có | Có |
| Dùng AI Tutor, chấm AI | Không | Có | Có |
| Xem dashboard cá nhân | Không | Có | Có |
| Quản lý câu hỏi, dạng bài, chủ đề | Không | Không | Có |
| Duyệt câu hỏi, nhập JSON | Không | Không | Có |
| Quản lý đề thi | Không | Không | Có |
| Khóa/mở tài khoản, đổi vai trò | Không | Không | Có |
| Xem thống kê hệ thống, nhật ký AI | Không | Không | Có |

**Quy tắc kiểm soát:**

- Middleware `auth` chặn người chưa đăng nhập.
- Middleware `admin` chặn mọi route `/admin/*` nếu `vaitro != 'admin'`.
- Tài khoản `trangthai = 'locked'` bị đăng xuất ở request kế tiếp.
- Admin không thể tự khóa tài khoản mình.
- Admin chỉ được tự hạ quyền khi còn **ít nhất một quản trị viên khác đang hoạt động**. Đây là ràng buộc được bổ sung trong đợt sửa giao diện admin.

### 3.4. Use case tổng quát

**Học viên:**

1. Đăng ký → xác minh email → đăng nhập.
2. Chọn kỹ năng → chọn dạng bài/độ khó → làm bài → nhận kết quả.
3. Thi thử → làm từng câu → hết giờ/nộp bài → xem báo cáo → nhận chứng chỉ thử.
4. Dùng AI Tutor (sửa ngữ pháp, dịch, viết lại) → xem lịch sử.
5. Xem dashboard tiến độ, chuỗi ngày học, điểm thi.

**Quản trị viên:**

1. Đăng nhập bằng tài khoản admin.
2. Xem thống kê tổng quan.
3. Duyệt câu hỏi theo trạng thái: bản nháp → chờ duyệt → xuất bản → lưu trữ.
4. Quản lý dạng bài, chủ đề.
5. Xem danh sách và chi tiết đề thi.
6. Quản lý người dùng, phân quyền.
7. Nhập câu hỏi/đề thi từ file JSON.
8. Xem nhật ký AI để kiểm tra lỗi và chi phí.

### 3.5. Kiến trúc hệ thống

Hệ thống theo kiến trúc **Laravel MVC mở rộng với Service layer và Job queue**:

```
Trình duyệt (HTML/CSS, form POST/GET)
        │
        ▼
Routes (web.php, admin.php)
        │
        ▼
Middleware (auth, verified, admin, throttle)
        │
        ▼
Controllers (Auth, Practice, MockTest, Tutor, Admin, Certificate)
        │
        ├── Services (AIService, ScoringService, QuestionImportService, ...)
        │        │
        │        └── HTTP Client → Gemini / OpenAI
        │
        ├── Jobs (ProcessAIGradingJob) → Queue
        │
        ▼
Models (Eloquent) → MySQL/MariaDB
        │
        ▼
Views (Blade) → HTML/CSS
```

**Cấu trúc thư mục chính:**

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Câu hỏi, đề thi, người dùng, nhật ký AI
│   │   ├── Auth/           # Đăng nhập, đăng ký, Google, mật khẩu
│   │   ├── MockTest/       # Thi thử, kết quả, lịch sử, dashboard
│   │   ├── Practice/       # Luyện tập và luyện AI
│   │   ├── Tutor/          # AI Tutor
│   │   └── Certificate/    # Chứng chỉ thử
│   ├── Middleware/         # Admin, Verified
│   └── Requests/           # Form request (nếu có)
├── Jobs/                   # ProcessAIGradingJob
├── Models/                 # 25+ model Eloquent
├── Services/               # AI, chấm điểm, import, ngân hàng câu hỏi
config/ai.php               # Cấu hình AI
database/
├── migrations/             # 33 migration
└── seeders/                # Dữ liệu mẫu
resources/views/            # Blade templates
public/css/                 # CSS viết tay
routes/web.php, admin.php   # Định nghĩa route
tests/Feature/              # 30+ file feature test
```

### 3.6. Thiết kế cơ sở dữ liệu

#### 3.6.1. Tổng quan

CSDL gồm **26 bảng nghiệp vụ** (thiết kế gốc 24 bảng, bổ sung `import_batches` và `vipham`) cùng các bảng hạ tầng của Laravel (`migrations`, `jobs`, `failed_jobs`, `password_reset_tokens`).

| Nhóm | Bảng | Mục đích |
| --- | --- | --- |
| Người dùng | `nguoidung` | Tài khoản, vai trò, trạng thái, hồ sơ |
| Danh mục | `danhmuc` | Chủ đề câu hỏi |
| Dạng bài | `loaicauhoi` | Mã dạng bài, kỹ năng, cách chấm, thời gian mặc định |
| Câu hỏi | `cauhoi` | Câu hỏi cha, độ khó, trạng thái, media |
| | `luachon` | Lựa chọn của câu hỏi cha |
| | `dap_an` | Đáp án tự luận/điền từ |
| | `cauhoi_doc` | Câu hỏi con của Interactive Reading |
| | `luachon_doc` | Lựa chọn câu con đọc |
| | `cauhoi_nghe` | Câu hỏi con của Interactive Listening |
| | `luachon_nghe` | Lựa chọn câu con nghe |
| Đề thi | `dethi` | Đề thi, chế độ, kiểu, thời gian, trạng thái |
| | `dethicauhoi` | Liên kết đề–câu hỏi, thứ tự, điểm |
| Lượt thi | `lanthi` | Lần thi, thời gian, trạng thái, điểm |
| | `lanthicauhoi` | Câu hỏi trong lần thi, thời gian cấp/trả lời |
| | `traloi` | Câu trả lời của người dùng |
| | `chamdiem` | Điểm và nhận xét từng câu |
| Luyện tập | `luyentap` | Lượt luyện tập và kết quả |
| | `tiendo` | Tiến độ theo dạng bài |
| | `danhdau` | Câu hỏi đã đánh dấu |
| | `danhdau_loai` | Dạng bài quan tâm |
| | `chuoingay` | Chuỗi ngày học |
| Chứng chỉ | `chungchi` | Chứng chỉ thi thử |
| AI | `ailog` | Nhật ký từng lượt gọi AI |
| | `aisudung` | Hạn mức lượt AI theo ngày |
| Nhập dữ liệu | `import_batches` | Lịch sử nhập JSON |
| Kiểm soát | `vipham` | Ghi nhận vi phạm (nếu có) |

#### 3.6.2. Quan hệ chính

- `nguoidung` 1–N `luyentap`, `lanthi`, `danhdau`, `chungchi`, `ailog`, `aisudung`.
- `loaicauhoi` 1–N `cauhoi`.
- `danhmuc` 1–N `cauhoi`.
- `cauhoi` 1–N `luachon`, `cauhoi_doc`, `cauhoi_nghe`, `dap_an`.
- `cauhoi_doc` 1–N `luachon_doc`; `cauhoi_nghe` 1–N `luachon_nghe`.
- `dethi` N–N `cauhoi` qua `dethicauhoi` (kèm `thutu`, `diem`).
- `lanthi` 1–N `lanthicauhoi`, `traloi`, `chamdiem`.
- `chungchi` thuộc về một `nguoidung` và một `lanthi`.

#### 3.6.3. Quy ước thiết kế

- Tên bảng/cột dùng tiếng Việt không dấu, nhất quán với tài liệu thiết kế.
- Khóa chính là `bigint unsigned auto_increment`.
- Cột thời gian: `ngaytao`, `ngaycapnhat`, `batdau`, `ketthuc`.
- Trạng thái câu hỏi: `draft`, `review`, `published`, `archived`.
- Trạng thái đề: `draft`, `published`, `hidden`.
- Trạng thái lần thi: `in_progress`, `completed`, `abandoned`.
- Trạng thái chấm AI: `pending`, `done`, `failed`.

### 3.7. Quy trình nghiệp vụ chính

#### 3.7.1. Luyện tập

1. Người dùng chọn kỹ năng/dạng bài/độ khó.
2. Hệ thống lấy câu `published`, ưu tiên câu chưa làm.
3. Hiển thị câu hỏi, ảnh/audio nếu có.
4. Người dùng nộp bài; server chấm tự động hoặc chuyển AI.
5. Lưu kết quả vào `luyentap`, cập nhật `tiendo`, `chuoingay`.
6. Hiển thị đáp án, giải thích và gợi ý câu tiếp theo.

#### 3.7.2. Thi thử

1. Người dùng chọn đề, hệ thống tạo `lanthi` với `batdau` và thời gian.
2. Server cấp từng câu, ghi `thoigian_cap`.
3. Người dùng trả lời; server kiểm tra hạn và ghi `traloi`.
4. Khi hết câu hoặc hết giờ, hệ thống chấm điểm, gọi AI cho câu cần thiết.
5. Tính điểm tổng theo thang 10–160 và điểm thành phần.
6. Hiển thị báo cáo, cho phép cấp chứng chỉ thử.

#### 3.7.3. Chấm AI

1. Kiểm tra hạn mức ngày trong `aisudung`.
2. Trừ lượt trước khi gọi API (transaction ngắn).
3. Gọi API ngoài transaction với timeout cấu hình.
4. Phân tích JSON trả về; nếu sai cấu trúc thì ghi log thất bại.
5. Lưu `ailog` và kết quả chấm.
6. Nếu lỗi, hoàn lượt và cho phép chấm lại.

#### 3.7.4. Nhập câu hỏi bằng JSON

1. Admin tải file JSON lên.
2. `QuestionImportService::prepare()` kiểm tra cấu trúc, mã dạng bài, đáp án, media.
3. Hệ thống hiển thị bản xem trước: số câu, số lựa chọn, câu con, đề thi, cảnh báo.
4. Admin xác nhận; service ghi dữ liệu trong transaction.
5. Ghi `import_batches` để lưu lịch sử.
6. `ma` là khóa ổn định: nhập lại cùng mã sẽ cập nhật thay vì tạo trùng.

### 3.8. Thiết kế giao diện

- **Layout theo vai trò:** khách, học viên, quản trị viên có thanh điều hướng khác nhau.
- **Admin layout:** sidebar gồm Tổng quan, Nội dung (Ngân hàng câu hỏi, Dạng bài, Chủ đề, Đề thi), Hệ thống (Người dùng, Nhật ký AI).
- **Trang công khai:** giới thiệu DET, tính năng, nút bắt đầu.
- **Trang học viên:** luyện tập, thi thử, kết quả, dashboard, AI Tutor.
- **Nguyên tắc trực quan:** màu xanh chủ đạo, thẻ nội dung, bảng dữ liệu, nhãn trạng thái, phản hồi thành công/lỗi rõ ràng.
- **Không JavaScript:** trạng thái chờ dùng thông báo và meta refresh; form luôn có nút submit rõ ràng.

---

## CHƯƠNG 4 — TRIỂN KHAI CÁC PHÂN HỆ

### 4.1. Môi trường và cấu hình

| Thành phần | Giá trị sử dụng |
| --- | --- |
| Hệ điều hành | Windows 10/11 |
| Web server | Apache (XAMPP) |
| PHP | 8.2+ |
| Framework | Laravel 12 |
| CSDL phát triển | MySQL/MariaDB 10.4+ (`duoling_csdl_remake`) |
| CSDL kiểm thử | SQLite in-memory |
| Queue | `sync` khi test, database khi chạy thật |
| AI | `mock` khi test/offline; Gemini hoặc OpenAI khi có key |

Các biến môi trường quan trọng:

```dotenv
APP_ENV=local
APP_URL=http://localhost/Phat_Trien_Ma_Nguon_Mo/public

DB_CONNECTION=mysql
DB_DATABASE=duoling_csdl_remake
DB_USERNAME=root
DB_PASSWORD=

AI_PROVIDER=mock
GEMINI_API_KEY=
OPENAI_API_KEY=
AI_DAILY_LIMIT=20

MAIL_MAILER=smtp
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

### 4.2. Phân hệ Tài khoản

**Đăng ký:** `RegisterController` kiểm tra họ tên, email duy nhất, mật khẩu đủ mạnh; tạo `nguoidung` với `vaitro = 'user'`, `trangthai = 'active'`, mật khẩu băm bcrypt. Sau đăng ký, hệ thống gửi email xác minh.

**Đăng nhập:** `LoginController` xác thực email/mật khẩu, kiểm tra trạng thái tài khoản, tạo session và tái sinh CSRF token. Route đăng nhập có `throttle:6,1` chống brute force.

**Google OAuth:** `GoogleController` dùng Socialite; nếu email đã tồn tại thì liên kết `google_id`, nếu chưa thì tạo tài khoản mới.

**Quên mật khẩu:** `ForgotPasswordController` và `ResetPasswordController` dùng bảng `password_reset_tokens`, gửi liên kết có thời hạn.

**Hồ sơ:** `ProfileController` cho phép sửa tên, ảnh đại diện, mục tiêu điểm DET và đổi mật khẩu; ảnh được lưu trong `storage/app/public`.

### 4.3. Phân hệ Luyện tập

**Danh sách dạng bài:** `PracticeController@index` lấy các `loaicauhoi` đang bật, nhóm theo 4 kỹ năng, hiển thị số câu và tiến độ.

**Chọn câu:** `select()` lọc theo dạng bài, chủ đề, độ khó; chỉ lấy câu `published`; ưu tiên câu chưa làm.

**Làm bài:** mỗi câu một trang; với câu có ảnh/audio, hiển thị `<img>` hoặc `<audio controls>`. Đáp án đúng không được đưa vào HTML.

**Chấm tự động:** `ScoringService` so sánh đáp án đã chọn với `luachon.ladung = true` hoặc `dap_an`. Kết quả lưu vào `luyentap`; `tiendo` và `chuoingay` được cập nhật.

**Câu hỏi tương tác:** Interactive Reading hiển thị đoạn văn và các câu con; Interactive Listening hiển thị audio và câu con. Mỗi câu con có thể có lựa chọn hoặc đáp án điền từ.

**Đánh dấu:** `BookmarkController` bật/tắt `danhdau` và `danhdau_loai`; trang câu đã lưu hỗ trợ luyện lại.

### 4.4. Phân hệ Thi thử

**Danh sách đề:** `MockTestController@index` lấy các đề `published`, hiển thị kỹ năng, thời gian, số câu.

**Bắt đầu:** `start()` tạo `lanthi` với `batdau`, `thoigian`, trạng thái `in_progress`; nếu đã có bài đang làm thì tiếp tục hoặc hủy.

**Cấp câu:** `capCauHoi()` chọn câu theo thứ tự và luật thích ứng đơn giản (dựa trên độ khó và kết quả các câu trước), ghi `thoigian_cap` để kiểm soát thời gian từng câu.

**Nộp câu:** `submit()` kiểm tra câu thuộc bài thi, chưa trả lời, còn thời gian; lưu `traloi`.

**Kết thúc:** `finish()` chấm câu tự động, gọi AI cho câu Writing/Speaking qua job, tổng hợp điểm và cập nhật `lanthi`.

**Báo cáo:** `MockTestResultController@show` hiển thị điểm tổng, điểm thành phần, số câu đúng theo dạng, đáp án và nhận xét.

**Lịch sử:** `MockTestHistoryController@index` liệt kê các lần thi, cho phép xem lại kết quả.

**Chứng chỉ:** `CertificateController@issue` tạo mã chứng chỉ duy nhất, lưu điểm và cấp độ; `show` hiển thị chứng chỉ.

### 4.5. Phân hệ Dashboard

`MockTestDashboardController@index` tổng hợp:

- Tổng số câu đã luyện, tỉ lệ đúng, thời gian học.
- Tiến độ từng dạng bài từ `tiendo`.
- Điểm các lần thi từ `lanthi`.
- Chuỗi ngày học từ `chuoingay`.
- Gợi ý dạng bài cần cải thiện.

Biểu đồ được vẽ bằng SVG hoặc thanh CSS, không dùng thư viện JavaScript.

### 4.6. Phân hệ Quản trị

#### 4.6.1. Tổng quan

`DashboardController@index` đếm số người dùng, câu hỏi theo trạng thái, đề thi, lượt thi, nhật ký AI trong ngày. Dùng query builder để giảm phụ thuộc giữa các module.

#### 4.6.2. Ngân hàng câu hỏi

`QuestionBankController` hỗ trợ:

- Tìm kiếm theo mã/nội dung/giải thích.
- Lọc theo dạng bài, kỹ năng, trạng thái, độ khó.
- Xem chi tiết câu hỏi và đáp án.
- Duyệt trạng thái theo quy trình: `draft → review → published → archived`.
- Chặn nhảy cóc trạng thái và chặn xuất bản khi thiếu đáp án đúng.
- Duyệt hàng loạt, bỏ qua câu không hợp lệ và báo lý do.
- Xóa câu nháp chưa nằm trong đề; không xóa câu đã xuất bản.

#### 4.6.3. Dạng bài

`QuestionTypeController` cho phép bật/tắt, sửa tên hiển thị, cách chấm (`auto`, `ai`, `manual`) và thời gian mặc định. Mã dạng bài không được sửa vì là khóa tham chiếu.

#### 4.6.4. Chủ đề

`CategoryController` cung cấp CRUD chủ đề; chặn xóa chủ đề đang được câu hỏi sử dụng.

#### 4.6.5. Đề thi

`ExamController` (bổ sung trong đợt sửa giao diện admin) cung cấp:

- Danh sách đề thi có tìm kiếm, lọc theo kỹ năng và trạng thái.
- Thống kê nhanh số đề theo trạng thái.
- Trang chi tiết: thông tin đề, người tạo, thời gian, tổng điểm, danh sách câu hỏi theo thứ tự.
- Liên kết tới chi tiết câu hỏi trong ngân hàng.

Mục **Đề thi** trên sidebar admin đã được bật (trước đây hiển thị "Sắp có"), giúp quản trị viên truy cập trực tiếp chức năng đã hoàn thiện.

#### 4.6.6. Người dùng và bảo vệ admin cuối cùng

`UserController` hỗ trợ tìm kiếm, lọc vai trò/trạng thái, khóa/mở tài khoản và đổi vai trò. Các ràng buộc:

- Không thể tự khóa tài khoản của mình.
- Không thể tự hạ quyền nếu mình là **quản trị viên cuối cùng đang hoạt động**.
- Nếu còn ít nhất một admin khác đang `active`, admin được phép tự chuyển mình thành học viên.
- Admin bị khóa không được tính là "admin khác còn hoạt động", tránh trường hợp hệ thống không còn ai quản trị.

#### 4.6.7. Nhật ký AI

`AiLogController` hiển thị danh sách lượt gọi AI, lọc theo loại, trạng thái, người dùng, ngày. Trang chi tiết hiển thị đầu vào, đầu ra, model, token và lỗi (nếu có).

#### 4.6.8. Nhập câu hỏi từ JSON

`QuestionImportController` và `QuestionImportService`:

- Tải file tối đa 10 MB.
- Kiểm tra JSON hợp lệ, mảng `questions` không rỗng.
- Kiểm tra `loai` tồn tại trong `loaicauhoi`.
- Kiểm tra câu trắc nghiệm có ít nhất 2 lựa chọn và đúng 1 đáp án đúng.
- Kiểm tra câu con đọc/nghe và thứ tự không trùng.
- Kiểm tra đường dẫn media tương đối trong `public/`, không cho `..`, URL tuyệt đối hay ổ đĩa.
- Hiển thị cảnh báo nếu danh mục mới hoặc thiếu file media.
- Ghi dữ liệu trong transaction, ghi lịch sử vào `import_batches`.
- Giữ `ma` ổn định để nhập lại không tạo trùng.

### 4.7. Phân hệ AI Tutor và chấm điểm AI

#### 4.7.1. AI Tutor

`TutorController` cung cấp bốn chức năng:

| Chức năng | Đầu vào | Đầu ra |
| --- | --- | --- |
| Sửa ngữ pháp | Đoạn văn | Bản sửa, số lỗi, danh sách lỗi, nhận xét |
| Dịch thuật | Văn bản + chiều dịch | Bản dịch, từ vựng, ghi chú ngữ pháp |
| Viết lại | Văn bản + phong cách | Bản viết lại, điểm cải thiện, giải thích |
| Lịch sử | Bộ lọc loại | Danh sách lượt tương tác |

Mỗi lượt là một form POST theo mô hình PRG (Post–Redirect–Get). Kết quả lưu trong `ailog` với `daura` là JSON.

**Cải tiến hiển thị kết quả:** trước đây trang lịch sử hiển thị nguyên chuỗi JSON, gây khó đọc. Nay view `tutor/partials/ai-result.blade.php` giải mã JSON và render theo từng loại:

- Grammar: bản sửa, số lỗi, từng lỗi (gốc → thay thế, giải thích), nhận xét tổng quan.
- Translate: bản dịch, bảng từ vựng, ghi chú ngữ pháp.
- Rewrite: bản viết lại, các điểm cải thiện, giải thích.
- Writing/Speaking: điểm lớn trên thang 160, nhận xét, điểm thành phần có nhãn tiếng Việt.
- Nếu dữ liệu không phải JSON, hiển thị văn bản thường như cũ.

#### 4.7.2. Chấm Writing/Speaking

- Writing: gửi đề và bài làm, nhận điểm 10–160, nhận xét và 4 điểm thành phần (ngữ pháp, từ vựng, mạch lạc, hoàn thành yêu cầu).
- Speaking: tải audio lên, chuyển thành văn bản bằng Whisper (nếu dùng OpenAI), chấm nội dung, độ trôi chảy, từ vựng, ngữ pháp, liên quan chủ đề.
- Audio Speaking lưu trong `storage/app/private/audio`; phát qua route kiểm tra quyền sở hữu, không để lộ file công khai.
- Chấm bất đồng bộ bằng `ProcessAIGradingJob`; trạng thái hiển thị bằng meta refresh.
- Lỗi tạm thời được phép chấm lại; lỗi cấu hình không trả điểm giả.

### 4.8. Bảo mật

| Nguy cơ | Biện pháp |
| --- | --- |
| XSS | Blade escape mặc định, không render HTML từ AI |
| CSRF | `@csrf` trên mọi form, token tái sinh khi đăng nhập/xuất |
| SQL Injection | Eloquent/query builder với binding |
| Brute force | `throttle` cho đăng nhập và xác minh email |
| Leo thang quyền | Middleware `admin`, kiểm tra `vaitro` ở server |
| Lộ đáp án | Không đưa đáp án đúng vào HTML trước khi nộp |
| Lộ audio riêng tư | Lưu ngoài `public`, route kiểm tra chủ sở hữu |
| Prompt injection | Đặt bài làm trong khối dữ liệu, dặn AI không thực thi chỉ dẫn |
| Lộ bí mật | `.env` không commit, `.env.example` chỉ chứa khóa rỗng |
| Mất quyền quản trị | Chặn tự khóa và tự hạ quyền khi là admin cuối cùng |

### 4.9. Ghi chú đợt sửa nhánh `fix-giao-dien-admin`

Trong đợt hoàn thiện giao diện và ràng buộc quản trị, nhóm đã thực hiện:

1. **Bật mục Đề thi trên sidebar admin**, thêm `Admin\ExamController`, route `admin.exams.index` / `admin.exams.show` và hai view danh sách/chi tiết.
2. **Thêm ràng buộc admin cuối cùng** trong `UserController::updateRole()`: chỉ cho tự hạ quyền khi còn admin khác đang hoạt động.
3. **Đưa admin lên đầu khi khởi tạo CSDL:** đổi thứ tự `DatabaseSeeder` để `AdminNguoidungSeeder` chạy trước `NguoidungSeeder`; đồng thời hoán đổi id tài khoản trong CSDL hiện tại (kèm cập nhật khóa ngoại ở tất cả bảng liên quan) để `admin@example.com` là id 1, `lam@example.com` là id 2.
4. **Bổ sung file JSON kiểm thử import** tại `public/downloads/import-cau-hoi-mau.json`, gồm 8 dạng bài, 28 lựa chọn, 3 câu con đọc, 3 câu con nghe và 1 đề thi mẫu; đã kiểm tra qua `QuestionImportService::prepare()` không có lỗi/cảnh báo.
5. **Cải thiện hiển thị kết quả AI Tutor**, thay raw JSON bằng giao diện thân thiện theo từng loại kết quả.

---

## CHƯƠNG 5 — KIỂM THỬ VÀ ĐÁNH GIÁ CHẤT LƯỢNG

### 5.1. Chiến lược kiểm thử

Dự án sử dụng **feature test** của Laravel/PHPUnit, tập trung vào hành vi từ góc nhìn người dùng:

- **RefreshDatabase:** mỗi test chạy trên CSDL sạch (SQLite in-memory).
- **Http::fake:** giả lập API AI, không gọi mạng thật.
- **AI_PROVIDER=mock:** kiểm thử offline, không cần API key.
- **Queue sync:** job chấm AI chạy ngay trong test.
- **Kiểm tra cả trường hợp thành công và thất bại:** dữ liệu thiếu, sai quyền, sai trạng thái, vượt hạn mức.

### 5.2. Phạm vi kiểm thử

| Nhóm | Nội dung chính |
| --- | --- |
| Auth | Đăng ký, đăng nhập, xác minh email, quên mật khẩu, Google OAuth |
| Profile | Cập nhật hồ sơ, đổi mật khẩu, ảnh đại diện |
| Practice | Danh sách dạng bài, lọc, làm bài, chấm tự động, đánh dấu, lịch sử |
| Mock Test | Bắt đầu, cấp câu, nộp câu, hết giờ, kết quả, lịch sử, chứng chỉ |
| Dashboard | Thống kê, tiến độ, chuỗi ngày học |
| Admin | Câu hỏi, dạng bài, chủ đề, đề thi, người dùng, nhật ký AI |
| Import | Xem trước, validate, import, cập nhật theo mã, tải template |
| AI | Chấm Writing/Speaking, hạn mức, hoàn lượt, retry, log |
| AI Tutor | Grammar, translate, rewrite, lịch sử, hiển thị JSON thân thiện |
| Bảo mật | Chặn truy cập trái quyền, chặn tự khóa/tự hạ quyền admin cuối |

### 5.3. Kết quả kiểm thử

Tại thời điểm hoàn thiện báo cáo, toàn bộ test suite chạy thành công:

```
Tests:    1 skipped, 177 passed (751 assertions)
Duration: ~10–45 giây tùy máy
```

Một số test tiêu biểu:

- `AdminExamTest`: admin xem danh sách/chi tiết đề, học viên bị chặn.
- `AdminUserTest`: admin cuối cùng không tự hạ quyền; admin tự hạ quyền được khi còn admin khác; admin khác bị khóa thì không được tính.
- `AITutorTest`: lịch sử hiển thị JSON theo nhãn thân thiện, không lộ khóa JSON thô.
- `QuestionImportTest`: preview/import JSON, giữ mã ổn định, tải template.
- `PracticeTest`, `PracticeScoringTest`: chấm điểm và luồng luyện tập.
- `FullMockTestSeederTest`, `OperationalQuestionBankSeederTest`, `ExtendedQuestionBankTest`: dữ liệu mẫu và ngân hàng câu hỏi.

### 5.4. Lỗi đã phát hiện và khắc phục

| Lỗi | Cách khắc phục |
| --- | --- |
| Lịch sử AI Tutor hiển thị raw JSON | Tạo partial render theo loại kết quả |
| Admin có thể tự hạ quyền dù là admin duy nhất | Thêm kiểm tra số admin hoạt động còn lại |
| Tài khoản Lâm được tạo trước admin | Đổi thứ tự seeder; hoán đổi id trong CSDL hiện tại |
| Mục Đề thi bị khóa "Sắp có" | Thêm controller/view/route và bật liên kết |
| Migration trùng ở nhánh AI | Xóa migration trùng, thêm migration cho phép `dethi_id` nullable |
| Test dùng camelCase sai | Sửa tên phương thức test theo chuẩn |
| File audio cũ không được dọn | Sửa `CleanOldAudioFilesTest` và logic dọn file |

### 5.5. Hạn chế của kiểm thử

- Chưa có test end-to-end tự động trên trình duyệt thật (do yêu cầu không dùng JavaScript, nhóm kiểm thử thủ công các luồng giao diện).
- Chưa đo độ phủ dòng lệnh (code coverage).
- Chưa kiểm thử tải cao hoặc nhiều người dùng đồng thời.
- Chưa có test tích hợp với Gemini/OpenAI thật trong CI (dùng mock để tránh chi phí và phụ thuộc mạng).

---

## CHƯƠNG 6 — KẾT QUẢ, HẠN CHẾ VÀ HƯỚNG PHÁT TRIỂN

### 6.1. Kết quả đạt được

**Về chức năng:**

- Hoàn thành 7 phân hệ: Tài khoản, Luyện tập, Thi thử, Dashboard, AI, AI Tutor và Quản trị.
- Hỗ trợ 11 dạng bài DET thuộc 4 kỹ năng.
- Ngân hàng câu hỏi **897 câu**, 11 dạng bài, 6 chủ đề, 8 đề thi, hơn 2.700 lựa chọn.
- Import câu hỏi/đề thi bằng JSON với kiểm tra trước và ghi transaction.
- Quản trị đề thi, người dùng, nhật ký AI và bảo vệ admin cuối cùng.
- Hiển thị kết quả AI thân thiện, có cấu trúc thay vì JSON thô.

**Về kỹ thuật:**

- Kiến trúc MVC + Service layer rõ ràng, dễ mở rộng.
- Không dùng JavaScript phía client, đúng ràng buộc môn học.
- CSDL chuẩn hóa, có khóa ngoại và chỉ mục.
- Kiểm thử tự động hơn 170 test, chạy offline với mock.
- Quy trình Git nhánh, pull request và tài liệu đầy đủ.

**Về trải nghiệm:**

- Giao diện tiếng Việt, nhất quán giữa các trang.
- Phản hồi lỗi rõ ràng, có hướng dẫn sửa.
- Hỗ trợ bàn phím và không phụ thuộc JavaScript.

### 6.2. Đánh giá ưu điểm

1. **Phạm vi hoàn chỉnh:** từ đăng ký đến quản trị, không chỉ là bản demo một chức năng.
2. **Tính thực tiễn:** bám sát cấu trúc DET và nhu cầu luyện thi.
3. **An toàn:** chú trọng phân quyền, CSRF, XSS, bảo vệ audio riêng tư và prompt injection.
4. **Dễ bảo trì:** service tách biệt, đặt tên nhất quán, có tài liệu.
5. **Kiểm thử tốt:** số lượng test lớn, bao phủ các luồng quan trọng.

### 6.3. Hạn chế

1. **Chấm Speaking còn phụ thuộc chất lượng chuyển giọng nói thành văn bản**, chưa đánh giá trực tiếp phát âm.
2. **Luật thích ứng của Mock Test còn đơn giản**, chưa mô phỏng đầy đủ thuật toán DET.
3. **Chưa có giao diện quản trị tạo/sửa đề thi trực tiếp**; đề thi hiện được tạo qua import JSON hoặc seeder.
4. **Chưa có thông báo thời gian thực** do không dùng JavaScript; trạng thái chờ dùng meta refresh nên chưa tức thì.
5. **Chưa có phân quyền chi tiết** (ví dụ nhiều cấp admin, quyền theo module).
6. **Chưa có đa ngôn ngữ**, giao diện hiện chỉ tiếng Việt.
7. **Chưa có backup/restore tự động** cho CSDL và file audio.
8. **Một số dữ liệu mẫu còn phụ thuộc môi trường local** (`AI_PROVIDER=mock`, seeder chỉ chạy local/testing).

### 6.4. Hướng phát triển

| Hướng | Mô tả |
| --- | --- |
| Chấm phát âm | Tích hợp mô hình đánh giá phát âm hoặc so sánh waveform |
| Adaptive nâng cao | Áp dụng thuật toán ước lượng năng lực (IRT) cho Mock Test |
| Quản trị đề thi đầy đủ | Thêm form tạo/sửa đề, kéo thả thứ tự câu (không JS vẫn dùng nút lên/xuống) |
| Đa ngôn ngữ | Tách chuỗi UI, hỗ trợ tiếng Anh |
| Phân quyền nâng cao | Vai trò biên tập viên, người duyệt, chỉ xem |
| Thông báo | Dùng meta refresh thông minh hơn hoặc chuyển sang queue + email |
| Phân tích học tập | Báo cáo xu hướng, dự đoán điểm, gợi ý lộ trình |
| Triển khai | Docker hóa, CI/CD, backup định kỳ, giám sát lỗi |
| Mở rộng AI | Cache kết quả, giảm chi phí, kiểm duyệt nội dung AI |
| Bảo mật | Thêm CSP, rate limit toàn cục, audit log thao tác admin |

---

## CHƯƠNG 7 — KẾT LUẬN

Dự án **DET Practice** đã hoàn thành mục tiêu xây dựng một nền tảng luyện thi Duolingo English Test hoạt động được, có đầy đủ các phân hệ từ tài khoản, luyện tập, thi thử, dashboard, AI đến quản trị nội dung. Hệ thống đáp ứng các ràng buộc kỹ thuật của môn học: dùng PHP/Laravel, Blade + HTML5 + CSS thuần, không JavaScript phía client, CSDL MySQL quan hệ và kiểm thử tự động.

Thông qua dự án, nhóm đã rèn luyện được nhiều kỹ năng:

- Phân tích yêu cầu và thiết kế CSDL từ đầu.
- Tổ chức mã nguồn theo kiến trúc MVC và Service layer.
- Xử lý các bài toán bảo mật thực tế: phân quyền, CSRF, XSS, prompt injection.
- Tích hợp dịch vụ ngoài (AI, OAuth, SMTP) một cách có kiểm soát.
- Làm việc nhóm trên Git, phân chia module và tích hợp liên tục.
- Viết kiểm thử tự động và quy trình kiểm thử hồi quy.

Bên cạnh kết quả đạt được, dự án còn những hạn chế nhất định về chấm phát âm, tính thích ứng và giao diện quản trị đề thi. Đây là cơ sở để nhóm tiếp tục mở rộng trong tương lai. Nhìn chung, dự án đã đạt được mục tiêu đề ra và có thể dùng làm nền tảng để phát triển thành sản phẩm học tập hoàn chỉnh hơn.

---

## TÀI LIỆU THAM KHẢO

1. Laravel Documentation — https://laravel.com/docs
2. PHP Manual — https://www.php.net/manual/en/
3. MySQL Reference Manual — https://dev.mysql.com/doc/
4. MDN Web Docs — HTML, CSS — https://developer.mozilla.org/
5. Duolingo English Test — https://englishtest.duolingo.com/
6. Laravel Socialite — https://laravel.com/docs/socialite
7. Google Identity — OAuth 2.0 — https://developers.google.com/identity/protocols/oauth2
8. OWASP Top 10 — https://owasp.org/www-project-top-ten/
9. PHPUnit Documentation — https://phpunit.de/documentation.html
10. Google Gemini API — https://ai.google.dev/docs
11. OpenAI API — https://platform.openai.com/docs
12. Git Documentation — https://git-scm.com/doc
13. Tài liệu nội bộ dự án: `docs/ngu_canh.md`, `docs/phan_cong.md`, `docs/ai.md`, `docs/quan-tri.md`, `docs/import-cau-hoi.md`, `docs/ngan-hang-cau-hoi.md`, `docs/Thiết kế hệ thống DET Practice.md`.
14. Kho mã nguồn GitHub của nhóm: `Phat_Trien_Ma_Nguon_Mo`.

---

## PHỤ LỤC

### Phụ lục A — Tài khoản demo và lệnh vận hành

**Tài khoản mặc định (môi trường local/testing):**

| Vai trò | Email | Mật khẩu | Ghi chú |
| --- | --- | --- | --- |
| Quản trị viên | `admin@example.com` | `password` | Được tạo đầu tiên (id 1) |
| Học viên | `lam@example.com` | `password` | Tài khoản minh họa |
| Học viên | `phiphong1008@gmail.com` | — | Tài khoản Google |

**Lệnh cài đặt và chạy:**

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

**Lệnh kiểm thử và chuẩn hóa mã:**

```bash
php artisan test
php vendor/bin/pint
```

**Chạy với AI giả lập (offline):**

```dotenv
AI_PROVIDER=mock
```

### Phụ lục B — File JSON kiểm thử import

Đường dẫn: `public/downloads/import-cau-hoi-mau.json`

Nội dung gồm:

- 8 câu hỏi thuộc 8 dạng bài: `read_and_select`, `fill_in_the_blanks`, `interactive_reading`, `interactive_listening`, `listen_and_type`, `read_aloud`, `write_about_photo`, `write_about_topic`.
- 28 lựa chọn, 3 câu con đọc, 3 câu con nghe.
- 1 đề thi mẫu `Đề kiểm thử import tự động`, trạng thái `draft`.
- Tất cả câu hỏi ở trạng thái `draft`, mã có tiền tố `demo-import-` để không ảnh hưởng ngân hàng thật.
- Đường dẫn media tương đối: `audio/questions/listening-bank-01.wav`, `audio/listen-and-type-demo.mp3`, `images/fox-logo.jpg`.

Kết quả kiểm tra bằng `QuestionImportService::prepare()`: **0 lỗi, 0 cảnh báo**, tổng 8 câu, 28 lựa chọn, 3 câu con đọc, 3 câu con nghe, 8 câu trong đề.

Cách dùng: đăng nhập admin → Ngân hàng câu hỏi → Nhập câu hỏi → chọn file → Xem trước → Xác nhận nhập.

### Phụ lục C — Bảng đối chiếu chức năng và người thực hiện

| Phân hệ | Thành viên phụ trách | Mã chức năng |
| --- | --- | --- |
| Tài khoản | Trần Thế Hào | TK01–TK08 |
| Luyện tập | Trần Thế Hào | LT01–LT09 |
| Thi thử | Võ Nhật Lâm | MT01–MT09 |
| Dashboard | Võ Nhật Lâm | DB01–DB05 |
| Quản trị | Đoàn Phi Phong | AD01–AD08 |
| AI / AI Tutor | Đoàn Phi Phong | AI01–AI06, TU01–TU04 |

### Phụ lục D — Cấu trúc đề thi và câu hỏi con

**Interactive Reading** gồm 5 dạng câu con:

1. Complete the Sentence
2. Complete the Passage
3. Highlight the Answer
4. Identify the Idea
5. Title the Passage

**Interactive Listening** gồm 3 dạng câu con:

1. Listen and Complete
2. Listen and Respond
3. Summarise the Conversation

Mỗi câu con có thể có lựa chọn (đúng 1 đáp án) hoặc đáp án điền từ (`dapandung`).

### Phụ lục E — Ghi chú về thứ tự tạo admin

- `DatabaseSeeder` gọi `AdminNguoidungSeeder` trước `NguoidungSeeder`, đảm bảo CSDL mới luôn có admin là bản ghi đầu tiên.
- Với CSDL đang chạy, nhóm đã hoán đổi id 1 và 2 và cập nhật đồng bộ khóa ngoại ở các bảng: `ailog`, `aisudung`, `cauhoi`, `chamdiem`, `chungchi`, `chuoingay`, `danhdau`, `danhdau_loai`, `dethi`, `import_batches`, `lanthi`, `luyentap`, `tiendo`.
- Trước khi thao tác đã sao lưu CSDL; sau thao tác kiểm tra không có bản ghi mồ côi.

---

*Hết báo cáo.*
