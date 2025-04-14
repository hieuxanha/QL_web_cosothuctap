-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 05:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ql_cosothuctap`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `role`) VALUES
(1, 'admin123@gmail.com', '123', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `bao_cao_thuc_tap`
--

CREATE TABLE `bao_cao_thuc_tap` (
  `stt_baocao` int(11) NOT NULL,
  `ma_dang_ky` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bao_cao_thuc_tap`
--

INSERT INTO `bao_cao_thuc_tap` (`stt_baocao`, `ma_dang_ky`, `ma_tuyen_dung`, `noi_dung`, `ngay_gui`, `file_path`) VALUES
(1, 5, '3766', 'hieudzzz', '2025-04-14', '../uploads/baocao_1744600086_aa.docx');

-- --------------------------------------------------------

--
-- Table structure for table `cong_ty`
--

CREATE TABLE `cong_ty` (
  `stt_cty` int(11) NOT NULL,
  `ten_cong_ty` varchar(255) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gioi_thieu` text DEFAULT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `logo` varchar(255) DEFAULT NULL,
  `anh_bia` varchar(255) DEFAULT NULL,
  `quy_mo` varchar(255) DEFAULT NULL,
  `linh_vuc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cong_ty`
--

INSERT INTO `cong_ty` (`stt_cty`, `ten_cong_ty`, `dia_chi`, `so_dien_thoai`, `email`, `gioi_thieu`, `trang_thai`, `logo`, `anh_bia`, `quy_mo`, `linh_vuc`) VALUES
(5, 'CHI NHÁNH CÔNG TY CỔ PHẦN ALTIUS LINK VIỆT NAM TẠI THÀNH PHỐ HỒ CHÍ MINH', 'hồ tùng mậu', '0987654321', '924@gmail.com', '1', 'Đã duyệt', '1743069732_logo_Picture1.jpg', '1743069732_anhbia_Picture2.jpg', '111', '1'),
(8, 'CÔNG TY TNHH AN BÌNH VN', 'hồ tùng mậu', '123', 'nchieu79224@gmail.com', '123', 'Đang chờ', '1744482156_logo_1742990881_anhbia_Picture1.jpg', '1744482156_anhbia_1742990881_logo_Picture_2.png', '123', '123');

-- --------------------------------------------------------

--
-- Table structure for table `co_so_thuc_tap`
--

CREATE TABLE `co_so_thuc_tap` (
  `stt_cstt` int(11) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `ten_co_so` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('co_so_thuc_tap') NOT NULL DEFAULT 'co_so_thuc_tap'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `co_so_thuc_tap`
--

INSERT INTO `co_so_thuc_tap` (`stt_cstt`, `ma_co_so`, `ten_co_so`, `email`, `password`, `role`) VALUES
(4, '123123', 'abc', 'cstt123@gmail.com', '$2y$10$jbRk.8JbenOODm/chE7mZueYZkiDiM7SYB/3A32ZhQY4TwMSfC8/a', 'co_so_thuc_tap');

-- --------------------------------------------------------

--
-- Table structure for table `giang_vien`
--

CREATE TABLE `giang_vien` (
  `stt_gv` int(11) NOT NULL,
  `so_hieu_giang_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('giang_vien') NOT NULL DEFAULT 'giang_vien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `giang_vien`
--

INSERT INTO `giang_vien` (`stt_gv`, `so_hieu_giang_vien`, `ho_ten`, `email`, `password`, `khoa`, `so_dien_thoai`, `role`) VALUES
(7, '1234567890', 'A', 'A123@gmail.com', '$2y$10$hdm9O1OjqtNnBx4GoqnCPeveINS9sA9cOcsbyzEhnxFS0PDYwhHBy', 'kinh_te', NULL, 'giang_vien'),
(9, '123456789', 'B', 'B123@gmail.com', '$2y$10$kodosKy1Zw7HLhqt/0Vm3OmOtmdFUwoksOQKcI7uXvockskjJhP0u', 'moi_truong', NULL, 'giang_vien'),
(11, '1234567898', 'c', 'c123@gmail.com', '$2y$10$K/uYJV7qTBLK8sg8ClEKV.JqsXM8D3b77Jd7rhya80qfQxLPeZ87y', 'bo_mon_luat', NULL, 'giang_vien');

-- --------------------------------------------------------

--
-- Table structure for table `sinh_vien`
--

CREATE TABLE `sinh_vien` (
  `stt_sv` int(11) NOT NULL,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lop` varchar(50) DEFAULT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_hieu` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('sinh_vien') NOT NULL DEFAULT 'sinh_vien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sinh_vien`
--

INSERT INTO `sinh_vien` (`stt_sv`, `ma_sinh_vien`, `ho_ten`, `email`, `password`, `lop`, `khoa`, `so_hieu`, `so_dien_thoai`, `role`) VALUES
(58, '22111061351', 'ĐẶNG NAM ANH', '22111061351@hunre.edu.vn', '$2y$10$xdOCClqYuVHlV31wU/T1v.DzQkgJN7mpPJZiTK05/Mc97u9Jz40UG', 'a1', 'kinh_te', '1234567890', NULL, 'sinh_vien'),
(59, '22111061425', 'HÀ MINH QUANG ANH', '22111061425@hunre.edu.vn', '$2y$10$R6m2jrfGc1h7Dl5OHZavZ.eFkuqYgZdZsuQSvHa1Rk3joUzkTvddW', 'a1', 'kinh_te', '1234567890', '0954569450', 'sinh_vien'),
(60, '22111061031', 'NGUYỄN THỊ MINH ANH', '22111061031@hunre.edu.vn', '$2y$10$e7GgoarB0lB0gfqoPV/SzOvStNFrn6BN3wnKJgwrYBtnxfBVDtN9G', 'a2', 'moi_truong', NULL, NULL, 'sinh_vien'),
(61, '22111061314', 'NGUYỄN THỊ VÂN ANH', '22111061314@hunre.edu.vn', '$2y$10$aWNZh.kFvBTjJDSXHzrKYemxljt/x9j6PgCbEFHf2l2w5CaBu7aVK', 'a2', 'moi_truong', NULL, NULL, 'sinh_vien'),
(62, '22111060935', 'TRẦN HẢI ANH', '22111060935@hunre.edu.vn', '$2y$10$0L7jVLR6NzC.NjBhVqy1lekWFnN/4I21YDyN3AosUjEgkUrqqkYYC', NULL, 'quan_ly_dat_dai', NULL, NULL, 'sinh_vien'),
(63, '22111060967', 'LƯƠNG QUYẾT CHIẾN', '22111060967@hunre.edu.vn', '$2y$10$qHDxGUkw6aUS5u9RdeFv8eakmZViWCntC6kwH5.VdYQZwRoSDEDMW', NULL, 'quan_ly_dat_dai', NULL, NULL, 'sinh_vien'),
(64, '1234567890', 'TRẦN THI NHƯ HOA', 'trannhuhoa28@gmail.com', '$2y$10$YVc62G2GxxEeMNorYq.rce9nr6SH4ECmcb8L9aCTa9vM9RvI0TKhe', 'a3', 'bo_mon_luat', '1234567898', '1234567890', 'sinh_vien'),
(66, '1234567891', 'Nguyễn Thị Hằng', 'nghang20052003@gmail.com', '$2y$10$KKIkIj2Gcw5R1mhvEjKqEOEZfGjdTKyGugNpKHf77Cd51qrNq/aoK', 'a3', 'bo_mon_luat', '1234567898', '1234567890', 'sinh_vien'),
(67, '1234567892', 'Vũ Thảo Vân', 'vuthaovan088@gmail.com', '$2y$10$1.JAfPTQxcEWZqvdZYPKpeVWIpp1f7IbDFgnsSk9/6azL97cfI7LC', 'a3', 'bo_mon_luat', '1234567898', '1234267890', 'sinh_vien');

-- --------------------------------------------------------

--
-- Table structure for table `tuyen_dung`
--

CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `stt_cty` int(11) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `yeu_cau` text DEFAULT NULL,
  `so_luong` int(11) DEFAULT 1 CHECK (`so_luong` > 0),
  `han_nop` date NOT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `dia_chi` varchar(255) NOT NULL,
  `hinh_thuc` enum('Full-time','Part-time') NOT NULL,
  `gioi_tinh` enum('Nam','Nữ','Không giới hạn') NOT NULL,
  `noi_bat` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuyen_dung`
--

INSERT INTO `tuyen_dung` (`stt_tuyendung`, `ma_tuyen_dung`, `tieu_de`, `stt_cty`, `mo_ta`, `yeu_cau`, `so_luong`, `han_nop`, `trang_thai`, `dia_chi`, `hinh_thuc`, `gioi_tinh`, `noi_bat`) VALUES
(1, 'TD20250327536', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ - Mức Lương 12-18M - Tại Hà Nội', 5, '1', '1', 1, '2025-03-20', 'Đã duyệt', 'hồ tùng mậu', 'Part-time', 'Không giới hạn', 1),
(2, '8840', 'Kế Toán Quản Trị/ Kế Toán Kiểm 132', 5, '123', '12', 12, '2025-04-16', 'Đã duyệt', 'hồ tùng mậu123', 'Full-time', 'Nam', 1),
(3, '4053', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ - Mức Lương 12-18M - Tại Hà Nội 2', 5, '2', '2', 2, '2025-04-25', 'Đã duyệt', '2', 'Part-time', 'Nữ', 1),
(4, '3766', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ 3', 5, 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 3, '2025-04-18', 'Đã duyệt', '3', 'Full-time', 'Nam', 1),
(5, '9414', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ - Mức Lương 12-18M - Tại Hà Nội 4', 5, 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 22, '2025-04-02', 'Đã duyệt', 'hồ tùng mậu', 'Full-time', 'Nam', 1),
(6, '9709', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ_7', 5, 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 21, '2025-04-25', 'Đã duyệt', 'hồ tùng mậu z', 'Part-time', 'Không giới hạn', 1),
(7, '8771', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ_244', 5, 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 12, '2025-04-22', 'Đã duyệt', '123', 'Full-time', 'Nam', 1),
(8, '5088', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ - Mức Lương 12-18M - Tại Hà Nội123', 5, 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 123, '2025-04-24', 'Đã duyệt', '123', 'Part-time', 'Không giới hạn', 1),
(9, '9170', 'Kế Toán Quản Trị/ Kế Toán Kiểm Soát Nội Bộ22222233', 5, 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 'Công ty Cổ phần Altius Link Việt Nam là công ty có vốn đầu tư của Nhật Bản, chuyên cung cấp các dịch vụ chăm sóc khách hàng và các dịch vụ trọn gói khác cho khách hàng. Với đội ngũ lãnh đạo tâm huyết, nhân viên chuyên nghiệp và các chuyên gia có nhiều kinh nghiệm đến từ Nhật Bản, Altius Link Việt Nam đang phấn đấu trở thành công ty hàng đầu trong lĩnh vực cung cấp các dịch vụ dịch vụ thuê ngoài - BPO tại Việt Nam.', 5, '2025-04-26', 'Đã duyệt', '32', 'Part-time', 'Nam', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ung_tuyen`
--

CREATE TABLE `ung_tuyen` (
  `id` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `stt_sv` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `thu_gioi_thieu` text DEFAULT NULL,
  `cv_path` varchar(255) NOT NULL,
  `ngay_ung_tuyen` datetime NOT NULL,
  `trang_thai` enum('Chờ duyệt','Đồng ý','Không đồng ý') DEFAULT 'Chờ duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ung_tuyen`
--

INSERT INTO `ung_tuyen` (`id`, `ma_tuyen_dung`, `stt_sv`, `ho_ten`, `email`, `so_dien_thoai`, `thu_gioi_thieu`, `cv_path`, `ngay_ung_tuyen`, `trang_thai`) VALUES
(5, '3766', 64, 'TRẦN THI NHƯ HOA', 'trannhuhoa28@gmail.com', '1234567890', '123', '../uploads/cv/1744482872_CV_TRAN_THI_NHU_HOA_NHAN_VIEN_PHAP_CHE.pdf', '2025-04-13 01:34:32', 'Đồng ý'),
(6, '9414', 66, 'Nguyễn Thị Hằng', 'nghang20052003@gmail.com', '1234567890', '123', '../uploads/cv/1744482978_CV-_Nguy___n_Th____H___ng_-_TTSPL.pdf', '2025-04-13 01:36:18', 'Chờ duyệt'),
(7, '8771', 67, 'Vũ Thảo Vân', 'vuthaovan088@gmail.com', '1234267890', '123', '../uploads/cv/1744483018_CV_Vu___Tha__o_Va__n_-_CV-KHCN-VU___THA__O_VA__N-TopCV.vn__1_.pdf', '2025-04-13 01:36:58', 'Chờ duyệt');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  ADD PRIMARY KEY (`stt_baocao`),
  ADD KEY `ma_dang_ky` (`ma_dang_ky`);

--
-- Indexes for table `cong_ty`
--
ALTER TABLE `cong_ty`
  ADD PRIMARY KEY (`stt_cty`),
  ADD UNIQUE KEY `ten_cong_ty` (`ten_cong_ty`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `co_so_thuc_tap`
--
ALTER TABLE `co_so_thuc_tap`
  ADD PRIMARY KEY (`stt_cstt`),
  ADD UNIQUE KEY `ma_co_so` (`ma_co_so`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `giang_vien`
--
ALTER TABLE `giang_vien`
  ADD PRIMARY KEY (`stt_gv`),
  ADD UNIQUE KEY `so_hieu_giang_vien` (`so_hieu_giang_vien`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD PRIMARY KEY (`stt_sv`),
  ADD UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ma_sinh_vien_2` (`ma_sinh_vien`),
  ADD KEY `so_hieu` (`so_hieu`);

--
-- Indexes for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  ADD PRIMARY KEY (`stt_tuyendung`),
  ADD UNIQUE KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  ADD KEY `stt_cty` (`stt_cty`);

--
-- Indexes for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  ADD KEY `stt_sv` (`stt_sv`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  MODIFY `stt_baocao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cong_ty`
--
ALTER TABLE `cong_ty`
  MODIFY `stt_cty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `co_so_thuc_tap`
--
ALTER TABLE `co_so_thuc_tap`
  MODIFY `stt_cstt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `giang_vien`
--
ALTER TABLE `giang_vien`
  MODIFY `stt_gv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  MODIFY `stt_sv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  MODIFY `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  ADD CONSTRAINT `bao_cao_thuc_tap_ibfk_1` FOREIGN KEY (`ma_dang_ky`) REFERENCES `ung_tuyen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD CONSTRAINT `fk_sv_gv` FOREIGN KEY (`so_hieu`) REFERENCES `giang_vien` (`so_hieu_giang_vien`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  ADD CONSTRAINT `tuyen_dung_ibfk_1` FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty` (`stt_cty`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  ADD CONSTRAINT `ung_tuyen_ibfk_1` FOREIGN KEY (`ma_tuyen_dung`) REFERENCES `tuyen_dung` (`ma_tuyen_dung`),
  ADD CONSTRAINT `ung_tuyen_ibfk_2` FOREIGN KEY (`stt_sv`) REFERENCES `sinh_vien` (`stt_sv`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
