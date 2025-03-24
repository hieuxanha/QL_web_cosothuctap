-- 11/3/2025


-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS ql_cosott;
USE ql_cosott;

-- Tạo bảng admin
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(15) NOT NULL,
   `role` ENUM('admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng giang_vien
CREATE TABLE `giang_vien` (
  `stt_gv` int(11) NOT NULL AUTO_INCREMENT,
  `so_hieu_giang_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(15) NOT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
    `role` ENUM('giang_vien') NOT NULL DEFAULT 'giang_vien',
  PRIMARY KEY (`stt_gv`),
  UNIQUE KEY `so_hieu_giang_vien` (`so_hieu_giang_vien`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng sinh_vien
CREATE TABLE `sinh_vien` (
  `stt_sv` int(11) NOT NULL AUTO_INCREMENT,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(15) NOT NULL,
  `lop` varchar(50) DEFAULT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_hieu_giang_vien` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` ENUM('sinh_vien') NOT NULL DEFAULT 'sinh_vien',

  PRIMARY KEY (`stt_sv`),
  UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`),
  UNIQUE KEY `email` (`email`),
  KEY `so_hieu_giang_vien` (`so_hieu_giang_vien`),
  CONSTRAINT `fk_sv_gv` FOREIGN KEY (`so_hieu_giang_vien`) REFERENCES `giang_vien` (`so_hieu_giang_vien`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Tạo bảng co_so_thuc_tap
CREATE TABLE `co_so_thuc_tap` (
  `stt_cstt` int(11) NOT NULL AUTO_INCREMENT,
  `ma_co_so` varchar(50) NOT NULL,
  `ten_co_so` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(15) NOT NULL,
    `role` ENUM('co_so_thuc_tap') NOT NULL DEFAULT 'co_so_thuc_tap',
  PRIMARY KEY (`stt_cstt`),
  UNIQUE KEY `ma_co_so` (`ma_co_so`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Bảng nghiệp vụ
-- Tạo bảng dang_ky_thuc_tap
CREATE TABLE `dang_ky_thuc_tap` (
  `stt_dky` int(11) NOT NULL AUTO_INCREMENT,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `ngay_dang_ky` date DEFAULT curdate(),
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt',
  PRIMARY KEY (`stt_dky`),
  UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`,`ma_co_so`),
  KEY `fk_dky_coso` (`ma_co_so`),
  CONSTRAINT `fk_dky_coso` FOREIGN KEY (`ma_co_so`) REFERENCES `co_so_thuc_tap` (`ma_co_so`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dky_sinhvien` FOREIGN KEY (`ma_sinh_vien`) REFERENCES `sinh_vien` (`ma_sinh_vien`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng bao_cao_thuc_tap
CREATE TABLE `bao_cao_thuc_tap` (
  `stt_baocao` int(11) NOT NULL AUTO_INCREMENT,
  `ma_dang_ky` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  PRIMARY KEY (`stt_baocao`),
  KEY `fk_baocao_dky` (`ma_dang_ky`),
  CONSTRAINT `fk_baocao_dky` FOREIGN KEY (`ma_dang_ky`) REFERENCES `dang_ky_thuc_tap` (`stt_dky`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng tuyen_dung
CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `vi_tri` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `yeu_cau` text DEFAULT NULL,
  `so_luong` int(11) DEFAULT 1 CHECK (`so_luong` > 0),
  `han_nop` date NOT NULL,
  `trang_thai` enum('Đang tuyển','Đã đóng') DEFAULT 'Đang tuyển',
  PRIMARY KEY (`stt_tuyendung`),
  UNIQUE KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  KEY `fk_tuyendung_coso` (`ma_co_so`),
  CONSTRAINT `fk_tuyendung_coso` FOREIGN KEY (`ma_co_so`) REFERENCES `co_so_thuc_tap` (`ma_co_so`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Tạo bảng cong_ty
CREATE TABLE `cong_ty` (
  `stt_cty` int(11) NOT NULL AUTO_INCREMENT,
  `ten_cong_ty` varchar(255) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `gioi_thieu` text DEFAULT NULL,
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt',
  PRIMARY KEY (`stt_cty`),
  UNIQUE KEY `ten_cong_ty` (`ten_cong_ty`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;