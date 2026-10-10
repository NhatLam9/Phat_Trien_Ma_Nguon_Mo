-- =====================================================================
-- DET PRACTICE - SCHEMA MYSQL 8.0 (tham khảo)
-- Dựa trên mục 5 của "Kế hoạch dự án", đã chỉnh theo review:
--   * order -> sort_order (order là từ khóa MySQL)
--   * thêm practice_attempts (lưu bài làm chế độ Practice)
--   * thêm question_types (thay cho VARCHAR type)
--   * difficulty dạng số 1-10 (phục vụ mock test adaptive)
--   * thêm điểm thành phần DET (thang 10-160)
--   * user_progress theo (user, type); streak tách riêng
--   * thêm ai_usage (giới hạn lượt gọi AI để kiểm soát chi phí)
-- Kiểu id dùng BIGINT UNSIGNED để khớp migration mặc định của Laravel.
-- Các bảng hệ thống của Laravel (migrations, sessions, jobs,
-- password_reset_tokens...) sẽ do `php artisan migrate` tự tạo.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS det_practice
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE det_practice;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. TÀI KHOẢN
-- ---------------------------------------------------------------------
CREATE TABLE users (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(100) NOT NULL,
  email             VARCHAR(100) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password          VARCHAR(255) NULL,              -- NULL nếu chỉ đăng nhập Google
  google_id         VARCHAR(100) NULL UNIQUE,
  role              ENUM('user','admin') NOT NULL DEFAULT 'user',
  avatar            VARCHAR(255) NULL,
  target_score      SMALLINT UNSIGNED NULL,         -- mục tiêu điểm DET
  is_active         TINYINT(1) NOT NULL DEFAULT 1,  -- admin khóa tài khoản
  remember_token    VARCHAR(100) NULL,
  created_at        TIMESTAMP NULL,
  updated_at        TIMESTAMP NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. NGÂN HÀNG CÂU HỎI
-- ---------------------------------------------------------------------
CREATE TABLE categories (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  description TEXT NULL,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE question_types (
  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code           VARCHAR(50)  NOT NULL UNIQUE,      -- vd: read_aloud
  name           VARCHAR(100) NOT NULL,             -- tên hiển thị
  skill          ENUM('reading','writing','listening','speaking') NOT NULL,
  scoring_method ENUM('auto','ai','manual') NOT NULL DEFAULT 'auto',
  description    TEXT NULL,
  is_active      TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE questions (
  id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question_type_id BIGINT UNSIGNED NOT NULL,
  category_id      BIGINT UNSIGNED NULL,
  content          TEXT NOT NULL,                   -- đề bài / đoạn văn
  options          JSON NULL,                       -- lựa chọn (nếu có)
  answer           JSON NULL,                       -- đáp án; cấu trúc tùy dạng bài
  explanation      TEXT NULL,
  difficulty       TINYINT UNSIGNED NOT NULL DEFAULT 5, -- 1 (dễ) .. 10 (khó)
  image_url        VARCHAR(255) NULL,
  audio_url        VARCHAR(255) NULL,
  status           ENUM('draft','review','published') NOT NULL DEFAULT 'draft',
  created_by       BIGINT UNSIGNED NULL,
  created_at       TIMESTAMP NULL,
  updated_at       TIMESTAMP NULL,
  INDEX idx_q_type_diff (question_type_id, difficulty, status),
  INDEX idx_q_category (category_id),
  CONSTRAINT fk_q_type     FOREIGN KEY (question_type_id) REFERENCES question_types(id),
  CONSTRAINT fk_q_category FOREIGN KEY (category_id)      REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_q_creator  FOREIGN KEY (created_by)       REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB;

-- Bài làm ở chế độ Practice
CREATE TABLE practice_attempts (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       BIGINT UNSIGNED NOT NULL,
  question_id   BIGINT UNSIGNED NOT NULL,
  user_answer   TEXT NULL,
  user_audio    VARCHAR(255) NULL,                  -- file ghi âm của người dùng
  is_correct    TINYINT(1) NULL,                    -- NULL nếu chấm bằng AI
  ai_score      DECIMAL(5,2) NULL,
  ai_feedback   TEXT NULL,
  time_spent_s  INT UNSIGNED NULL,
  created_at    TIMESTAMP NULL,
  INDEX idx_pa_user (user_id, created_at),
  INDEX idx_pa_question (question_id),
  CONSTRAINT fk_pa_user     FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_pa_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookmarks (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     BIGINT UNSIGNED NOT NULL,
  question_id BIGINT UNSIGNED NOT NULL,
  created_at  TIMESTAMP NULL,
  UNIQUE KEY uq_bookmark (user_id, question_id),
  CONSTRAINT fk_bm_user     FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
  CONSTRAINT fk_bm_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. MOCK TEST
-- ---------------------------------------------------------------------
CREATE TABLE tests (
  id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id             BIGINT UNSIGNED NOT NULL,
  mode                ENUM('full','section') NOT NULL DEFAULT 'full',
  status              ENUM('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
  current_difficulty  DECIMAL(4,2) NOT NULL DEFAULT 5.00,   -- mức độ khó hiện tại (adaptive)
  total_score         SMALLINT UNSIGNED NULL,               -- thang 10-160
  literacy_score      SMALLINT UNSIGNED NULL,
  comprehension_score SMALLINT UNSIGNED NULL,
  conversation_score  SMALLINT UNSIGNED NULL,
  production_score    SMALLINT UNSIGNED NULL,
  time_limit_s        INT UNSIGNED NOT NULL DEFAULT 3600,
  started_at          TIMESTAMP NULL,
  completed_at        TIMESTAMP NULL,
  INDEX idx_t_user (user_id, status),
  CONSTRAINT fk_t_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE test_questions (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  test_id      BIGINT UNSIGNED NOT NULL,
  question_id  BIGINT UNSIGNED NOT NULL,
  position     SMALLINT UNSIGNED NOT NULL,             -- thứ tự câu trong bài thi
  user_answer  TEXT NULL,
  user_audio   VARCHAR(255) NULL,
  is_correct   TINYINT(1) NULL,
  ai_score     DECIMAL(5,2) NULL,
  ai_feedback  TEXT NULL,
  served_at    TIMESTAMP NULL,
  answered_at  TIMESTAMP NULL,
  UNIQUE KEY uq_tq_pos (test_id, position),
  INDEX idx_tq_question (question_id),
  CONSTRAINT fk_tq_test     FOREIGN KEY (test_id)     REFERENCES tests(id)     ON DELETE CASCADE,
  CONSTRAINT fk_tq_question FOREIGN KEY (question_id) REFERENCES questions(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. KHÓA HỌC
-- ---------------------------------------------------------------------
CREATE TABLE courses (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  description TEXT NULL,
  thumbnail   VARCHAR(255) NULL,
  level       ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE lessons (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id  BIGINT UNSIGNED NOT NULL,
  title      VARCHAR(200) NOT NULL,
  content    LONGTEXT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  video_url  VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_l_course (course_id, sort_order),
  CONSTRAINT fk_l_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE lesson_progress (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id      BIGINT UNSIGNED NOT NULL,
  lesson_id    BIGINT UNSIGNED NOT NULL,
  completed_at TIMESTAMP NULL,
  UNIQUE KEY uq_lp (user_id, lesson_id),
  CONSTRAINT fk_lp_user   FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  CONSTRAINT fk_lp_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. AI (Correction + Tutor)
-- ---------------------------------------------------------------------
CREATE TABLE ai_logs (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    BIGINT UNSIGNED NOT NULL,
  type       ENUM('speaking','writing','grammar','translate','rewrite','chat') NOT NULL,
  input      MEDIUMTEXT NULL,
  output     MEDIUMTEXT NULL,
  model      VARCHAR(50) NULL,
  tokens_in  INT UNSIGNED NULL,
  tokens_out INT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  INDEX idx_ai_user (user_id, created_at),
  CONSTRAINT fk_ai_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Giới hạn lượt gọi AI theo ngày
CREATE TABLE ai_usage (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    BIGINT UNSIGNED NOT NULL,
  usage_date DATE NOT NULL,
  call_count INT UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY uq_ai_usage (user_id, usage_date),
  CONSTRAINT fk_aiu_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. DASHBOARD
-- ---------------------------------------------------------------------
-- Tiến độ theo từng dạng bài
CREATE TABLE user_progress (
  id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id          BIGINT UNSIGNED NOT NULL,
  question_type_id BIGINT UNSIGNED NOT NULL,
  total_done       INT UNSIGNED NOT NULL DEFAULT 0,
  correct_count    INT UNSIGNED NOT NULL DEFAULT 0,
  avg_score        DECIMAL(5,2) NULL,
  updated_at       TIMESTAMP NULL,
  UNIQUE KEY uq_up (user_id, question_type_id),
  CONSTRAINT fk_up_user FOREIGN KEY (user_id)          REFERENCES users(id)          ON DELETE CASCADE,
  CONSTRAINT fk_up_type FOREIGN KEY (question_type_id) REFERENCES question_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Streak: một dòng mỗi người dùng
CREATE TABLE user_streaks (
  user_id         BIGINT UNSIGNED PRIMARY KEY,
  current_streak  INT UNSIGNED NOT NULL DEFAULT 0,
  longest_streak  INT UNSIGNED NOT NULL DEFAULT 0,
  last_active_on  DATE NULL,
  CONSTRAINT fk_us_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- DỮ LIỆU MẪU
-- Danh sách dạng bài DET dưới đây cần đối chiếu lại với tài liệu
-- Official Guide của Duolingo trước khi chốt.
-- ---------------------------------------------------------------------
INSERT INTO question_types (code, name, skill, scoring_method) VALUES
('read_and_complete',   'Read and Complete',   'reading',   'auto'),
('read_and_select',     'Read and Select',     'reading',   'auto'),
('listen_and_type',     'Listen and Type',     'listening', 'auto'),
('interactive_reading', 'Interactive Reading', 'reading',   'auto'),
('interactive_listening','Interactive Listening','listening','ai'),
('read_aloud',          'Read Aloud',          'speaking',  'ai'),
('write_about_photo',   'Write About the Photo','writing',  'ai'),
('speak_about_photo',   'Speak About the Photo','speaking', 'ai'),
('read_then_write',     'Read, Then Write',    'writing',   'ai'),
('read_then_speak',     'Read, Then Speak',    'speaking',  'ai'),
('listen_then_speak',   'Listen, Then Speak',  'speaking',  'ai'),
('writing_sample',      'Writing Sample',      'writing',   'ai'),
('speaking_sample',     'Speaking Sample',     'speaking',  'ai');

INSERT INTO categories (name) VALUES
('Đời sống'), ('Giáo dục'), ('Khoa học'), ('Công nghệ'), ('Môi trường'), ('Xã hội');
