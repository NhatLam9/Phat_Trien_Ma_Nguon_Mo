-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 16, 2026 lúc 04:53 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `duoling`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhoi`
--

CREATE TABLE `cauhoi` (
  `id` int(11) NOT NULL COMMENT 'Mã câu hỏi',
  `loai_id` int(11) NOT NULL COMMENT 'Mã loại câu hỏi',
  `noidung` text NOT NULL COMMENT 'Nội dung câu hỏi',
  `dokho` enum('de','trungbinh','kho') NOT NULL DEFAULT 'trungbinh' COMMENT 'Độ khó',
  `media` varchar(255) DEFAULT NULL COMMENT 'Ảnh hoặc audio',
  `dapandung` text DEFAULT NULL COMMENT 'Đáp án đúng nếu có',
  `thoigian` int(11) DEFAULT NULL COMMENT 'Thời gian làm câu, tính bằng giây',
  `diem` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Điểm câu hỏi',
  `trangthai` enum('hoatdong','an') NOT NULL DEFAULT 'hoatdong' COMMENT 'Trạng thái',
  `nguoitao` int(11) NOT NULL COMMENT 'Người tạo câu hỏi',
  `ngaytao` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamdiem`
--

CREATE TABLE `chamdiem` (
  `id` int(11) NOT NULL COMMENT 'Mã chấm điểm',
  `traloi_id` int(11) NOT NULL COMMENT 'Mã câu trả lời',
  `nguoicham` int(11) NOT NULL COMMENT 'Người chấm',
  `nguphap` decimal(5,2) DEFAULT NULL COMMENT 'Điểm ngữ pháp',
  `tuvung` decimal(5,2) DEFAULT NULL COMMENT 'Điểm từ vựng',
  `luloat` decimal(5,2) DEFAULT NULL COMMENT 'Điểm lưu loát',
  `lienquan` decimal(5,2) DEFAULT NULL COMMENT 'Điểm đúng chủ đề',
  `machlac` decimal(5,2) DEFAULT NULL COMMENT 'Điểm mạch lạc',
  `nhanxet` text DEFAULT NULL COMMENT 'Nhận xét',
  `tongdiem` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tổng điểm chấm',
  `ngaycham` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày chấm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dethi`
--

CREATE TABLE `dethi` (
  `id` int(11) NOT NULL COMMENT 'Mã đề thi',
  `ten` varchar(150) NOT NULL COMMENT 'Tên đề thi',
  `mota` text DEFAULT NULL COMMENT 'Mô tả',
  `thoigian` int(11) NOT NULL COMMENT 'Thời gian thi, tính bằng phút',
  `tongdiem` decimal(7,2) NOT NULL DEFAULT 0.00 COMMENT 'Tổng điểm',
  `trangthai` enum('nhap','hoatdong','an') NOT NULL DEFAULT 'nhap' COMMENT 'Trạng thái đề',
  `nguoitao` int(11) NOT NULL COMMENT 'Người tạo đề',
  `ngaytao` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dethicauhoi`
--

CREATE TABLE `dethicauhoi` (
  `id` int(11) NOT NULL COMMENT 'Mã chi tiết đề',
  `dethi_id` int(11) NOT NULL COMMENT 'Mã đề thi',
  `cauhoi_id` int(11) NOT NULL COMMENT 'Mã câu hỏi',
  `thutu` int(11) NOT NULL COMMENT 'Thứ tự câu hỏi',
  `diem` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Điểm câu hỏi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lanthi`
--

CREATE TABLE `lanthi` (
  `id` int(11) NOT NULL COMMENT 'Mã lần thi',
  `nguoidung_id` int(11) NOT NULL COMMENT 'Người làm bài',
  `dethi_id` int(11) NOT NULL COMMENT 'Đề thi',
  `batdau` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian bắt đầu',
  `ketthuc` datetime DEFAULT NULL COMMENT 'Thời gian kết thúc',
  `trangthai` enum('batdau','dangthi','danop','dangcham','hoanthanh') NOT NULL DEFAULT 'batdau' COMMENT 'Trạng thái lần thi',
  `diemdoc` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm Reading',
  `diemnghe` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm Listening',
  `diemviet` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm Writing',
  `diemnoi` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm Speaking',
  `tongdiem` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'Tổng điểm',
  `ngaytao` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lanthicauhoi`
--

CREATE TABLE `lanthicauhoi` (
  `id` int(11) NOT NULL COMMENT 'Mã chi tiết câu hỏi lần thi',
  `lanthi_id` int(11) NOT NULL COMMENT 'Mã lần thi',
  `cauhoi_id` int(11) NOT NULL COMMENT 'Mã câu hỏi',
  `thutu` int(11) NOT NULL COMMENT 'Thứ tự câu hỏi',
  `diemtoida` decimal(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Điểm tối đa',
  `datraloi` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã trả lời hay chưa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaicauhoi`
--

CREATE TABLE `loaicauhoi` (
  `id` int(11) NOT NULL COMMENT 'Mã loại câu hỏi',
  `ma` varchar(50) NOT NULL COMMENT 'Mã loại',
  `ten` varchar(100) NOT NULL COMMENT 'Tên loại câu hỏi',
  `kynang` enum('reading','listening','writing','speaking') NOT NULL COMMENT 'Kỹ năng',
  `mota` text DEFAULT NULL COMMENT 'Mô tả'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `luachon`
--

CREATE TABLE `luachon` (
  `id` int(11) NOT NULL COMMENT 'Mã lựa chọn',
  `cauhoi_id` int(11) NOT NULL COMMENT 'Mã câu hỏi',
  `noidung` text NOT NULL COMMENT 'Nội dung lựa chọn',
  `ladung` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 đúng, 0 sai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `id` int(11) NOT NULL COMMENT 'Mã người dùng',
  `hoten` varchar(100) NOT NULL COMMENT 'Họ và tên',
  `email` varchar(150) NOT NULL COMMENT 'Email đăng nhập',
  `matkhau` varchar(255) NOT NULL COMMENT 'Mật khẩu',
  `vaitro` enum('admin','user') NOT NULL DEFAULT 'user' COMMENT 'Vai trò',
  `anh` varchar(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
  `trangthai` enum('hoatdong','khoa') NOT NULL DEFAULT 'hoatdong' COMMENT 'Trạng thái tài khoản',
  `ngaytao` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `traloi`
--

CREATE TABLE `traloi` (
  `id` int(11) NOT NULL COMMENT 'Mã câu trả lời',
  `lanthi_id` int(11) NOT NULL COMMENT 'Mã lần thi',
  `cauhoi_id` int(11) NOT NULL COMMENT 'Mã câu hỏi',
  `noidung` longtext DEFAULT NULL COMMENT 'Nội dung trả lời',
  `luachon_id` int(11) DEFAULT NULL COMMENT 'Lựa chọn được chọn',
  `audio` varchar(255) DEFAULT NULL COMMENT 'File ghi âm',
  `diem` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm đạt được',
  `ladung` tinyint(1) DEFAULT NULL COMMENT '1 đúng, 0 sai, NULL nếu chưa chấm',
  `thoigian` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian trả lời'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vipham`
--

CREATE TABLE `vipham` (
  `id` int(11) NOT NULL COMMENT 'Mã vi phạm',
  `lanthi_id` int(11) NOT NULL COMMENT 'Mã lần thi',
  `loai` varchar(50) NOT NULL COMMENT 'Loại vi phạm',
  `mota` text DEFAULT NULL COMMENT 'Mô tả',
  `thoigian` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Thời gian vi phạm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loai_id` (`loai_id`),
  ADD KEY `nguoitao` (`nguoitao`);

--
-- Chỉ mục cho bảng `chamdiem`
--
ALTER TABLE `chamdiem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `traloi_id` (`traloi_id`),
  ADD KEY `nguoicham` (`nguoicham`);

--
-- Chỉ mục cho bảng `dethi`
--
ALTER TABLE `dethi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoitao` (`nguoitao`);

--
-- Chỉ mục cho bảng `dethicauhoi`
--
ALTER TABLE `dethicauhoi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dethi_id` (`dethi_id`,`cauhoi_id`),
  ADD KEY `cauhoi_id` (`cauhoi_id`);

--
-- Chỉ mục cho bảng `lanthi`
--
ALTER TABLE `lanthi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoidung_id` (`nguoidung_id`),
  ADD KEY `dethi_id` (`dethi_id`);

--
-- Chỉ mục cho bảng `lanthicauhoi`
--
ALTER TABLE `lanthicauhoi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lanthi_id` (`lanthi_id`,`cauhoi_id`),
  ADD KEY `cauhoi_id` (`cauhoi_id`);

--
-- Chỉ mục cho bảng `loaicauhoi`
--
ALTER TABLE `loaicauhoi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma` (`ma`);

--
-- Chỉ mục cho bảng `luachon`
--
ALTER TABLE `luachon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cauhoi_id` (`cauhoi_id`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `traloi`
--
ALTER TABLE `traloi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lanthi_id` (`lanthi_id`,`cauhoi_id`),
  ADD KEY `cauhoi_id` (`cauhoi_id`),
  ADD KEY `luachon_id` (`luachon_id`);

--
-- Chỉ mục cho bảng `vipham`
--
ALTER TABLE `vipham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lanthi_id` (`lanthi_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã câu hỏi';

--
-- AUTO_INCREMENT cho bảng `chamdiem`
--
ALTER TABLE `chamdiem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã chấm điểm';

--
-- AUTO_INCREMENT cho bảng `dethi`
--
ALTER TABLE `dethi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã đề thi';

--
-- AUTO_INCREMENT cho bảng `dethicauhoi`
--
ALTER TABLE `dethicauhoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã chi tiết đề';

--
-- AUTO_INCREMENT cho bảng `lanthi`
--
ALTER TABLE `lanthi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã lần thi';

--
-- AUTO_INCREMENT cho bảng `lanthicauhoi`
--
ALTER TABLE `lanthicauhoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã chi tiết câu hỏi lần thi';

--
-- AUTO_INCREMENT cho bảng `loaicauhoi`
--
ALTER TABLE `loaicauhoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã loại câu hỏi';

--
-- AUTO_INCREMENT cho bảng `luachon`
--
ALTER TABLE `luachon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã lựa chọn';

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã người dùng';

--
-- AUTO_INCREMENT cho bảng `traloi`
--
ALTER TABLE `traloi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã câu trả lời';

--
-- AUTO_INCREMENT cho bảng `vipham`
--
ALTER TABLE `vipham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã vi phạm';

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  ADD CONSTRAINT `cauhoi_ibfk_1` FOREIGN KEY (`loai_id`) REFERENCES `loaicauhoi` (`id`),
  ADD CONSTRAINT `cauhoi_ibfk_2` FOREIGN KEY (`nguoitao`) REFERENCES `nguoidung` (`id`);

--
-- Các ràng buộc cho bảng `chamdiem`
--
ALTER TABLE `chamdiem`
  ADD CONSTRAINT `chamdiem_ibfk_1` FOREIGN KEY (`traloi_id`) REFERENCES `traloi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chamdiem_ibfk_2` FOREIGN KEY (`nguoicham`) REFERENCES `nguoidung` (`id`);

--
-- Các ràng buộc cho bảng `dethi`
--
ALTER TABLE `dethi`
  ADD CONSTRAINT `dethi_ibfk_1` FOREIGN KEY (`nguoitao`) REFERENCES `nguoidung` (`id`);

--
-- Các ràng buộc cho bảng `dethicauhoi`
--
ALTER TABLE `dethicauhoi`
  ADD CONSTRAINT `dethicauhoi_ibfk_1` FOREIGN KEY (`dethi_id`) REFERENCES `dethi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dethicauhoi_ibfk_2` FOREIGN KEY (`cauhoi_id`) REFERENCES `cauhoi` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lanthi`
--
ALTER TABLE `lanthi`
  ADD CONSTRAINT `lanthi_ibfk_1` FOREIGN KEY (`nguoidung_id`) REFERENCES `nguoidung` (`id`),
  ADD CONSTRAINT `lanthi_ibfk_2` FOREIGN KEY (`dethi_id`) REFERENCES `dethi` (`id`);

--
-- Các ràng buộc cho bảng `lanthicauhoi`
--
ALTER TABLE `lanthicauhoi`
  ADD CONSTRAINT `lanthicauhoi_ibfk_1` FOREIGN KEY (`lanthi_id`) REFERENCES `lanthi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lanthicauhoi_ibfk_2` FOREIGN KEY (`cauhoi_id`) REFERENCES `cauhoi` (`id`);

--
-- Các ràng buộc cho bảng `luachon`
--
ALTER TABLE `luachon`
  ADD CONSTRAINT `luachon_ibfk_1` FOREIGN KEY (`cauhoi_id`) REFERENCES `cauhoi` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `traloi`
--
ALTER TABLE `traloi`
  ADD CONSTRAINT `traloi_ibfk_1` FOREIGN KEY (`lanthi_id`) REFERENCES `lanthi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `traloi_ibfk_2` FOREIGN KEY (`cauhoi_id`) REFERENCES `cauhoi` (`id`),
  ADD CONSTRAINT `traloi_ibfk_3` FOREIGN KEY (`luachon_id`) REFERENCES `luachon` (`id`);

--
-- Các ràng buộc cho bảng `vipham`
--
ALTER TABLE `vipham`
  ADD CONSTRAINT `vipham_ibfk_1` FOREIGN KEY (`lanthi_id`) REFERENCES `lanthi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
