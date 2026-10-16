-- =====================================================================
-- DET PRACTICE - SCHEMA MySQL 8.0
-- =====================================================================

CREATE DATABASE IF NOT EXISTS det_practice
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE det_practice;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. TÀI KHOẢN
-- ---------------------------------------------------------------------
CREATE TABLE users (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  name              VARCHAR(100) NOT NULL,                      -- Họ tên người dùng
  email             VARCHAR(100) NOT NULL UNIQUE,              -- Email đăng nhập (duy nhất)
  email_verified_at TIMESTAMP NULL,                            -- Thời gian xác minh email (NULL = chưa xác minh)
  password          VARCHAR(255) NULL,                          -- Mật khẩu bcrypt (NULL nếu chỉ dùng Google)
  google_id         VARCHAR(100) NULL UNIQUE,                  -- ID tài khoản Google (duy nhất)
  role              ENUM('user','admin') NOT NULL DEFAULT 'user', -- Vai trò: user=học viên, admin=quản trị
  avatar            VARCHAR(255) NULL,                         -- Đường dẫn ảnh đại diện
  target_score      SMALLINT UNSIGNED NULL,                    -- Mục tiêu điểm DET (10-160)
  is_active         TINYINT(1) NOT NULL DEFAULT 1,             -- 1=hoạt động, 0=bị khóa
  remember_token    VARCHAR(100) NULL,                         -- Token ghi nhớ đăng nhập
  created_at        TIMESTAMP NULL,                            -- Thời gian tạo tài khoản
  updated_at        TIMESTAMP NULL                             -- Thời gian cập nhật cuối
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. NGÂN HÀNG CÂU HỎI
-- ---------------------------------------------------------------------
CREATE TABLE categories (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  name        VARCHAR(100) NOT NULL,                      -- Tên chủ đề (vd: Đời sống, Khoa học)
  description TEXT NULL,                                   -- Mô tả chủ đề
  created_at  TIMESTAMP NULL,                              -- Thời gian tạo
  updated_at  TIMESTAMP NULL                               -- Thời gian cập nhật cuối
) ENGINE=InnoDB;

CREATE TABLE question_types (
  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  code           VARCHAR(50)  NOT NULL UNIQUE,               -- Mã dạng bài (vd: read_aloud, listen_type)
  name           VARCHAR(100) NOT NULL,                      -- Tên hiển thị (vd: Read Aloud)
  skill          ENUM('reading','writing','listening','speaking') NOT NULL, -- Nhóm kỹ năng
  scoring_method ENUM('auto','ai','manual') NOT NULL DEFAULT 'auto', -- Cách chấm: auto=tự động, ai=AI, manual=thủ công
  time_limit_s   INT UNSIGNED NULL,                           -- Giới hạn thời gian mặc định (giây)
  description    TEXT NULL,                                  -- Mô tả dạng bài
  is_active      TINYINT(1) NOT NULL DEFAULT 1              -- 1=bật, 0=tắt dạng bài
) ENGINE=InnoDB;

CREATE TABLE questions (
  id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  question_type_id BIGINT UNSIGNED NOT NULL,                  -- Dạng bài (FK → question_types)
  category_id      BIGINT UNSIGNED NULL,                      -- Chủ đề (FK → categories)
  content          TEXT NOT NULL,                             -- Nội dung câu hỏi / đoạn văn
  options          JSON NULL,                                 -- Các lựa chọn (nếu có)
  answer           JSON NULL,                                 -- Đáp án (cấu trúc tùy dạng bài)
  explanation      TEXT NULL,                                 -- Giải thích đáp án
  difficulty       TINYINT UNSIGNED NOT NULL DEFAULT 5,      -- Độ khó 1(dễ)-10(khó), dùng cho adaptive
  image_url        VARCHAR(255) NULL,                         -- Hình ảnh đề bài
  audio_url        VARCHAR(255) NULL,                         -- File âm thanh (bài nghe/nói)
  status           ENUM('draft','review','published','archived') NOT NULL DEFAULT 'draft', -- Trạng thái câu hỏi
  created_by       BIGINT UNSIGNED NULL,                      -- Người tạo (FK → users)
  created_at       TIMESTAMP NULL,                            -- Thời gian tạo
  updated_at       TIMESTAMP NULL,                            -- Thời gian cập nhật cuối
  INDEX idx_q_type_diff (question_type_id, difficulty, status), -- Index tìm câu theo dạng + độ khó
  INDEX idx_q_category (category_id),                         -- Index tìm câu theo chủ đề
  CONSTRAINT fk_q_type     FOREIGN KEY (question_type_id) REFERENCES question_types(id),
  CONSTRAINT fk_q_category FOREIGN KEY (category_id)      REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_q_creator  FOREIGN KEY (created_by)       REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2.1 CÂU HỎI CON (INTERACTIVE READING / LISTENING)
-- ---------------------------------------------------------------------
-- Interactive Reading: 5 dạng câu hỏi con
CREATE TABLE interactive_reading_questions (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  question_id BIGINT UNSIGNED NOT NULL,                    -- Câu hỏi cha (FK → questions)
  position    TINYINT UNSIGNED NOT NULL,                   -- Thứ tự câu con (1, 2, 3, 4, 5)
  type        ENUM('complete_sentence','complete_passage','highlight_answer','identify_idea','title_passage') NOT NULL, -- Loại câu hỏi con
  text        TEXT NOT NULL,                               -- Nội dung câu hỏi con
  options     JSON NULL,                                   -- Các lựa chọn (nếu có)
  answer      TEXT NULL,                                   -- Đáp án
  explanation TEXT NULL,                                   -- Giải thích
  created_at  TIMESTAMP NULL,                              -- Thời gian tạo
  updated_at  TIMESTAMP NULL,                              -- Thời gian cập nhật cuối
  INDEX idx_irq_question (question_id, position),          -- Index tìm câu con theo câu cha + thứ tự
  CONSTRAINT fk_irq_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Interactive Listening: 3 phần
CREATE TABLE interactive_listening_questions (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  question_id BIGINT UNSIGNED NOT NULL,                    -- Câu hỏi cha (FK → questions)
  position    TINYINT UNSIGNED NOT NULL,                   -- Thứ tự phần (1, 2, 3)
  type        ENUM('listen_and_complete','listen_and_respond','summarise_conversation') NOT NULL, -- Loại phần
  text        TEXT NOT NULL,                               -- Nội dung câu hỏi / đoạn hội thoại
  options     JSON NULL,                                   -- Các lựa chọn (nếu có)
  answer      TEXT NULL,                                   -- Đáp án
  explanation TEXT NULL,                                   -- Giải thích
  created_at  TIMESTAMP NULL,                              -- Thời gian tạo
  updated_at  TIMESTAMP NULL,                              -- Thời gian cập nhật cuối
  INDEX idx_ilq_question (question_id, position),          -- Index tìm phần theo câu cha + thứ tự
  CONSTRAINT fk_ilq_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. BÀI LÀM LUYỆN TẬP
-- ---------------------------------------------------------------------
CREATE TABLE practice_attempts (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id       BIGINT UNSIGNED NOT NULL,                    -- Người làm bài (FK → users)
  question_id   BIGINT UNSIGNED NOT NULL,                    -- Câu hỏi (FK → questions)
  user_answer   TEXT NULL,                                   -- Câu trả lời (text)
  user_audio    VARCHAR(255) NULL,                           -- Đường dẫn file ghi âm (nếu là bài nói)
  is_correct    TINYINT(1) NULL,                             -- 1=đúng, 0=sai, NULL=chưa chấm (AI)
  ai_status     ENUM('pending','done','failed') NULL,        -- Trạng thái chấm AI: pending=chờ, done=xong, failed=lỗi
  ai_score      DECIMAL(5,2) NULL,                           -- Điểm AI (0-100)
  ai_feedback   TEXT NULL,                                   -- Nhận xét/gợi ý từ AI
  time_spent_s  INT UNSIGNED NULL,                           -- Thời gian làm bài (giây)
  created_at    TIMESTAMP NULL,                              -- Thời gian nộp bài
  INDEX idx_pa_user (user_id, created_at),                   -- Index lịch sử theo người + thời gian
  INDEX idx_pa_question (question_id),                       -- Index tìm theo câu hỏi
  CONSTRAINT fk_pa_user     FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_pa_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookmarks (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id     BIGINT UNSIGNED NOT NULL,                    -- Người đánh dấu (FK → users)
  question_id BIGINT UNSIGNED NOT NULL,                    -- Câu hỏi được đánh dấu (FK → questions)
  created_at  TIMESTAMP NULL,                              -- Thời gian đánh dấu
  UNIQUE KEY uq_bookmark (user_id, question_id),            -- Không đánh dấu trùng 2 lần
  CONSTRAINT fk_bm_user     FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_bm_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. MOCK TEST
-- ---------------------------------------------------------------------
CREATE TABLE tests (
  id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id             BIGINT UNSIGNED NOT NULL,              -- Người thi (FK → users)
  mode                ENUM('full','section') NOT NULL DEFAULT 'full', -- Chế độ: full=cả bài thi, section=từng phần
  status              ENUM('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress', -- Trạng thái bài thi
  current_difficulty  DECIMAL(4,2) NOT NULL DEFAULT 5.00,    -- Độ khó hiện tại (adaptive, 1-10)
  total_score         SMALLINT UNSIGNED NULL,                -- Điểm tổng (thang 10-160)
  literacy_score      SMALLINT UNSIGNED NULL,                -- Điểm Literacy (0-40)
  comprehension_score SMALLINT UNSIGNED NULL,                -- Điểm Comprehension (0-40)
  conversation_score  SMALLINT UNSIGNED NULL,                -- Điểm Conversation (0-40)
  production_score    SMALLINT UNSIGNED NULL,                -- Điểm Production (0-40)
  time_limit_s        INT UNSIGNED NOT NULL DEFAULT 3600,    -- Giới hạn thời gian tổng (giây)
  started_at          TIMESTAMP NULL,                        -- Thời gian bắt đầu thi
  completed_at        TIMESTAMP NULL,                        -- Thời gian kết thúc thi
  INDEX idx_t_user (user_id, status),                       -- Index tìm bài thi theo người + trạng thái
  CONSTRAINT fk_t_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE test_questions (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  test_id      BIGINT UNSIGNED NOT NULL,                    -- Bài thi (FK → tests)
  question_id  BIGINT UNSIGNED NOT NULL,                    -- Câu hỏi (FK → questions)
  position     SMALLINT UNSIGNED NOT NULL,                  -- Thứ tự câu trong bài thi (1, 2, 3...)
  user_answer  TEXT NULL,                                   -- Câu trả lời
  user_audio   VARCHAR(255) NULL,                           -- File ghi âm (nếu bài nói)
  is_correct   TINYINT(1) NULL,                             -- 1=đúng, 0=sai, NULL=chưa chấm
  ai_status    ENUM('pending','done','failed') NULL,        -- Trạng thái chấm AI
  ai_score     DECIMAL(5,2) NULL,                           -- Điểm AI
  ai_feedback  TEXT NULL,                                   -- Nhận xét AI
  served_at    TIMESTAMP NULL,                              -- Thời gian câu được cấp
  answered_at  TIMESTAMP NULL,                              -- Thời gian trả lời
  UNIQUE KEY uq_tq_pos (test_id, position),                 -- Không trùng thứ tự câu
  INDEX idx_tq_question (question_id),                      -- Index tìm theo câu hỏi
  CONSTRAINT fk_tq_test     FOREIGN KEY (test_id)     REFERENCES tests(id)     ON DELETE CASCADE,
  CONSTRAINT fk_tq_question FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. CHỨNG CHỈ (CERTIFICATE)
-- ---------------------------------------------------------------------
CREATE TABLE certificates (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id           BIGINT UNSIGNED NOT NULL,              -- Người nhận (FK → users)
  test_id           BIGINT UNSIGNED NOT NULL,              -- Bài thi liên quan (FK → tests)
  certificate_code  VARCHAR(100) NULL UNIQUE,              -- Mã chứng chỉ (vd: DET-2026-00001)
  total_score       SMALLINT UNSIGNED NOT NULL,            -- Điểm tổng (10-160)
  literacy_score    SMALLINT UNSIGNED NULL,                -- Điểm Literacy
  comprehension_score SMALLINT UNSIGNED NULL,               -- Điểm Comprehension
  conversation_score SMALLINT UNSIGNED NULL,                -- Điểm Conversation
  production_score  SMALLINT UNSIGNED NULL,                -- Điểm Production
  level             VARCHAR(50) NULL,                      -- Cấp độ (vd: Advanced, Intermediate)
  issued_at         TIMESTAMP NULL,                        -- Ngày cấp
  created_at        TIMESTAMP NULL,                        -- Thời gian tạo
  INDEX idx_cert_user (user_id, issued_at),                -- Index tìm theo người + ngày cấp
  CONSTRAINT fk_cert_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_cert_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. AI (Correction + Tutor)
-- ---------------------------------------------------------------------
CREATE TABLE ai_logs (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id    BIGINT UNSIGNED NOT NULL,                    -- Người gọi AI (FK → users)
  type       ENUM('speaking','writing','grammar','translate','rewrite','chat') NOT NULL, -- Loại yêu cầu AI
  input      MEDIUMTEXT NULL,                             -- Dữ liệu đầu vào (text/đường dẫn audio)
  output     MEDIUMTEXT NULL,                             -- Kết quả AI trả về
  model      VARCHAR(50) NULL,                            -- Tên model (vd: gpt-4o)
  tokens_in  INT UNSIGNED NULL,                           -- Số token đầu vào
  tokens_out INT UNSIGNED NULL,                           -- Số token đầu ra
  created_at TIMESTAMP NULL,                             -- Thời gian gọi AI
  INDEX idx_ai_user (user_id, created_at),                -- Index lịch sử AI theo người + thời gian
  CONSTRAINT fk_ai_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Giới hạn lượt gọi AI theo ngày
CREATE TABLE ai_usage (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id    BIGINT UNSIGNED NOT NULL,                    -- Người dùng (FK → users)
  usage_date DATE NOT NULL,                               -- Ngày sử dụng
  call_count INT UNSIGNED NOT NULL DEFAULT 0,             -- Số lần gọi AI trong ngày
  UNIQUE KEY uq_ai_usage (user_id, usage_date),            -- Mỗi người 1 dòng/ngày
  CONSTRAINT fk_aiu_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. DASHBOARD
-- ---------------------------------------------------------------------
-- Tiến độ theo từng dạng bài
CREATE TABLE user_progress (
  id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Khóa chính tự tăng
  user_id          BIGINT UNSIGNED NOT NULL,              -- Người dùng (FK → users)
  question_type_id BIGINT UNSIGNED NOT NULL,              -- Dạng bài (FK → question_types)
  total_done       INT UNSIGNED NOT NULL DEFAULT 0,        -- Tổng số câu đã làm
  correct_count    INT UNSIGNED NOT NULL DEFAULT 0,        -- Số câu làm đúng
  avg_score        DECIMAL(5,2) NULL,                      -- Điểm trung bình
  updated_at       TIMESTAMP NULL,                        -- Thời gian cập nhật cuối
  UNIQUE KEY uq_up (user_id, question_type_id),            -- Mỗi người 1 dòng/dạng bài
  CONSTRAINT fk_up_user FOREIGN KEY (user_id)          REFERENCES users(id)          ON DELETE CASCADE,
  CONSTRAINT fk_up_type FOREIGN KEY (question_type_id) REFERENCES question_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Streak: một dòng mỗi người dùng
CREATE TABLE user_streaks (
  user_id         BIGINT UNSIGNED PRIMARY KEY,           -- Khóa chính = user_id
  current_streak  INT UNSIGNED NOT NULL DEFAULT 0,         -- Chuỗi ngày học hiện tại
  longest_streak  INT UNSIGNED NOT NULL DEFAULT 0,         -- Chuỗi dài nhất từ trước tới nay
  last_active_on  DATE NULL,                               -- Ngày học gần nhất
  CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- DỮ LIỆU MẪU
-- ---------------------------------------------------------------------
INSERT INTO question_types (code, name, skill, scoring_method) VALUES
('read_and_select',      'Read and Select',      'reading',   'auto'),
('fill_in_the_blanks',   'Fill in the Blanks',   'reading',   'auto'),
('read_and_complete',    'Read and Complete',    'reading',   'auto'),
('listen_and_type',      'Listen and Type',      'listening', 'auto'),
('interactive_reading',  'Interactive Reading',  'reading',   'auto'),
('interactive_listening','Interactive Listening','listening', 'ai'),
('read_aloud',           'Read Aloud',           'speaking',  'ai'),
('write_about_photo',    'Write About the Photo','writing',   'ai'),
('speak_about_photo',    'Speak About the Photo','speaking',  'ai'),
('interactive_writing',  'Interactive Writing',  'writing',   'ai'),
('read_then_speak',      'Read, Then Speak',     'speaking',  'ai'),
('listen_then_speak',    'Listen, Then Speak',   'speaking',  'ai'),
('writing_sample',       'Writing Sample',       'writing',   'ai'),
('speaking_sample',      'Speaking Sample',      'speaking',  'ai');

INSERT INTO categories (name) VALUES
('Đời sống'), ('Giáo dục'), ('Khoa học'), ('Công nghệ'), ('Môi trường'), ('Xã hội');
