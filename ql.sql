-- Bảng admin
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng bao_cao_thuc_tap
CREATE TABLE `bao_cao_thuc_tap` (
  `stt_baocao` int(11) NOT NULL AUTO_INCREMENT,
  `ma_dang_ky` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  PRIMARY KEY (`stt_baocao`),
  KEY `ma_dang_ky` (`ma_dang_ky`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng cong_ty
CREATE TABLE `cong_ty` (
  `stt_cty` int(11) NOT NULL AUTO_INCREMENT,
  `ten_cong_ty` varchar(255) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gioi_thieu` text DEFAULT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `logo` varchar(255) DEFAULT NULL,
  `anh_bia` varchar(255) DEFAULT NULL,
  `quy_mo` varchar(255) DEFAULT NULL,
  `linh_vuc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stt_cty`),
  UNIQUE KEY `ten_cong_ty` (`ten_cong_ty`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng co_so_thuc_tap
CREATE TABLE `co_so_thuc_tap` (
  `stt_cstt` int(11) NOT NULL AUTO_INCREMENT,
  `ma_co_so` varchar(50) NOT NULL,
  `ten_co_so` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('co_so_thuc_tap') NOT NULL DEFAULT 'co_so_thuc_tap',
  PRIMARY KEY (`stt_cstt`),
  UNIQUE KEY `ma_co_so` (`ma_co_so`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng dang_ky_thuc_tap
CREATE TABLE `dang_ky_thuc_tap` (
  `stt_dky` int(11) NOT NULL AUTO_INCREMENT,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `ngay_dang_ky` date DEFAULT curdate(),
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt',
  PRIMARY KEY (`stt_dky`),
  UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`,`ma_co_so`),
  KEY `fk_dky_coso` (`ma_co_so`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng giang_vien
CREATE TABLE `giang_vien` (
  `stt_gv` int(11) NOT NULL AUTO_INCREMENT,
  `so_hieu_giang_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('giang_vien') NOT NULL DEFAULT 'giang_vien',
  PRIMARY KEY (`stt_gv`),
  UNIQUE KEY `so_hieu_giang_vien` (`so_hieu_giang_vien`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng sinh_vien
CREATE TABLE `sinh_vien` (
  `stt_sv` int(11) NOT NULL AUTO_INCREMENT,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lop` varchar(50) DEFAULT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_hieu` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('sinh_vien') NOT NULL DEFAULT 'sinh_vien',
  PRIMARY KEY (`stt_sv`),
  UNIQUE KEY `ma_sinh_vien` (`ma_sinh_vien`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `ma_sinh_vien_2` (`ma_sinh_vien`),
  KEY `so_hieu` (`so_hieu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng tuyen_dung
CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT,
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
  `noi_bat` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`stt_tuyendung`),
  UNIQUE KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  KEY `stt_cty` (`stt_cty`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Bảng ung_tuyen
CREATE TABLE `ung_tuyen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `stt_sv` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `thu_gioi_thieu` text DEFAULT NULL,
  `cv_path` varchar(255) NOT NULL,
  `ngay_ung_tuyen` datetime NOT NULL,
  `trang_thai` enum('Chờ duyệt','Đồng ý','Không đồng ý') DEFAULT 'Chờ duyệt',
  PRIMARY KEY (`id`),
  KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  KEY `stt_sv` (`stt_sv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Các ràng buộc khóa ngoại
ALTER TABLE `bao_cao_thuc_tap`
  ADD CONSTRAINT `bao_cao_thuc_tap_ibfk_1` FOREIGN KEY (`ma_dang_ky`) REFERENCES `dang_ky_thuc_tap` (`stt_dky`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `dang_ky_thuc_tap`
  ADD CONSTRAINT `fk_dky_coso` FOREIGN KEY (`ma_co_so`) REFERENCES `co_so_thuc_tap` (`ma_co_so`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dky_sinhvien` FOREIGN KEY (`ma_sinh_vien`) REFERENCES `sinh_vien` (`ma_sinh_vien`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sinh_vien`
  ADD CONSTRAINT `fk_sv_gv` FOREIGN KEY (`so_hieu`) REFERENCES `giang_vien` (`so_hieu_giang_vien`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `tuyen_dung`
  ADD CONSTRAINT `tuyen_dung_ibfk_1` FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty` (`stt_cty`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ung_tuyen`
  ADD CONSTRAINT `ung_tuyen_ibfk_1` FOREIGN KEY (`ma_tuyen_dung`) REFERENCES `tuyen_dung` (`ma_tuyen_dung`),
  ADD CONSTRAINT `ung_tuyen_ibfk_2` FOREIGN KEY (`stt_sv`) REFERENCES `sinh_vien` (`stt_sv`);