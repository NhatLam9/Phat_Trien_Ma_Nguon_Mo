CREATE DATABASE duoling_csdl_remake
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE duoling_csdl_remake;

-- 1. Bảng người dùng
CREATE TABLE nguoidung (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã người dùng',
    hoten VARCHAR(100) NOT NULL COMMENT 'Họ và tên',
    email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email đăng nhập',
    email_xacthuc DATETIME DEFAULT NULL COMMENT 'Thời gian xác thực email',
    matkhau VARCHAR(255) DEFAULT NULL COMMENT 'Mật khẩu đã mã hóa',
    google_id VARCHAR(150) DEFAULT NULL UNIQUE COMMENT 'Mã tài khoản Google',
    vaitro ENUM('admin', 'user') NOT NULL DEFAULT 'user' COMMENT 'Vai trò',
    anh VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
    muctieu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Mục tiêu điểm DET',
    trangthai ENUM('hoatdong', 'khoa') NOT NULL DEFAULT 'hoatdong' COMMENT 'Trạng thái',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật'
);

-- 2. Bảng danh mục
CREATE TABLE danhmuc (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã danh mục',
    ten VARCHAR(100) NOT NULL COMMENT 'Tên chủ đề',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật'
);

-- 3. Bảng loại câu hỏi
CREATE TABLE loaicauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã loại câu hỏi',
    ma VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã loại',
    ten VARCHAR(100) NOT NULL COMMENT 'Tên loại câu hỏi',
    kynang ENUM('reading','listening','writing','speaking') NOT NULL COMMENT 'Kỹ năng',
    cachcham ENUM('tudong','ai','thugcong') NOT NULL DEFAULT 'tudong' COMMENT 'Cách chấm điểm',
    thoigian INT UNSIGNED DEFAULT NULL COMMENT 'Thời gian mặc định tính bằng giây',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    trangthai ENUM('hoatdong','an') NOT NULL DEFAULT 'hoatdong' COMMENT 'Trạng thái'
);

-- 4. Bảng câu hỏi
CREATE TABLE cauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi',
    loai_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã loại câu hỏi',
    danhmuc_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Mã danh mục',
    noidung TEXT NOT NULL COMMENT 'Nội dung câu hỏi',
    giaithich TEXT DEFAULT NULL COMMENT 'Giải thích đáp án',
    dokho TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Độ khó từ 1 đến 10',
    anh VARCHAR(255) DEFAULT NULL COMMENT 'Hình ảnh câu hỏi',
    audio VARCHAR(255) DEFAULT NULL COMMENT 'File âm thanh',
    dapandung TEXT DEFAULT NULL COMMENT 'Đáp án đúng nếu có',
    thoigian INT UNSIGNED DEFAULT NULL COMMENT 'Thời gian làm câu tính bằng giây',
    diem DECIMAL(6,2) NOT NULL DEFAULT 1 COMMENT 'Điểm câu hỏi',
    trangthai ENUM('nhap','choduyet','hoatdong','luutru') NOT NULL DEFAULT 'nhap' COMMENT 'Trạng thái câu hỏi',
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

-- 5. Bảng lựa chọn
CREATE TABLE luachon (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lựa chọn',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi',
    noidung TEXT NOT NULL COMMENT 'Nội dung lựa chọn',
    ladung BOOLEAN NOT NULL DEFAULT FALSE COMMENT '1 đúng, 0 sai',
    thutu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Thứ tự lựa chọn',

    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- 6. Bảng câu hỏi con
CREATE TABLE cauhoicon (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi con',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi cha',
    nhom ENUM('interactive_reading','interactive_listening') NOT NULL COMMENT 'Nhóm câu hỏi tương tác',
    loai VARCHAR(50) NOT NULL COMMENT 'Loại câu hỏi con',
    noidung TEXT NOT NULL COMMENT 'Nội dung câu hỏi con',
    dapandung TEXT DEFAULT NULL COMMENT 'Đáp án đúng',
    giaithich TEXT DEFAULT NULL COMMENT 'Giải thích',
    thutu SMALLINT UNSIGNED NOT NULL COMMENT 'Thứ tự câu hỏi con',

    UNIQUE (cauhoi_id, thutu),

    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- 7. Bảng lựa chọn câu hỏi con
CREATE TABLE luachoncon (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lựa chọn câu con',
    cauhoicon_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi con',
    noidung TEXT NOT NULL COMMENT 'Nội dung lựa chọn',
    ladung BOOLEAN NOT NULL DEFAULT FALSE COMMENT '1 đúng, 0 sai',
    thutu SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Thứ tự',

    FOREIGN KEY (cauhoicon_id) REFERENCES cauhoicon(id) ON DELETE CASCADE
);

-- 8. Bảng luyện tập
CREATE TABLE luyentap (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lần luyện tập',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người luyện tập',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Câu hỏi',
    noidung LONGTEXT DEFAULT NULL COMMENT 'Nội dung trả lời',
    luachon_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Lựa chọn đã chọn',
    audio VARCHAR(255) DEFAULT NULL COMMENT 'File ghi âm',
    ladung BOOLEAN DEFAULT NULL COMMENT 'Đúng hoặc sai',
    diem DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm đạt được',
    trangthai_ai ENUM('choxuly','hoanthanh','loi') DEFAULT NULL COMMENT 'Trạng thái AI',
    diem_ai DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm AI',
    nhanxet_ai TEXT DEFAULT NULL COMMENT 'Nhận xét AI',
    thoigianlam INT UNSIGNED DEFAULT NULL COMMENT 'Thời gian làm tính bằng giây',
    ngaylam DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày làm',

    INDEX idx_luyentap_user (nguoidung_id, ngaylam),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE,
    FOREIGN KEY (luachon_id) REFERENCES luachon(id) ON DELETE SET NULL
);

-- 9. Bảng đánh dấu câu hỏi
CREATE TABLE danhdau (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã đánh dấu',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người đánh dấu',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Câu hỏi được đánh dấu',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày đánh dấu',

    UNIQUE (nguoidung_id, cauhoi_id),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id) ON DELETE CASCADE
);

-- 10. Bảng đề thi
CREATE TABLE dethi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã đề thi',
    ten VARCHAR(150) NOT NULL COMMENT 'Tên đề thi',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    chedo ENUM('full','section') NOT NULL DEFAULT 'full' COMMENT 'Chế độ thi',
    kieu ENUM('codinh','adaptive') NOT NULL DEFAULT 'codinh' COMMENT 'Kiểu đề thi',
    kynang ENUM('reading','listening','writing','speaking') DEFAULT NULL COMMENT 'Kỹ năng nếu thi từng phần',
    thoigian INT UNSIGNED NOT NULL COMMENT 'Thời gian thi tính bằng phút',
    tongdiem DECIMAL(7,2) NOT NULL DEFAULT 0 COMMENT 'Tổng điểm',
    trangthai ENUM('nhap','hoatdong','an') NOT NULL DEFAULT 'nhap' COMMENT 'Trạng thái đề',
    nguoitao BIGINT UNSIGNED DEFAULT NULL COMMENT 'Người tạo đề',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    ngaycapnhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',

    FOREIGN KEY (nguoitao) REFERENCES nguoidung(id) ON DELETE SET NULL
);

-- 11. Bảng câu hỏi thuộc đề
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

-- 12. Bảng lần thi
CREATE TABLE lanthi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã lần thi',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người làm bài',
    dethi_id BIGINT UNSIGNED NOT NULL COMMENT 'Đề thi',
    dokho_hientai DECIMAL(4,2) NOT NULL DEFAULT 5.00 COMMENT 'Độ khó adaptive hiện tại',
    batdau DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian bắt đầu',
    ketthuc DATETIME DEFAULT NULL COMMENT 'Thời gian kết thúc',
    trangthai ENUM('batdau','dangthi','danop','dangcham','hoanthanh','huy')
        NOT NULL DEFAULT 'batdau' COMMENT 'Trạng thái lần thi',

    diemdoc SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Reading',
    diemnghe SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Listening',
    diemviet SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Writing',
    diemnoi SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Speaking',

    literacy SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Literacy',
    comprehension SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Comprehension',
    conversation SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Conversation',
    production SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm Production',

    tongdiem SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Điểm tổng DET 10-160',
    ngaytao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',

    INDEX idx_lanthi_user (nguoidung_id, trangthai),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id),
    FOREIGN KEY (dethi_id) REFERENCES dethi(id)
);

-- 13. Bảng câu hỏi của từng lần thi
CREATE TABLE lanthicauhoi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu hỏi lần thi',
    lanthi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã lần thi',
    cauhoi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu hỏi',
    thutu SMALLINT UNSIGNED NOT NULL COMMENT 'Thứ tự xuất hiện',
    dokho_lucra DECIMAL(4,2) DEFAULT NULL COMMENT 'Độ khó lúc chọn câu',
    diemtoida DECIMAL(6,2) NOT NULL DEFAULT 1 COMMENT 'Điểm tối đa',
    thoigian_cap DATETIME DEFAULT NULL COMMENT 'Thời gian cấp câu hỏi',
    datraloi BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Đã trả lời hay chưa',

    UNIQUE (lanthi_id, cauhoi_id),
    UNIQUE (lanthi_id, thutu),

    FOREIGN KEY (lanthi_id) REFERENCES lanthi(id) ON DELETE CASCADE,
    FOREIGN KEY (cauhoi_id) REFERENCES cauhoi(id)
);

-- 14. Bảng câu trả lời
CREATE TABLE traloi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã câu trả lời',
    lanthicauhoi_id BIGINT UNSIGNED NOT NULL UNIQUE COMMENT 'Câu hỏi thuộc lần thi',
    noidung LONGTEXT DEFAULT NULL COMMENT 'Nội dung trả lời',
    luachon_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'Lựa chọn được chọn',
    audio VARCHAR(255) DEFAULT NULL COMMENT 'File ghi âm',
    ladung BOOLEAN DEFAULT NULL COMMENT 'Đúng hoặc sai',
    diem DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm đạt được',
    trangthai_ai ENUM('choxuly','hoanthanh','loi') DEFAULT NULL COMMENT 'Trạng thái chấm AI',
    diem_ai DECIMAL(6,2) DEFAULT NULL COMMENT 'Điểm AI',
    nhanxet_ai TEXT DEFAULT NULL COMMENT 'Nhận xét AI',
    thoigian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian trả lời',

    FOREIGN KEY (lanthicauhoi_id) REFERENCES lanthicauhoi(id) ON DELETE CASCADE,
    FOREIGN KEY (luachon_id) REFERENCES luachon(id) ON DELETE SET NULL
);

-- 15. Bảng chấm điểm
CREATE TABLE chamdiem (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã chấm điểm',
    traloi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã câu trả lời',
    nguoicham BIGINT UNSIGNED NOT NULL COMMENT 'Người chấm',
    nguphap DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm ngữ pháp',
    tuvung DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm từ vựng',
    luloat DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm lưu loát',
    lienquan DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm đúng chủ đề',
    machlac DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm mạch lạc',
    nhanxet TEXT DEFAULT NULL COMMENT 'Nhận xét',
    tongdiem DECIMAL(6,2) NOT NULL DEFAULT 0 COMMENT 'Tổng điểm chấm',
    ngaycham DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày chấm',

    UNIQUE (traloi_id, nguoicham),

    FOREIGN KEY (traloi_id) REFERENCES traloi(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoicham) REFERENCES nguoidung(id)
);

-- 16. Bảng vi phạm
CREATE TABLE vipham (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã vi phạm',
    lanthi_id BIGINT UNSIGNED NOT NULL COMMENT 'Mã lần thi',
    loai VARCHAR(50) NOT NULL COMMENT 'Loại vi phạm',
    mota TEXT DEFAULT NULL COMMENT 'Mô tả',
    thoigian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian vi phạm',

    INDEX idx_vipham_lanthi (lanthi_id, thoigian),

    FOREIGN KEY (lanthi_id) REFERENCES lanthi(id) ON DELETE CASCADE
);

-- 17. Bảng chứng chỉ
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

-- 18. Bảng nhật ký AI
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

-- 19. Bảng lượt sử dụng AI
CREATE TABLE aisudung (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã sử dụng AI',
    nguoidung_id BIGINT UNSIGNED NOT NULL COMMENT 'Người dùng',
    ngay DATE NOT NULL COMMENT 'Ngày sử dụng',
    solan INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số lần gọi AI',

    UNIQUE (nguoidung_id, ngay),

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE
);

-- 20. Bảng tiến độ học tập
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

-- 21. Bảng chuỗi ngày học
CREATE TABLE chuoingay (
    nguoidung_id BIGINT UNSIGNED PRIMARY KEY COMMENT 'Người dùng',
    hientai INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Chuỗi ngày hiện tại',
    dainhat INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Chuỗi ngày dài nhất',
    ngaygannhat DATE DEFAULT NULL COMMENT 'Ngày hoạt động gần nhất',

    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE CASCADE
);

-- 22. Xem tất cả bảng
SHOW TABLES;
