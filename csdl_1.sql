-- Admin table
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Giảng viên table
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

-- Sinh viên table
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
  KEY `so_hieu` (`so_hieu`),
  CONSTRAINT `fk_sv_gv` FOREIGN KEY (`so_hieu`) REFERENCES `giang_vien` (`so_hieu_giang_vien`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cơ sở thực tập table
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

-- Công ty table
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

-- Tuyển dụng table
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
  KEY `stt_cty` (`stt_cty`),
  CONSTRAINT `tuyen_dung_ibfk_1` FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty` (`stt_cty`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ứng tuyển table
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
  KEY `stt_sv` (`stt_sv`),
  CONSTRAINT `ung_tuyen_ibfk_1` FOREIGN KEY (`ma_tuyen_dung`) REFERENCES `tuyen_dung` (`ma_tuyen_dung`),
  CONSTRAINT `ung_tuyen_ibfk_2` FOREIGN KEY (`stt_sv`) REFERENCES `sinh_vien` (`stt_sv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Báo cáo thực tập table
CREATE TABLE `bao_cao_thuc_tap` (
  `stt_baocao` int(11) NOT NULL AUTO_INCREMENT,
  `ma_dang_ky` int(11) NOT NULL,
  `ma_tuyen_dung` varchar(50) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stt_baocao`),
  KEY `ma_dang_ky` (`ma_dang_ky`),
  CONSTRAINT `bao_cao_thuc_tap_ibfk_1` FOREIGN KEY (`ma_dang_ky`) REFERENCES `ung_tuyen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `danh_gia_thuc_tap` (
    `stt_danhgia` INT(11) NOT NULL AUTO_INCREMENT,
    `ma_dang_ky` INT(11) NOT NULL,
    `ten_co_so` VARCHAR(255) NOT NULL,
    `tieu_de_tuyen_dung` VARCHAR(255) NOT NULL,
    `cong_ty` VARCHAR(255) NOT NULL,
    `nguoi_huong_dan` VARCHAR(100) NOT NULL,
    `chuc_vu` VARCHAR(100) DEFAULT NULL,
    `email_lien_he` VARCHAR(100) NOT NULL,
    `so_dien_thoai` VARCHAR(15) NOT NULL,
    `ho_ten_sinh_vien` VARCHAR(100) NOT NULL,
    `ma_sinh_vien` VARCHAR(50) NOT NULL,
    `lop_khoa` VARCHAR(50) NOT NULL,
    `nganh_hoc` VARCHAR(100) NOT NULL,
    `thoi_gian_thuc_tap` VARCHAR(100) NOT NULL,
    `thai_do` ENUM('Xuất sắc', 'Tốt', 'Trung bình', 'Yếu') NOT NULL,
    `thai_do_ghi_chu` TEXT DEFAULT NULL,
    `ky_nang_chuyen_mon` ENUM('Xuất sắc', 'Tốt', 'Trung bình', 'Yếu') NOT NULL,
    `ky_nang_ghi_chu` TEXT DEFAULT NULL,
    `lam_viec_nhom` ENUM('Xuất sắc', 'Tốt', 'Trung bình', 'Yếu') NOT NULL,
    `lam_viec_nhom_ghi_chu` TEXT DEFAULT NULL,
    `ky_nang_giao_tiep` ENUM('Xuất sắc', 'Tốt', 'Trung bình', 'Yếu') NOT NULL,
    `giao_tiep_ghi_chu` TEXT DEFAULT NULL,
    `thich_nghi` ENUM('Xuất sắc', 'Tốt', 'Trung bình', 'Yếu') NOT NULL,
    `thich_nghi_ghi_chu` TEXT DEFAULT NULL,
    `tuan_thu` ENUM('Xuất sắc', 'Tốt', 'Trung bình', 'Yếu') NOT NULL,
    `tuan_thu_ghi_chu` TEXT DEFAULT NULL,
    `nhan_xet_chung` TEXT DEFAULT NULL,
    `ket_qua_de_xuat` VARCHAR(255) NOT NULL,
    `ngay_danh_gia` DATE NOT NULL,
    `nguoi_danh_gia` VARCHAR(100) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`stt_danhgia`),
    KEY `ma_dang_ky` (`ma_dang_ky`),
    CONSTRAINT `fk_danhgia_ungtuyen` FOREIGN KEY (`ma_dang_ky`) REFERENCES `ung_tuyen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` INT(11) NOT NULL AUTO_INCREMENT,
  `ma_tuyen_dung` VARCHAR(50) NOT NULL,
  `tieu_de` VARCHAR(255) NOT NULL,
  `stt_cty` INT(11) NOT NULL,
  `mo_ta` TEXT DEFAULT NULL,
  `so_luong` INT(11) DEFAULT 1 CHECK (`so_luong` > 0),
  `han_nop` DATE NOT NULL,
  `trang_thai` ENUM('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `dia_chi` VARCHAR(255) NOT NULL,
  `hinh_thuc` ENUM('Full-time','Part-time') NOT NULL,
  `gioi_tinh` ENUM('Nam','Nữ','Không giới hạn') NOT NULL,
  `noi_bat` TINYINT(1) DEFAULT 0,
  `khoa` ENUM(
    'kinh_te',
    'moi_truong',
    'quan_ly_dat_dai',
    'khi_tuong_thuy_van',
    'trac_dia_ban_do',
    'dia_chat',
    'tai_nguyen_nuoc',
    'cntt',
    'ly_luan_chinh_tri',
    'bien_hai_dao',
    'khoa_hoc_dai_cuong',
    'the_chat_quoc_phong',
    'bo_mon_luat',
    'bien_doi_khi_hau',
    'ngoai_ngu'
  ) DEFAULT NULL,
  PRIMARY KEY (`stt_tuyendung`),
  UNIQUE KEY `ma_tuyen_dung` (`ma_tuyen_dung`),
  KEY `stt_cty` (`stt_cty`),
  CONSTRAINT `tuyen_dung_ibfk_1` FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty` (`stt_cty`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
