CREATE DATABASE duoling_csdl_remake
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE duoling_csdl_remake;

-- =====================================================================
-- 1. BẢNG NGƯỜI DÙNG
-- =====================================================================
CREATE TABLE nguoidung (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã người dùng',
    hoten VARCHAR(100) NOT NULL COMMENT 'Họ và tên',
    email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email đăng nhập',
    email_xacthuc DATETIME DEFAULT NULL COMMENT 'Thời gian xác thực email',
    matkhau VARCHAR(255) DEFAULT NULL COMMENT 'Mật khẩu đã mã hóa (NULL nếu chỉ dùng Google)',
    google_id VARCHAR(150) DEFAULT NULL UNIQUE COMMENT 'Mã tài khoản Google',
    vaitro ENUM('user','admin') NOT NULL DEFAULT 'user' COMMENT 'Vai trò: user=học viên, admin=quản trị',
    anh VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
    muctieu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Mục tiêu điểm DET (10-160)',
    trangthai ENUM('active','locked') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái: active=hoạt động, locked=bị khóa',
    remember_token VARCHAR(100) DEFAULT NULL COMMENT 'Token ghi nhớ đăng nhập',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật'
);

-- =====================================================================
-- 2. BẢNG DANH MỤC
-- =====================================================================
CREATE TABLE danhmuc (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã danh mục',
    ten VARCHAR(100) NOT NULL COMMENT 'Tên chủ đề',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật'
);

-- =====================================================================
-- 3. BẢNG LOẠI CÂU HỎI
-- =====================================================================
CREATE TABLE loaicauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã loại câu hỏi',
    ma VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã loại (vd: read_aloud)',
    ten VARCHAR(100) NOT NULL COMMENT 'Tên loại câu hỏi',
    kynang ENUM('reading','listening','writing','speaking') NOT NULL COMMENT 'Kỹ năng',
    cachcham ENUM('auto','ai','manual') NOT NULL DEFAULT 'auto' COMMENT 'Cách chấm: auto=tự động, ai=AI, manual=thủ công',
    thoigian INT UNSIGNED DEFAULT NULL COMMENT 'Thời gian mặc định tính bằng giây',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=bật, 0=tắt'
);

-- =====================================================================
-- 4. BẢNG CÂU HỎI
-- =====================================================================
CREATE TABLE cauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi',
    loai_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã loại câu hỏi',
    danhmuc_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Mã danh mục',
    noidung TEXT NOT NULL COMMENT 'Nội dung câu hỏi',
    giaithich TEXT DEFAULT NULL COMMENT 'Giải thích đáp án',
    dokho TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Độ khó từ 1 đến 10',
    anh VARCHAR(255) DEFAULT NULL COMMENT 'Hình ảnh câu hỏi',
    audio VARCHAR(255) DEFAULT NULL COMMENT 'File âm thanh',
    thoigian INT UNSIGNED DEFAULT NULL COMMENT 'Thời gian làm câu tính bằng giây',
    diem DECIMAL(6,2) NOT NULL DEFAULT 1 COMMENT 'Điểm câu hỏi',
    trangthai ENUM('draft','review','published','archived') NOT NULL DEFAULT 'draft' COMMENT 'Trạng thái câu hỏi',
    nguoitao BIGINT UNSIGNED DEFAULT NULL COMMENT 'Người tạo câu hỏi',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',

    INDEX idx_cauhoi_loai (loai_id, dokho, trangthai),
    INDEX idx_cauhoi_danhmuc (danhmuc_id),

    FOREIGN KEY (loai_id) REFERENCES loaicauhoi(id),
    FOREIGN KEY (danhmuc_id) REFERENCES danhmuc(id) ON DELETE SET NULL,
    FOREIGN KEY (nguoitao) REFERENCES nguoidung(id) ON DELETE SET NULL,

    CHECK (dokho BETWEEN 1 AND 10)
);

-- =====================================================================
-- 5. BẢNG LỰA CHỌN (A/B/C/D) - câu trắc nghiệm
-- =====================================================================
CREATE TABLE luachon (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lựa chọn',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi',
    noidung TEXT NOT NULL COMMENT 'Nội dung lựa chọn',
    ladung TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 đúng, 0 sai',
    thutu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Thứ tự lựa chọn',

    INDEX idx_luachon_cauhoi (cauhoi_id),

    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 6. BẢNG ĐÁP ÁN - các dạng không phải trắc nghiệm
-- =====================================================================
CREATE TABLE dap_an (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã đáp án',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi',
    noidung TEXT NOT NULL COMMENT 'Nội dung đáp án',
    ladung TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 đúng, 0 sai',
    thutu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Thứ tự đáp án',

    INDEX idx_dapan_cauhoi (cauhoi_id),

    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 7. BẢNG CÂU HỎI CON - INTERACTIVE READING
-- =====================================================================
CREATE TABLE cauhoi_doc (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi con (đọc)',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi cha',
    loai ENUM('complete_sentence','complete_passage','highlight_answer','identify_idea','title_passage') NOT NULL COMMENT 'Loại câu hỏi con',
    noidung TEXT NOT NULL COMMENT 'Nội dung câu hỏi con',
    dapandung TEXT DEFAULT NULL COMMENT 'Đáp án đúng',
    giaithich TEXT DEFAULT NULL COMMENT 'Giải thích',
    thutu SMALLINT UNSIGNED NOT NULL COMMENT 'Thứ tự câu hỏi con',

    UNIQUE (cauhoi_id, thutu),

    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 8. BẢNG CÂU HỎI CON - INTERACTIVE LISTENING
-- =====================================================================
CREATE TABLE cauhoi_nghe (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi con (nghe)',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi cha',
    loai ENUM('listen_and_complete','listen_and_respond','summarise_conversation') NOT NULL COMMENT 'Loại câu hỏi con',
    noidung TEXT NOT NULL COMMENT 'Nội dung câu hỏi con',
    dapandung TEXT DEFAULT NULL COMMENT 'Đáp án đúng',
    giaithich TEXT DEFAULT NULL COMMENT 'Giải thích',
    thutu SMALLINT UNSIGNED NOT NULL COMMENT 'Thứ tự câu hỏi con',

    UNIQUE (cauhoi_id, thutu),

    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 9. BẢNG LỰA CHỌN CÂU HỎI CON (ĐỌC)
-- =====================================================================
CREATE TABLE luachon_doc (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lựa chọn câu con (đọc)',
    cauhoi_doc_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi con (cauhoi_doc)',
    noidung TEXT NOT NULL COMMENT 'Nội dung lựa chọn',
    ladung TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 đúng, 0 sai',
    thutu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Thứ tự',

    INDEX idx_luachondoc_cauhoidoc (cauhoi_doc_id),

    FOREIGN KEY (cauhoi_doc_id) REFERENCES cauhoi_doc(id) ON DELETE CASCADE
);

-- =====================================================================
-- 10. BẢNG LỰA CHỌN CÂU HỎI CON (NGHE)
-- =====================================================================
CREATE TABLE luachon_nghe (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lựa chọn câu con (nghe)',
    cauhoi_nghe_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi con (cauhoi_nghe)',
    noidung TEXT NOT NULL COMMENT 'Nội dung lựa chọn',
    ladung TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 đúng, 0 sai',
    thutu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Thứ tự',

    INDEX idx_luachonnghe_cauhoinghe (cauhoi_nghe_id),

    FOREIGN KEY (cauhoi_nghe_id) REFERENCES cauhoi_nghe(id) ON DELETE CASCADE
);

-- =====================================================================
-- 10. BẢNG LUYỆN TẬP
-- =====================================================================
CREATE TABLE luyentap (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lần luyện tập',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người luyện tập',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Câu hỏi',
    noidung LONGTEXT DEFAULT NULL COMMENT 'Nội dung trả lời',
    luachon_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Lựa chọn đã chọn',
    audio VARCHAR(255) DEFAULT NULL COMMENT 'File ghi âm',
    ladung TINYINT(1) DEFAULT NULL COMMENT '1=đúng, 0=sai, NULL=chưa chấm',
    diem DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm đạt được',
    trangthai_ai ENUM('pending','done','failed') DEFAULT NULL COMMENT 'Trạng thái AI: pending=chờ, done=xong, failed=lỗi',
    diem_ai DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm AI',
    nhanxet_ai TEXT DEFAULT NULL COMMENT 'Nhận xét AI',
    thoigianlam INT UNSIGNED DEFAULT NULL COMMENT 'Thời gian làm tính bằng giây',
    ngaylam DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày làm',

    INDEX idx_luyentap_user (nguoidung_id, ngaylam),
    INDEX idx_luyentap_cauhoi (cauhoi_id),
    INDEX idx_luyentap_luachon (luachon_id),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE,
    FOREIGN KEY (luachon_id) REFERENCES luachon(id) ON DELETE SET NULL
);

-- =====================================================================
-- 11. BẢNG ĐÁNH DẤU CÂU HỎI
-- =====================================================================
CREATE TABLE danhdau (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã đánh dấu',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người đánh dấu',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Câu hỏi được đánh dấu',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày đánh dấu',

    UNIQUE (nguoidung_id, cauhoi_id),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 12. BẢNG ĐỀ THI
-- =====================================================================
CREATE TABLE dethi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã đề thi',
    ten VARCHAR(150) NOT NULL COMMENT 'Tên đề thi',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    chedo ENUM('full','section') NOT NULL DEFAULT 'full' COMMENT 'Chế độ thi',
    kieu ENUM('fixed','adaptive') NOT NULL DEFAULT 'fixed' COMMENT 'Kiểu đề thi: fixed=cố định, adaptive=thích ứng',
    kynang ENUM('reading','listening','writing','speaking') DEFAULT NULL COMMENT 'Kỹ năng nếu thi từng phần',
    thoigian INT UNSIGNED NOT NULL COMMENT 'Thời gian thi tính bằng giây',
    tongdiem DECIMAL(7,2) NOT NULL DEFAULT 0 COMMENT 'Tổng điểm',
    trangthai ENUM('draft','published','hidden') NOT NULL DEFAULT 'draft' COMMENT 'Trạng thái đề',
    nguoitao BIGINT UNSIGNED DEFAULT NULL COMMENT 'Người tạo đề',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',

    FOREIGN KEY (nguoitao) REFERENCES nguoidung(id) ON DELETE SET NULL
);

-- =====================================================================
-- 13. BẢNG CÂU HỎI THUỘC ĐỀ
-- =====================================================================
CREATE TABLE dethicauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã chi tiết đề',
    dethi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã đề thi',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi',
    thutu SMALLINT UNSIGNED NOT NULL COMMENT 'Thứ tự câu hỏi',
    diem DECIMAL(6,2) NOT NULL DEFAULT 1 COMMENT 'Điểm câu hỏi',

    UNIQUE (dethi_id, cauhoi_id),
    UNIQUE (dethi_id, thutu),

    FOREIGN KEY (dethi_id) REFERENCES dethi(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 14. BẢNG LẦN THI
-- =====================================================================
CREATE TABLE lanthi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lần thi',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người làm bài',
    dethi_id BIGINT UNSIGNED NOT NULL COMMENT 'Đề thi',
    dokho_hientai DECIMAL(4,2) NOT NULL DEFAULT 5.00 COMMENT 'Độ khó adaptive hiện tại',
    batdau DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian bắt đầu',
    ketthuc DATETIME DEFAULT NULL COMMENT 'Thời gian kết thúc',
    trangthai ENUM('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress' COMMENT 'Trạng thái lần thi',

    literacy SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Literacy (0-40)',
    comprehension SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Comprehension (0-40)',
    conversation SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Conversation (0-40)',
    production SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Production (0-40)',

    tongdiem SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm tổng DET 10-160',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',

    INDEX idx_lanthi_user (nguoidung_id, trangthai),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id),
    FOREIGN KEY (dethi_id) REFERENCES dethi(id)
);

-- =====================================================================
-- 15. BẢNG CÂU HỎI CỦA LẦN THI
-- =====================================================================
CREATE TABLE lanthicauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi lần thi',
    lanthi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã lần thi',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi',
    thutu SMALLINT UNSIGNED NOT NULL COMMENT 'Thứ tự xuất hiện',
    dokho_lucra DECIMAL(4,2) DEFAULT NULL COMMENT 'Độ khó lúc chọn câu',
    diemtoida DECIMAL(6,2) NOT NULL DEFAULT 1 COMMENT 'Điểm tối đa',
    thoigian_cap DATETIME DEFAULT NULL COMMENT 'Thời gian cấp câu hỏi',
    thoigian_tra_loi DATETIME DEFAULT NULL COMMENT 'Thời gian trả lời',
    datraloi TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=đã trả lời, 0=chưa',

    UNIQUE (lanthi_id, cauhoi_id),
    UNIQUE (lanthi_id, thutu),
    INDEX idx_lanthicauhoi_cauhoi (cauhoi_id),

    FOREIGN KEY (lanthi_id) REFERENCES lanthi(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id)
);

-- =====================================================================
-- 16. BẢNG CÂU TRẢ LỜI
-- =====================================================================
CREATE TABLE traloi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu trả lời',
    lanthicauhoi_id BIGINT UNSIGNED NOT NULL UNIQUE COMMENT 'Câu hỏi thuộc lần thi',
    noidung LONGTEXT DEFAULT NULL COMMENT 'Nội dung trả lời',
    luachon_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Lựa chọn được chọn',
    audio VARCHAR(255) DEFAULT NULL COMMENT 'File ghi âm',
    ladung TINYINT(1) DEFAULT NULL COMMENT '1=đúng, 0=sai, NULL=chưa chấm',
    diem DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm đạt được',
    trangthai_ai ENUM('pending','done','failed') DEFAULT NULL COMMENT 'Trạng thái chấm AI',
    diem_ai DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm AI',
    nhanxet_ai TEXT DEFAULT NULL COMMENT 'Nhận xét AI',
    thoigian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian trả lời',

    INDEX idx_traloi_luachon (luachon_id),

    FOREIGN KEY (lanthicauhoi_id) REFERENCES lanthicauhoi(id) ON DELETE CASCADE,
    FOREIGN KEY (luachon_id) REFERENCES luachon(id) ON DELETE SET NULL
);

-- =====================================================================
-- 17. BẢNG CHẤM ĐIỂM (có cột loai phân biệt Speaking/Writing)
-- =====================================================================
CREATE TABLE chamdiem (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã chấm điểm',
    traloi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu trả lời',
    nguoicham BIGINT UNSIGNED NOT NULL COMMENT 'Người chấm',
    loai ENUM('speaking','writing') NOT NULL COMMENT 'Loại bài: speaking=nói, writing=viết',
    nguphap DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm ngữ pháp',
    tuvung DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm từ vựng',
    luloat DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm lưu loát (Speaking)',
    phatam DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm phát âm (Speaking)',
    lienquan DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm đúng chủ đề',
    machlac DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm mạch lạc',
    nhanxet TEXT DEFAULT NULL COMMENT 'Nhận xét',
    tongdiem DECIMAL(6,2) NOT NULL DEFAULT 0 COMMENT 'Tổng điểm chấm',
    ngaycham DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày chấm',

    UNIQUE (traloi_id, nguoicham),

    FOREIGN KEY (traloi_id) REFERENCES traloi(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoicham) REFERENCES nguoidung(id)
);

-- =====================================================================
-- 18. BẢNG VI PHẠM
-- =====================================================================
CREATE TABLE vipham (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã vi phạm',
    lanthi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã lần thi',
    loai ENUM('out_tab','window_change','disconnect','early_submit','timeout','other') NOT NULL COMMENT 'Loại vi phạm',
    nguon ENUM('auto','self_report','admin') NOT NULL DEFAULT 'auto' COMMENT 'Nguồn: auto=tự động, self_report=tự báo cáo, admin=admin ghi nhận',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    thoigian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian vi phạm',

    INDEX idx_vipham_lanthi (lanthi_id, thoigian),

    FOREIGN KEY (lanthi_id) REFERENCES lanthi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 19. BẢNG CHỨNG CHỈ
-- =====================================================================
CREATE TABLE chungchi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã chứng chỉ',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người nhận',
    lanthi_id BIGINT UNSIGNED NOT NULL COMMENT 'Lần thi',
    ma VARCHAR(100) NOT NULL UNIQUE COMMENT 'Mã chứng chỉ',
    tongdiem SMALLINT UNSIGNED NOT NULL COMMENT 'Điểm tổng',
    literacy SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Literacy',
    comprehension SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Comprehension',
    conversation SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Conversation',
    production SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Production',
    trinhdo VARCHAR(50) DEFAULT NULL COMMENT 'Trình độ đánh giá',
    ngaycap DATETIME DEFAULT NULL COMMENT 'Ngày cấp',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',

    UNIQUE (lanthi_id),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE,
    FOREIGN KEY (lanthi_id) REFERENCES lanthi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 20. BẢNG NHẬT KÝ AI
-- =====================================================================
CREATE TABLE ailog (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã nhật ký AI',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người sử dụng AI',
    loai ENUM('speaking','writing','grammar','translate','rewrite','chat')
        NOT NULL COMMENT 'Loại yêu cầu',
    dauvao MEDIUMTEXT DEFAULT NULL COMMENT 'Dữ liệu đầu vào',
    daura MEDIUMTEXT DEFAULT NULL COMMENT 'Kết quả AI',
    model VARCHAR(100) DEFAULT NULL COMMENT 'Model AI',
    token_vao INT UNSIGNED DEFAULT NULL COMMENT 'Token đầu vào',
    token_ra INT UNSIGNED DEFAULT NULL COMMENT 'Token đầu ra',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày sử dụng',

    INDEX idx_ailog_user (nguoidung_id, ngaytao),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE
);

-- =====================================================================
-- 21. BẢNG LƯỢT SỬ DỤNG AI
-- =====================================================================
CREATE TABLE aisudung (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã sử dụng AI',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người dùng',
    ngay DATE NOT NULL COMMENT 'Ngày sử dụng',
    solan INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số lần gọi AI',

    UNIQUE (nguoidung_id, ngay),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE
);

-- =====================================================================
-- 22. BẢNG TIẾN ĐỘ HỌC TẬP
-- =====================================================================
CREATE TABLE tiendo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã tiến độ',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người dùng',
    loai_id BIGINT UNSIGNED NOT NULL COMMENT 'Loại câu hỏi',
    tongso INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Tổng số câu đã làm',
    sodung INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số câu đúng',
    diemtrungbinh DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm trung bình',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',

    UNIQUE (nguoidung_id, loai_id),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE,
    FOREIGN KEY (loai_id) REFERENCES loaicauhoi(id) ON DELETE CASCADE
);

-- =====================================================================
-- 23. BẢNG CHUỖI NGÀY HỌC
-- =====================================================================
CREATE TABLE chuoingay (
    nguoidung_id BIGINT UNSIGNED PRIMARY KEY COMMENT 'Người dùng',
    hientai INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Chuỗi ngày hiện tại',
    dainhat INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Chuỗi ngày dài nhất',
    ngaygannhat DATE DEFAULT NULL COMMENT 'Ngày hoạt động gần nhất',

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE
);

-- =====================================================================
-- GHI CHÚ THIẾT KẾ
-- =====================================================================
-- 1. Câu hỏi con được tách thành 2 bảng riêng:
--    - `cauhoi_doc` (Interactive Reading): 5 loại câu hỏi con
--    - `cauhoi_nghe` (Interactive Listening): 3 loại câu hỏi con
--
-- 2. Lựa chọn câu hỏi con cũng được tách thành 2 bảng:
--    - `luachon_doc` → trỏ `cauhoi_doc(id)`
--    - `luachon_nghe` → trỏ `cauhoi_nghe(id)`
--
-- 3. Bảng `chamdiem` có cột `loai` phân biệt Speaking/Writing:
--    - Writing: dùng nguphap, tuvung, lienquan, machlac
--    - Speaking: dùng nguphap, tuvung, luloat, phatam, machlac
--    → Các cột không dùng sẽ để NULL.
-- =====================================================================

-- Xem tất cả bảng
SHOW TABLES;
