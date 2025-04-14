-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 02:31 PM
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
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 'CHI NHÁNH CÔNG TY CỔ PHẦN ALTIUS LINK VIỆT NAM TẠI THÀNH PHỐ HỒ CHÍ MINH', 'hồ tùng mậu', '0987654321', '924@gmail.com', '1', 'Đã duyệt', '1743069732_logo_Picture1.jpg', '1743069732_anhbia_Picture2.jpg', '111', '1');

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
(1, '123', '123', 'cstt123@gmail.com', '$2y$10$WzPOcwlrzm4bI3kQ2XX/Iugk9l6iAcKPLEjKeqj1Lc80EFyVM/20a', 'co_so_thuc_tap'),
(2, 'c', '123', 'nguyenconghieu7924@gmail.com', '$2y$10$Qkr7YZYjXnwAaFJYBUNxm.TUWwOmcdzeV0dP/KMTb6xzI6DADyjZ2', 'co_so_thuc_tap');

-- --------------------------------------------------------

--
-- Table structure for table `cv_da_nop`
--

CREATE TABLE `cv_da_nop` (
  `id` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `stt_cstt` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `thu_gioi_thieu` text DEFAULT NULL,
  `cv_path` varchar(255) NOT NULL,
  `ngay_nop` datetime NOT NULL,
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dang_ky_thuc_tap`
--

CREATE TABLE `dang_ky_thuc_tap` (
  `stt_dky` int(11) NOT NULL,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `ngay_dang_ky` date DEFAULT curdate(),
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, '1231232', 'hieu123', 'gv123@gmail.com', '$2y$10$SqCiyVnk7Fali0Cg/m6/SONZy2zd7EWTOM.XzVNuXZ7UJ.WA9RsJG', 'the_chat_quoc_phong', NULL, 'giang_vien'),
(4, '123', 'b', 'nguyenconghieu7924@gmail.com', '$2y$10$K2hnZXuamu4XMKQw.FuCXuPSnsenDSD5gyTkxR8P6hvPMNbkMfXE.', 'bo_mon_luat', NULL, 'giang_vien'),
(5, '1', '1', 'LouisNeLson12119@hihicute.com', '$2y$10$bRkCdCKdva7zv0hQ0f/1k.XmKd/0ReUsWL.vDW90Hh/tYh3rYWDrq', 'bo_mon_luat', NULL, 'giang_vien');

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
(1, '001', 'hieu123', 'sv123@gmail.com', '$2y$10$rX9rwq3RM3zKEvt3a0jvpOAFYZmYrIh2cAOsmi4XUpEosN0acGoe6', 'dh12c3', 'the_chat_quoc_phong', NULL, NULL, 'sinh_vien'),
(43, '123123123', 'hieu', 'cb12324@gmail.com', '$2y$10$ZwPAgF4At4gOoKZDO.LvC.ZySmqBH7zQKG9ZEVL3LVpKc5LLNjH.q', NULL, 'the_chat_quoc_phong', NULL, NULL, 'sinh_vien'),
(49, '11111111', '1', 'nchieu7911124@gmail.com', '$2y$10$lsOvfdW/fJyz3FDA1hlTBOgJcm08bumhrdIiB.vNHW7ZVE2fZqhi6', NULL, 'bo_mon_luat', NULL, NULL, 'sinh_vien'),
(50, '111', '1', 'nchieu792224@gmail.com', '$2y$10$lMHZSTYOSQzNoqWOplB7YO8tjoNmkpLw.4CYowtE4dc7gDGi6GcDq', NULL, 'the_chat_quoc_phong', NULL, NULL, 'sinh_vien'),
(52, '1', '1h', 'nchieu7921324@gmail.com', '$2y$10$b1Xz0Wx9E7gnM/bovBrlZ.4Fmti4LuwTVntFVur0IF/vKucKcQj7q', NULL, 'bien_hai_dao', NULL, NULL, 'sinh_vien'),
(56, '222222', 'vanhvanh', 'nchieu7924@gmail.com', '$2y$10$HG2icJqlPACiGRPWYFSuxefrY3kgHth5nnaolBo1fN.cQk/0EJaUG', 'dh12c33', 'khoa_hoc_dai_cuong', NULL, '0987654321', 'sinh_vien'),
(57, '211111111', 'Le Thi Bii', 'nv112@gmail.com', '$2y$10$iYyrrjB3.jVYOplAQZ/xwukKho4qf7ucxJg2QynoLSWAGvn5PPvkW', NULL, 'kinh_te', NULL, NULL, 'sinh_vien');

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
(1, 'TD20250327536', 56, 'vanhvanh', 'nchieu7924@gmail.com', '0987654321', '1', '../uploads/cv/1744248467_CV-_Nguy___n_Th____H___ng_-_TTSPL.pdf', '2025-04-10 08:27:47', 'Chờ duyệt'),
(3, '8840', 56, 'vanhvanh', 'nchieu7924@gmail.com', '0987654321', '123', '../uploads/cv/1744249440_CV_Vu___Tha__o_Va__n_-_CV-KHCN-VU___THA__O_VA__N-TopCV.vn__1_.pdf', '2025-04-10 08:44:00', 'Chờ duyệt'),
(4, '4053', 1, 'hieu123', 'sv123@gmail.com', '0987654321', '1', '../uploads/cv/1744249498_CV_TRAN_THI_NHU_HOA_NHAN_VIEN_PHAP_CHE.pdf', '2025-04-10 08:44:58', 'Đồng ý');

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
-- Indexes for table `cv_da_nop`
--
ALTER TABLE `cv_da_nop`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  ADD KEY `ma_sinh_vien` (`ma_sinh_vien`),
  ADD KEY `stt_cstt` (`stt_cstt`);

--
-- Indexes for table `dang_ky_thuc_tap`
--
ALTER TABLE `dang_ky_thuc_tap`
  ADD PRIMARY KEY (`stt_dky`),
  ADD UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`,`ma_co_so`),
  ADD KEY `fk_dky_coso` (`ma_co_so`);

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
  MODIFY `stt_baocao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cong_ty`
--
ALTER TABLE `cong_ty`
  MODIFY `stt_cty` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `co_so_thuc_tap`
--
ALTER TABLE `co_so_thuc_tap`
  MODIFY `stt_cstt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cv_da_nop`
--
ALTER TABLE `cv_da_nop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dang_ky_thuc_tap`
--
ALTER TABLE `dang_ky_thuc_tap`
  MODIFY `stt_dky` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `giang_vien`
--
ALTER TABLE `giang_vien`
  MODIFY `stt_gv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sinh_vien`
--
ALTER TABLE `sinh_vien`
  MODIFY `stt_sv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tuyen_dung`
--
ALTER TABLE `tuyen_dung`
  MODIFY `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ung_tuyen`
--
ALTER TABLE `ung_tuyen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bao_cao_thuc_tap`
--
ALTER TABLE `bao_cao_thuc_tap`
  ADD CONSTRAINT `bao_cao_thuc_tap_ibfk_1` FOREIGN KEY (`ma_dang_ky`) REFERENCES `dang_ky_thuc_tap` (`stt_dky`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cv_da_nop`
--
ALTER TABLE `cv_da_nop`
  ADD CONSTRAINT `cv_da_nop_ibfk_1` FOREIGN KEY (`ma_tuyen_dung`) REFERENCES `tuyen_dung` (`ma_tuyen_dung`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cv_da_nop_ibfk_2` FOREIGN KEY (`ma_sinh_vien`) REFERENCES `sinh_vien` (`ma_sinh_vien`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cv_da_nop_ibfk_3` FOREIGN KEY (`stt_cstt`) REFERENCES `co_so_thuc_tap` (`stt_cstt`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dang_ky_thuc_tap`
--
ALTER TABLE `dang_ky_thuc_tap`
  ADD CONSTRAINT `fk_dky_coso` FOREIGN KEY (`ma_co_so`) REFERENCES `co_so_thuc_tap` (`ma_co_so`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dky_sinhvien` FOREIGN KEY (`ma_sinh_vien`) REFERENCES `sinh_vien` (`ma_sinh_vien`) ON DELETE CASCADE ON UPDATE CASCADE;

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
