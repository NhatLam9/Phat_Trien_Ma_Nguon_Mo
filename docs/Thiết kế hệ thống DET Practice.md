# Thiết kế hệ thống DET Practice

Oct 12, 2026 · @Phong. VÀ bạn hãy dùng tiếng việt để nói chuyện với tôi

> **CSDL chính:** `docs/duoling_csdl_remake.sql` (24 bảng, tên tiếng Việt)
> Các tên bảng/cột trong tài liệu này dùng theo CSDL chính.

## 1. Tổng quan và ràng buộc kỹ thuật

Hệ thống là website luyện thi Duolingo English Test (dự án học tập, không liên kết với Duolingo), xây bằng PHP và MySQL, giao diện chỉ dùng HTML và CSS thuần, không có một dòng JavaScript nào.

### 1.1. Mục tiêu hệ thống

- Cho người học luyện từng dạng bài DET và nhận đáp án, giải thích ngay sau khi nộp.
- Mô phỏng một bài thi thử gồm nhiều câu, chuyển câu theo năng lực (adaptive đơn giản), có báo cáo điểm.
- Chấm Writing (và Speaking nếu làm) bằng AI qua API, điểm chỉ mang tính tham khảo.
- Cho quản trị viên quản lý ngân hàng câu hỏi, người dùng và theo dõi thống kê.

### 1.2. Ràng buộc kỹ thuật

| Hạng mục | Được dùng | Không được dùng |
| --- | --- | --- |
| Ngôn ngữ phía máy chủ | PHP 8.2 trở lên | Node.js, Python service |
| Framework | Laravel (Blade, Eloquent, Socialite, Mail) | Breeze, Jetstream, Sanctum, Livewire, Inertia |
| Giao diện | HTML5, CSS thuần viết tay, SVG nhúng trong Blade | JavaScript ở mọi dạng, React, Vue, Alpine, jQuery, Tailwind, Vite, npm |
| Cơ sở dữ liệu | MySQL 8.0 | Redis, MongoDB |
| Gọi dịch vụ ngoài | HTTP từ PHP (Laravel Http client) tới API AI | Gọi API từ trình duyệt |
| Lưu file | Thư mục storage của Laravel | Dịch vụ lưu trữ cần SDK JavaScript |

Mọi tương tác của người dùng chỉ gồm: liên kết, form POST/GET, thẻ `<audio controls>`, `<details>` và chuyển trang do máy chủ điều khiển.

### 1.3. Cách thay thế cho các tính năng thường cần JavaScript

| Tính năng | Cách làm thuần HTML, CSS, PHP |
| --- | --- |
| Chuyển câu trong mock test | Mỗi câu là một trang form; POST xong máy chủ chọn câu kế và redirect |
| Giới hạn thời gian | Lưu `thoigian_cap` và hạn chót trên máy chủ, từ chối bài nộp quá hạn; trang hiển thị giờ hết hạn, dùng `<meta http-equiv="refresh">` để tự chuyển khi hết giờ |
| Ghi âm Speaking | `<input type="file" accept="audio/*">`, người học ghi âm bằng ứng dụng của máy rồi tải lên |
| Phát audio bài nghe | `<audio controls>`; không giới hạn được số lần nghe |
| Biểu đồ điểm | SVG hoặc thanh CSS dựng trong Blade từ dữ liệu PHP |
| Hiện/ẩn đáp án, mục thu gọn | Thẻ `<details>` hoặc CSS `:checked` |
| Chờ kết quả AI | Cột trạng thái `trangthai_ai`; trang kết quả dùng meta refresh cho đến khi xong |
| Chat AI Tutor | Mỗi lượt là một form POST, lịch sử đọc lại từ bảng `ailog` |

## 2. Tác nhân và phân quyền

Hệ thống có ba tác nhân bên trong (Khách, Người học, Quản trị viên) và một hệ thống bên ngoài (dịch vụ AI), cùng các dịch vụ hỗ trợ như Google OAuth và SMTP.

| Tác nhân | Mô tả | Vai trò trong CSDL |
| --- | --- | --- |
| Khách | Người chưa đăng nhập, chỉ xem trang giới thiệu, đăng ký, đăng nhập | Không có bản ghi |
| Người học | Người dùng đã đăng nhập, luyện tập, thi thử, dùng AI | `users.role = 'user'` |
| Quản trị viên | Quản lý nội dung và người dùng | `users.role = 'admin'` |
| Dịch vụ AI | API ngoài nhận bài làm và trả điểm, nhận xét | Hệ thống ngoài |
| Google OAuth, SMTP | Đăng nhập Google, gửi email xác minh và đặt lại mật khẩu | Hệ thống ngoài |

### Ma trận phân quyền

| Chức năng | Khách | Người học | Quản trị viên |
| --- | --- | --- | --- |
| Xem trang chủ, đăng ký, đăng nhập | Có | Có | Có |
| Cập nhật hồ sơ, đổi mật khẩu | Không | Có | Có |
| Luyện tập theo dạng bài, chủ đề | Không | Có | Có |
| Đánh dấu câu hỏi | Không | Có | Có |
| Thi thử và xem lịch sử thi | Không | Có | Có |
| Dùng AI chấm bài, AI Tutor (có hạn mức) | Không | Có | Có |
| Xem dashboard cá nhân | Không | Có | Có |
| CRUD câu hỏi, dạng bài, chủ đề | Không | Không | Có |
| Duyệt câu hỏi (draft, review, published) | Không | Không | Có |
| Khóa/mở tài khoản, đổi vai trò | Không | Không | Có |
| Xem thống kê hệ thống | Không | Không | Có |

Quy tắc kiểm soát: middleware `auth` chặn người chưa đăng nhập, middleware `admin` chặn mọi route `/admin/*` với người không có `vaitro = 'admin'`, và tài khoản có `trangthai = 'locked'` bị đăng xuất khi gửi yêu cầu kế tiếp.

## 3. Yêu cầu chức năng theo phân hệ

Hệ thống gồm 7 phân hệ. Cột Mức: BB là bắt buộc (MVP), MR là mở rộng làm nếu đủ điều kiện.

### 3.1. Phân hệ Tài khoản (TK)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| TK01 | Đăng ký bằng email | Nhập họ tên, email, mật khẩu; kiểm tra email chưa tồn tại; mật khẩu băm bằng bcrypt | BB |
| TK02 | Xác minh email | Gửi liên kết có chữ ký qua SMTP; chưa xác minh thì không vào khu vực người học | BB |
| TK03 | Đăng nhập | Email và mật khẩu, session; từ chối nếu `is_active = 0` | BB |
| TK04 | Đăng nhập Google | Laravel Socialite; gặp email đã có thì liên kết `google_id` vào tài khoản cũ | BB |
| TK05 | Đăng xuất | Hủy session, tạo lại token CSRF | BB |
| TK06 | Quên và đặt lại mật khẩu | Gửi liên kết đặt lại có thời hạn; tài khoản chỉ Google phải được đặt mật khẩu trước khi dùng email | BB |
| TK07 | Hồ sơ cá nhân | Sửa tên, ảnh đại diện, điểm mục tiêu DET (10-160), đổi mật khẩu | BB |
| TK08 | Phân quyền | Middleware `auth`, `verified`, `admin` | BB |

### 3.2. Phân hệ Luyện tập (LT)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| LT01 | Danh sách dạng bài | Hiển thị các dạng bài đang bật, nhóm theo kỹ năng (đọc, viết, nghe, nói) | BB |
| LT02 | Chọn câu luyện | Lọc theo dạng bài, chủ đề, độ khó 1-10; chỉ lấy câu `trangthai = 'published'`; ưu tiên câu chưa làm | BB |
| LT03 | Làm bài | Mỗi câu một trang; hiển thị đề, ảnh, audio; không lộ đáp án trong HTML | BB |
| LT04 | Nộp và chấm tự động | Dạng `cachcham = 'auto'` được so đáp án phía máy chủ, lưu vào `luyentap` | BB |
| LT05 | Xem đáp án, giải thích | Hiện sau khi nộp | BB |
| LT06 | Gửi bài chấm AI | Dạng `ai` chuyển sang phân hệ Chấm AI, kết quả lưu vào cùng lượt làm | BB |
| LT07 | Đánh dấu câu yếu | Thêm/bỏ đánh dấu; trang danh sách câu đã đánh dấu để ôn lại | BB |
| LT08 | Lịch sử luyện tập | Danh sách lượt làm, lọc theo dạng bài, ngày | BB |
| LT09 | Tìm kiếm câu hỏi | Theo từ khóa trong nội dung | MR |

### 3.3. Phân hệ Thi thử (MT)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| MT01 | Bắt đầu bài thi | Chọn chế độ `full` hoặc `section`; tạo bản ghi `lanthi` với `thoigian` và `batdau`; mỗi người chỉ có một bài đang `in_progress` | BB |
| MT02 | Cấp câu hỏi tiếp theo | Máy chủ chọn câu theo luật adaptive (mục 9), ghi `lanthicauhoi.thoigian_cap`, không lặp câu | BB |
| MT03 | Nộp từng câu | POST câu trả lời, từ chối nếu quá hạn hoặc câu đã có `thoigian_tra_loi`; redirect sang câu kế (POST-redirect-GET) | BB |
| MT04 | Kiểm soát thời gian | Tính thời gian còn lại từ `batdau`; hết giờ thì chốt bài | BB |
| MT05 | Kết thúc bài thi | Chấm các câu auto, gọi AI cho câu cần chấm, tính điểm 10-160 và bốn điểm thành phần | BB |
| MT06 | Báo cáo kết quả | Điểm tổng, điểm thành phần, số câu đúng theo dạng bài, đáp án và nhận xét từng câu | BB |
| MT07 | Lịch sử thi | Danh sách các lần thi, so sánh điểm | BB |
| MT08 | Bỏ dở bài thi | Chuyển `trangthai = 'abandoned'` khi người học hủy hoặc bắt đầu bài mới | BB |
| MT09 | Cấp chứng chỉ thi thử | Lưu vào `chungchi` (mã chứng chỉ, điểm tổng, 4 điểm thành phần, cấp độ) | BB |

### 3.4. Phân hệ Chấm bài AI (AI)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| AI01 | Chấm Writing | Gửi văn bản và đề bài tới API, nhận điểm theo tiêu chí và gợi ý sửa | BB |
| AI02 | Chấm Speaking | Tải file audio, chuyển thành văn bản (speech-to-text), rồi chấm nội dung và độ trôi chảy bằng mô hình ngôn ngữ | MR |
| AI03 | Giới hạn lượt gọi | Đếm theo ngày trong `aisudung`, vượt hạn mức thì từ chối và báo rõ | BB |
| AI04 | Ghi nhật ký | Lưu đầu vào, đầu ra, tên mô hình, số token vào `ailog` | BB |
| AI05 | Trạng thái chấm | `trangthai_ai` gồm pending, done, failed; lỗi API thì cho phép chấm lại | BB |
| AI06 | Bảo vệ khỏi chèn lệnh | Bài làm đặt trong khối dữ liệu riêng của prompt, không được xử lý như chỉ dẫn | BB |

Điểm AI chỉ mang tính tham khảo, đặc biệt với phát âm.

### 3.5. Phân hệ AI Tutor (TU)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| TU01 | Sửa ngữ pháp | Nhập đoạn văn, nhận bản sửa và giải thích lỗi | MR |
| TU02 | Dịch Anh-Việt | Dịch hai chiều | MR |
| TU03 | Viết lại bài | Gợi ý bản viết tốt hơn | MR |
| TU04 | Lịch sử hội thoại | Đọc lại từ `ailog`, mỗi lượt dùng chung hạn mức AI03 | MR |

### 3.6. Phân hệ Dashboard (DB)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| DB01 | Tổng quan học tập | Số câu đã làm, tỷ lệ đúng, thời gian luyện | BB |
| DB02 | Tiến độ theo dạng bài | Từ `tiendo`, hiển thị bằng thanh CSS | BB |
| DB03 | Biểu đồ điểm thi thử | Đường SVG vẽ trong Blade từ `tests.total_score` | BB |
| DB04 | Chuỗi ngày học | Từ `chuoingay` | BB |
| DB05 | Gợi ý bài hôm nay | Chọn dạng bài có tỷ lệ đúng thấp nhất | MR |

### 3.7. Phân hệ Quản trị (AD)

| Mã | Chức năng | Mô tả | Mức |
| --- | --- | --- | --- |
| AD01 | Quản lý câu hỏi | Thêm, sửa, lưu nháp, đưa vào review, xuất bản, lưu trữ (không xóa cứng câu đã dùng); tải ảnh, audio | BB |
| AD02 | Quản lý dạng bài | Bật/tắt, đặt `cachcham`, giới hạn thời gian mặc định | BB |
| AD03 | Quản lý chủ đề | CRUD | BB |
| AD04 | Quản lý người dùng | Tìm, khóa/mở, đổi vai trò | BB |
| AD05 | Thống kê hệ thống | Số người dùng, câu hỏi theo dạng bài, lượt thi, lượt gọi AI theo ngày | BB |
| AD06 | Xem nhật ký AI | Lọc theo người dùng, loại, lỗi | MR |

### 3.8. Các dạng bài DET được hỗ trợ

Danh sách dưới đây trùng với dữ liệu mẫu trong mục 8 và cần đối chiếu với tài liệu Official Guide mới nhất của Duolingo trước khi chốt.

| Kỹ năng | Dạng bài | Cách chấm |
| --- | --- | --- |
| Đọc | Read and Select, Fill in the Blanks, Read and Complete, Interactive Reading | Tự động |
| Nghe | Listen and Type | Tự động |
| Nghe | Interactive Listening | AI |
| Viết | Write About the Photo, Interactive Writing, Writing Sample | AI |
| Nói | Read Aloud, Speak About the Photo, Read Then Speak, Listen Then Speak, Speaking Sample | AI |

Ghi chú: Read and Complete cần ô nhập từng chữ cái, không có JavaScript thì không tự nhảy ô; thay bằng một ô nhập cho mỗi từ khuyết.

**Interactive Reading** có 5 dạng câu hỏi con: Complete the Sentence, Complete the Passage, Highlight the Answer, Identify the Idea, Title the Passage.

**Interactive Listening** có 3 phần: Listen and Complete, Listen and Respond, Summarise the Conversation.

## 4. Yêu cầu hệ thống và phi chức năng

### 4.1. Môi trường chạy

| Thành phần | Yêu cầu |
| --- | --- |
| PHP | 8.2 trở lên, bật `pdo_mysql`, `mbstring`, `fileinfo`, `curl`, `openssl` |
| Laravel | Bản đang được hỗ trợ tại thời điểm triển khai (kiểm tra tại laravel.com/docs/releases) |
| MySQL | 8.0, bộ ký tự `utf8mb4`, engine InnoDB |
| Máy chủ web | Apache hoặc Nginx; XAMPP cho môi trường phát triển |
| Cấu hình PHP | `upload_max_filesize` và `post_max_size` đủ cho file audio; `max_execution_time` đủ cho một lần gọi AI |
| HTTPS | Bắt buộc ở môi trường thật (đăng nhập Google, bảo vệ session) |
| Thư mục lưu file | `storage/app/private` cho bài ghi âm; `public` chỉ chứa CSS, ảnh và audio đề thi |
| Biến môi trường | Khóa API AI, thông tin Google OAuth, cấu hình SMTP đặt trong `.env`, không đưa vào Git |

### 4.2. Yêu cầu phi chức năng

| Mã | Nhóm | Yêu cầu |
| --- | --- | --- |
| NF01 | Không dùng JavaScript | Không có thẻ `<script>`, không có thuộc tính sự kiện (`onclick`...), không có thư viện front-end; có thể kiểm tra bằng tìm kiếm trong thư mục `resources/views` |
| NF02 | Bảo mật dữ liệu | Mật khẩu băm bcrypt; truy vấn qua Eloquent hoặc tham số ràng buộc; Blade escape `{{ }}`, không dùng `{!! !!}` cho nội dung người dùng |
| NF03 | CSRF | Mọi form POST có `@csrf` |
| NF04 | Không lộ đáp án | Đáp án (`dap_an`, `luachon`) và giải thích chỉ gửi về trình duyệt sau khi nộp |
| NF05 | Tải file | Kiểm tra MIME thật, phần mở rộng, dung lượng tối đa; đặt tên ngẫu nhiên; file ghi âm phục vụ qua controller có kiểm tra quyền |
| NF06 | Giới hạn tần suất | Throttle cho đăng nhập, đăng ký, gọi AI |
| NF07 | Chống nộp lặp | Mỗi câu thi chỉ nhận một lần trả lời; nút quay lại không làm mất hoặc nhân đôi kết quả |
| NF08 | Toàn vẹn dữ liệu | Khóa ngoại, ràng buộc UNIQUE, CHECK; thao tác chốt bài thi nằm trong một giao dịch |
| NF09 | Quyền riêng tư | Có trang chính sách nêu rõ audio và văn bản được gửi tới bên thứ ba; nêu thời gian lưu và cách xóa; người dùng được xóa bài ghi âm của mình |
| NF10 | Độ tin cậy của AI | Điểm AI ghi chú là tham khảo; lỗi API không làm mất bài làm, chỉ đặt `trangthai_ai = 'failed'` |
| NF11 | Tương thích | Chrome, Firefox, Safari, Edge bản mới; bố cục responsive bằng CSS (flex, grid, media query) |
| NF12 | Khả năng truy cập | Dùng thẻ ngữ nghĩa, nhãn cho mọi ô nhập, tương phản đủ đọc |
| NF13 | Bảo trì | Tách Controller, Service (`TestService`, `ScoringService`, `AIService`), Model; migration là nguồn duy nhất của lược đồ |
| NF14 | Kiểm thử | Có test cho luật chọn câu adaptive, tính điểm, hết giờ, phân quyền |
| NF15 | Ngôn ngữ giao diện | Tiếng Việt, thuật ngữ DET giữ tiếng Anh |

### 4.3. Hạn chế đã chấp nhận do không dùng JavaScript

- Không ghi âm trực tiếp trong trang; người học tải file lên.
- Không có đồng hồ đếm ngược chạy thời gian thực; hạn giờ do máy chủ kiểm soát.
- Không giới hạn được số lần nghe audio.
- Không gợi ý hay kiểm tra khi đang gõ; mọi kiểm tra diễn ra sau khi gửi form.
- Mỗi thao tác (chuyển câu, gửi lượt chat) tải lại trang.

## 5. Sơ đồ phân rã chức năng (BFD)

Hệ thống chia thành 7 phân hệ, mỗi phân hệ phân rã thêm một mức chức năng; mã chức năng khớp với mục 3.

```mermaid
flowchart LR
  ROOT["Hệ thống DET Practice"]

  ROOT --> TK["1. Tài khoản"]
  TK --> TK1["Đăng ký, xác minh email"]
  TK --> TK2["Đăng nhập, đăng xuất"]
  TK --> TK3["Đăng nhập Google"]
  TK --> TK4["Quên, đặt lại mật khẩu"]
  TK --> TK5["Hồ sơ cá nhân"]

  ROOT --> LT["2. Luyện tập"]
  LT --> LT1["Chọn dạng bài, chủ đề, độ khó"]
  LT --> LT2["Làm bài và nộp"]
  LT --> LT3["Chấm tự động, xem giải thích"]
  LT --> LT4["Đánh dấu câu yếu"]
  LT --> LT5["Lịch sử luyện tập"]

  ROOT --> MT["3. Thi thử"]
  MT --> MT1["Bắt đầu bài thi"]
  MT --> MT2["Cấp câu theo năng lực"]
  MT --> MT3["Nộp câu, kiểm soát giờ"]
  MT --> MT4["Chốt bài, tính điểm"]
  MT --> MT5["Báo cáo, lịch sử thi, chứng chỉ"]

  ROOT --> AI["4. Chấm bài AI"]
  AI --> AI1["Chấm Writing"]
  AI --> AI2["Chấm Speaking"]
  AI --> AI3["Giới hạn lượt, nhật ký"]

  ROOT --> TU["5. AI Tutor"]
  TU --> TU1["Sửa ngữ pháp"]
  TU --> TU2["Dịch Anh-Việt"]
  TU --> TU3["Viết lại bài"]

  ROOT --> DB["6. Dashboard"]
  DB --> DB1["Thống kê tổng quan"]
  DB --> DB2["Tiến độ theo dạng bài"]
  DB --> DB3["Biểu đồ điểm, chuỗi ngày học"]

  ROOT --> AD["7. Quản trị"]
  AD --> AD1["Quản lý câu hỏi"]
  AD --> AD2["Quản lý dạng bài, chủ đề"]
  AD --> AD3["Quản lý người dùng"]
  AD --> AD4["Thống kê hệ thống"]
```

Bảng đối chiếu mã chức năng với nhánh trong sơ đồ:

| Phân hệ | Nhánh BFD | Mã chức năng ở mục 3 |
| --- | --- | --- |
| Tài khoản | TK1-TK5 | TK01-TK08 |
| Luyện tập | LT1-LT5 | LT01-LT09 |
| Thi thử | MT1-MT5 | MT01-MT09 |
| Chấm bài AI | AI1-AI3 | AI01-AI06 |
| AI Tutor | TU1-TU3 | TU01-TU04 |
| Dashboard | DB1-DB3 | DB01-DB05 |
| Quản trị | AD1-AD4 | AD01-AD06 |

## 6. Sơ đồ luồng dữ liệu (DFD)

Quy ước ký hiệu: hình chữ nhật là tác nhân ngoài, hình bo tròn là tiến trình, hình trụ là kho dữ liệu.

### 6.1. DFD mức 0 (sơ đồ ngữ cảnh)

```mermaid
flowchart LR
  HV["Người học"]
  QT["Quản trị viên"]
  AIS["Dịch vụ AI"]
  EXT["Google OAuth và SMTP"]
  SYS(("0. Hệ thống DET Practice"))

  HV -->|"thông tin đăng ký, đăng nhập, bài làm, yêu cầu thi"| SYS
  SYS -->|"câu hỏi, đáp án, điểm, báo cáo, nhận xét"| HV
  QT -->|"dữ liệu câu hỏi, lệnh quản lý người dùng"| SYS
  SYS -->|"thống kê, danh sách quản lý"| QT
  SYS -->|"bài làm cần chấm, yêu cầu tutor"| AIS
  AIS -->|"điểm, nhận xét, bản sửa"| SYS
  SYS -->|"yêu cầu xác thực, thư xác minh"| EXT
  EXT -->|"thông tin tài khoản Google, trạng thái gửi thư"| SYS
```

### 6.2. DFD mức 1

Kho dữ liệu: D1 Người dùng (`nguoidung`), D2 Ngân hàng câu hỏi (`cauhoi`, `loaicauhoi`, `danhmuc`, `luachon`, `dap_an`), D2a Câu hỏi con Interactive (`cauhoi_doc`, `cauhoi_nghe`, `luachon_doc`, `luachon_nghe`), D2b Đề thi (`dethi`, `dethicauhoi`), D3 Luyện tập (`luyentap`, `danhdau`), D4 Thi thử (`lanthi`, `lanthicauhoi`, `traloi`, `chamdiem`), D4a Vi phạm & Chứng chỉ (`vipham`, `chungchi`), D5 Nhật ký AI (`ailog`, `aisudung`), D6 Tiến độ (`tiendo`, `chuoingay`).

```mermaid
flowchart LR
  HV["Người học"]
  QT["Quản trị viên"]
  AIS["Dịch vụ AI"]
  EXT["Google OAuth và SMTP"]

  P1(("1.0 Quản lý tài khoản"))
  P2(("2.0 Luyện tập"))
  P3(("3.0 Thi thử"))
  P4(("4.0 Chấm bài AI"))
  P5(("5.0 AI Tutor"))
  P6(("6.0 Dashboard"))
  P7(("7.0 Quản trị"))

  D1[("D1 Người dùng")]
  D2[("D2 Ngân hàng câu hỏi")]
  D2A[("D2a Câu hỏi con Interactive")]
  D2B[("D2b Đề thi")]
  D3[("D3 Luyện tập")]
  D4[("D4 Thi thử")]
  D4A[("D4a Vi phạm & Chứng chỉ")]
  D5[("D5 Nhật ký AI")]
  D6[("D6 Tiến độ")]

  HV -->|"đăng ký, đăng nhập, hồ sơ"| P1
  P1 -->|"kết quả xác thực"| HV
  P1 <--> EXT
  P1 <--> D1

  HV -->|"chọn dạng bài, bài làm, đánh dấu"| P2
  P2 -->|"câu hỏi, đáp án, giải thích"| HV
  D2 -->|"câu hỏi đã xuất bản"| P2
  D2A -->|"câu hỏi con"| P2
  P2 -->|"lượt làm, đánh dấu"| D3
  P2 -->|"kết quả mỗi lượt"| D6
  P2 -->|"bài cần chấm AI"| P4

  HV -->|"bắt đầu, câu trả lời"| P3
  P3 -->|"câu kế tiếp, báo cáo điểm"| HV
  D2B -->|"đề thi"| P3
  D2 -->|"câu theo độ khó"| P3
  D2A -->|"câu hỏi con"| P3
  P3 <--> D4
  P3 -->|"bài cần chấm AI"| P4
  P3 -->|"điểm các lần thi"| D6
  P3 -->|"cấp chứng chỉ"| D4A

  P4 -->|"bài làm"| AIS
  AIS -->|"điểm, nhận xét"| P4
  P4 <--> D5
  P4 -->|"điểm AI"| D3
  P4 -->|"điểm AI"| D4

  HV -->|"văn bản cần xử lý"| P5
  P5 -->|"kết quả tutor"| HV
  P5 --> AIS
  AIS --> P5
  P5 <--> D5

  D6 -->|"số liệu tiến độ"| P6
  D4 -->|"điểm thi"| P6
  P6 -->|"biểu đồ, thống kê"| HV

  QT -->|"câu hỏi, lệnh quản lý"| P7
  P7 -->|"thống kê, danh sách"| QT
  P7 <--> D2
  P7 <--> D2A
  P7 <--> D1
  D5 -->|"lượt gọi AI"| P7
```

### 6.3. DFD mức 2 — tiến trình 2.0 Luyện tập

```mermaid
flowchart LR
  HV["Người học"]
  D2[("D2 Ngân hàng câu hỏi")]
  D3[("D3 Luyện tập")]
  D6[("D6 Tiến độ")]
  P4(("4.0 Chấm bài AI"))

  P21(("2.1 Chọn câu luyện"))
  P22(("2.2 Nhận và kiểm tra bài làm"))
  P23(("2.3 Chấm tự động"))
  P24(("2.4 Cập nhật tiến độ"))
  P25(("2.5 Quản lý đánh dấu"))

  HV -->|"dạng bài, chủ đề, độ khó"| P21
  D2 -->|"câu đã xuất bản"| P21
  D3 -->|"câu đã làm"| P21
  P21 -->|"đề bài, ảnh, audio"| HV
  HV -->|"câu trả lời hoặc file ghi âm"| P22
  P22 -->|"bài hợp lệ"| P23
  D2 -->|"đáp án"| P23
  P23 -->|"lượt làm đã chấm"| D3
  P23 -->|"đáp án, giải thích"| HV
  P22 -->|"bài thuộc dạng chấm AI"| P4
  P23 -->|"đúng hoặc sai"| P24
  P24 -->|"tổng số, số đúng, chuỗi ngày"| D6
  HV -->|"đánh dấu hoặc bỏ đánh dấu"| P25
  P25 <--> D3
```

### 6.4. DFD mức 2 — tiến trình 3.0 Thi thử

```mermaid
flowchart LR
  HV["Người học"]
  D2[("D2 Ngân hàng câu hỏi")]
  D4[("D4 Thi thử")]
  D6[("D6 Tiến độ")]
  P4(("4.0 Chấm bài AI"))

  P31(("3.1 Khởi tạo bài thi"))
  P32(("3.2 Chọn câu kế tiếp"))
  P33(("3.3 Nhận câu trả lời"))
  P34(("3.4 Chốt bài, tính điểm"))
  P35(("3.5 Lập báo cáo"))

  HV -->|"yêu cầu thi, chế độ"| P31
  P31 -->|"bài thi mới, giờ bắt đầu"| D4
  D4 -->|"độ khó hiện tại, câu đã cấp"| P32
  D2 -->|"câu chưa dùng theo độ khó"| P32
  P32 -->|"câu được cấp, giờ cấp"| D4
  P32 -->|"đề bài"| HV
  HV -->|"câu trả lời"| P33
  D4 -->|"giờ bắt đầu, hạn chót"| P33
  P33 -->|"câu trả lời hợp lệ"| D4
  P33 -->|"đúng hoặc sai, điều chỉnh độ khó"| P32
  P33 -->|"hết câu hoặc hết giờ"| P34
  D4 -->|"toàn bộ bài làm"| P34
  P34 -->|"bài cần chấm AI"| P4
  P4 -->|"điểm AI"| P34
  P34 -->|"điểm tổng và thành phần"| D4
  P34 -->|"điểm thi mới"| D6
  D4 -->|"kết quả bài thi"| P35
  P35 -->|"báo cáo điểm"| HV
```

## 7. Mô hình thực thể liên kết (ERD)

Cơ sở dữ liệu có 24 bảng nghiệp vụ, khớp với 7 phân hệ (xem `docs/duoling_csdl_remake.sql`). Các bảng hệ thống của Laravel (`migrations`, `sessions`, `password_reset_tokens`, `cache`) do `php artisan migrate` tự tạo.

```mermaid
erDiagram
  nguoidung ||--o{ luyentap : "làm"
  nguoidung ||--o{ danhdau : "đánh dấu"
  nguoidung ||--o{ lanthi : "thi"
  nguoidung ||--o{ ailog : "gọi AI"
  nguoidung ||--o{ aisudung : "đếm theo ngày"
  nguoidung ||--o{ tiendo : "tiến độ"
  nguoidung ||--|| chuoingay : "chuỗi ngày"
  nguoidung ||--o{ cauhoi : "tạo"
  nguoidung ||--o{ chungchi : "nhận chứng chỉ"
  nguoidung ||--o{ chamdiem : "chấm"
  loaicauhoi ||--o{ cauhoi : "phân loại"
  loaicauhoi ||--o{ tiendo : "theo dạng"
  danhmuc ||--o{ cauhoi : "chủ đề"
  cauhoi ||--o{ luachon : "lựa chọn"
  cauhoi ||--o{ dap_an : "đáp án"
  cauhoi ||--o{ cauhoi_doc : "câu con đọc"
  cauhoi ||--o{ cauhoi_nghe : "câu con nghe"
  cauhoi ||--o{ luyentap : "được làm"
  cauhoi ||--o{ danhdau : "được đánh dấu"
  cauhoi ||--o{ dethicauhoi : "được cấp"
  cauhoi ||--o{ lanthicauhoi : "được cấp"
  cauhoi_doc ||--o{ luachon_doc : "lựa chọn"
  cauhoi_nghe ||--o{ luachon_nghe : "lựa chọn"
  dethi ||--o{ dethicauhoi : "gồm"
  dethi ||--o{ lanthi : "được thi"
  lanthi ||--o{ lanthicauhoi : "gồm"
  lanthi ||--o{ vipham : "có vi phạm"
  lanthi ||--o{ chungchi : "cấp chứng chỉ"
  lanthicauhoi ||--|| traloi : "trả lời"
  traloi ||--o{ chamdiem : "được chấm"

  nguoidung {
    bigint id PK
    varchar email UK
    varchar matkhau
    varchar google_id UK
    enum vaitro
    enum trangthai
  }
  danhmuc {
    bigint id PK
    varchar ten
  }
  loaicauhoi {
    bigint id PK
    varchar ma UK
    enum kynang
    enum cachcham
    int thoigian
    tinyint is_active
  }
  cauhoi {
    bigint id PK
    bigint loai_id FK
    bigint danhmuc_id FK
    text noidung
    tinyint dokho
    enum trangthai
  }
  luachon {
    bigint id PK
    bigint cauhoi_id FK
    text noidung
    tinyint ladung
  }
  dap_an {
    bigint id PK
    bigint cauhoi_id FK
    text noidung
    tinyint ladung
  }
  cauhoi_doc {
    bigint id PK
    bigint cauhoi_id FK
    enum loai
    text noidung
    tinyint thutu
  }
  cauhoi_nghe {
    bigint id PK
    bigint cauhoi_id FK
    enum loai
    text noidung
    tinyint thutu
  }
  luachon_doc {
    bigint id PK
    bigint cauhoi_doc_id FK
    text noidung
    tinyint ladung
  }
  luachon_nghe {
    bigint id PK
    bigint cauhoi_nghe_id FK
    text noidung
    tinyint ladung
  }
  luyentap {
    bigint id PK
    bigint nguoidung_id FK
    bigint cauhoi_id FK
    text noidung
    varchar audio
    tinyint ladung
    enum trangthai_ai
    decimal diem_ai
    int thoigianlam
  }
  danhdau {
    bigint id PK
    bigint nguoidung_id FK
    bigint cauhoi_id FK
  }
  dethi {
    bigint id PK
    varchar ten
    enum chedo
    enum kieu
    int thoigian
    enum trangthai
  }
  dethicauhoi {
    bigint id PK
    bigint dethi_id FK
    bigint cauhoi_id FK
    tinyint thutu
  }
  lanthi {
    bigint id PK
    bigint nguoidung_id FK
    bigint dethi_id FK
    decimal dokho_hientai
    enum trangthai
    smallint tongdiem
  }
  lanthicauhoi {
    bigint id PK
    bigint lanthi_id FK
    bigint cauhoi_id FK
    tinyint thutu
    datetime thoigian_cap
    datetime thoigian_tra_loi
  }
  traloi {
    bigint id PK
    bigint lanthicauhoi_id FK
    text noidung
    tinyint ladung
    enum trangthai_ai
    decimal diem_ai
  }
  chamdiem {
    bigint id PK
    bigint traloi_id FK
    bigint nguoicham FK
    enum loai
    decimal nguphap
    decimal tuvung
    decimal luloat
    decimal phatam
    decimal machlac
  }
  vipham {
    bigint id PK
    bigint lanthi_id FK
    enum loai
    enum nguon
  }
  chungchi {
    bigint id PK
    bigint nguoidung_id FK
    bigint lanthi_id FK
    varchar ma UK
    smallint tongdiem
    varchar trinhdo
  }
  ailog {
    bigint id PK
    bigint nguoidung_id FK
    enum loai
    varchar model
  }
  aisudung {
    bigint id PK
    bigint nguoidung_id FK
    date ngay
    int solan
  }
  tiendo {
    bigint id PK
    bigint nguoidung_id FK
    bigint loai_id FK
    int tongso
    int sodung
  }
  chuoingay {
    bigint nguoidung_id PK
    int hientai
    int dainhat
    date ngaygannhat
  }
```

Sơ đồ chỉ liệt kê cột chính; đầy đủ cột nằm ở mã MySQL `docs/duoling_csdl_remake.sql`.

### 7.1. Từ điển dữ liệu

| Bảng | Vai trò | Khóa và ràng buộc chính |
| --- | --- | --- |
| `nguoidung` | Tài khoản người học và quản trị viên | `email` duy nhất; `google_id` duy nhất; `matkhau` được NULL cho tài khoản chỉ có Google |
| `danhmuc` | Chủ đề câu hỏi | Xóa chủ đề thì `cauhoi.danhmuc_id` về NULL |
| `loaicauhoi` | Danh mục dạng bài, cách chấm, giới hạn thời gian | `ma` duy nhất |
| `cauhoi` | Ngân hàng câu hỏi | `dokho` 1-10 (CHECK); `trangthai` gồm draft, review, published, archived; không xóa cứng câu đã có trong bài thi |
| `luachon` | Lựa chọn A/B/C/D cho câu trắc nghiệm | Xóa câu hỏi thì xóa theo |
| `dap_an` | Đáp án cho các dạng không phải trắc nghiệm | Xóa câu hỏi thì xóa theo |
| `cauhoi_doc` | Câu hỏi con Interactive Reading (5 dạng) | `(cauhoi_id, thutu)` duy nhất; xóa câu hỏi cha thì xóa theo |
| `cauhoi_nghe` | Câu hỏi con Interactive Listening (3 phần) | `(cauhoi_id, thutu)` duy nhất; xóa câu hỏi cha thì xóa theo |
| `luachon_doc` | Lựa chọn cho câu hỏi con đọc | Xóa câu con thì xóa theo |
| `luachon_nghe` | Lựa chọn cho câu hỏi con nghe | Xóa câu con thì xóa theo |
| `luyentap` | Mỗi lượt làm ở chế độ luyện tập | Xóa người dùng thì xóa theo; `trangthai_ai` theo dõi việc chấm AI (pending/done/failed) |
| `danhdau` | Câu hỏi được đánh dấu | `(nguoidung_id, cauhoi_id)` duy nhất |
| `dethi` | Đề thi (tên, chế độ, kiểu, thời gian) | `nguoitao` trỏ `nguoidung` |
| `dethicauhoi` | Câu hỏi thuộc đề thi | `(dethi_id, cauhoi_id)` và `(dethi_id, thutu)` duy nhất |
| `lanthi` | Một lần thi thử | Điểm tổng 10-160 và bốn điểm thành phần; mỗi người một lần `in_progress` |
| `lanthicauhoi` | Câu hỏi trong lần thi theo thứ tự | `(lanthi_id, thutu)` và `(lanthi_id, cauhoi_id)` duy nhất; `thoigian_cap`, `thoigian_tra_loi` |
| `traloi` | Câu trả lời của từng câu | `lanthicauhoi_id` duy nhất (chống nộp lặp); `trangthai_ai` theo dõi chấm AI |
| `chamdiem` | Chấm điểm chi tiết (Speaking/Writing) | `(traloi_id, nguoicham)` duy nhất; `loai` phân biệt speaking/writing |
| `vipham` | Ghi nhận vi phạm trong lần thi | `(lanthi_id, thoigian)` có index; `nguon` phân biệt auto/self_report/admin |
| `chungchi` | Chứng chỉ thi thử | `ma` duy nhất; `lanthi_id` duy nhất |
| `ailog` | Nhật ký mọi lần gọi AI | Lưu mô hình và số token để đối soát |
| `aisudung` | Hạn mức AI theo ngày | `(nguoidung_id, ngay)` duy nhất |
| `tiendo` | Tổng hợp theo (người dùng, dạng bài) | `(nguoidung_id, loai_id)` duy nhất; cập nhật bằng trigger |
| `chuoingay` | Chuỗi ngày học | Khóa chính là `nguoidung_id` |

### 7.2. Cấu trúc dữ liệu của câu hỏi

| Dạng bài | Lưu ở đâu | Ghi chú |
| --- | --- | --- |
| Read and Select | `luachon` | Các từ, `ladung = 1` cho từ thật |
| Fill in the Blanks | `dap_an` | Các từ điền vào, theo thứ tự câu |
| Read and Complete | `dap_an` | Các phần điền vào ô, theo thứ tự |
| Listen and Type | `dap_an` | `{"text": "câu chuẩn"}` |
| Interactive Reading | `cauhoi_doc` + `luachon_doc` | 5 câu con, mỗi câu có lựa chọn riêng |
| Interactive Listening | `cauhoi_nghe` + `luachon_nghe` | 3 phần, mỗi phần có lựa chọn riêng |
| Dạng chấm AI | `dap_an` | `{"rubric": "tiêu chí chấm", "sample": "bài mẫu"}` |

Ghi chú: Câu hỏi con và lựa chọn câu con được tách thành bảng riêng (mỗi câu con là một dòng).

Cấu trúc này là đề xuất; cần chốt khi viết bộ nhập câu hỏi cho Admin.
