-- Admin table
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`)
);

-- Students table
CREATE TABLE `sinh_vien` (
  `stt_sv` int(11) NOT NULL AUTO_INCREMENT,
  `ma_sinh_vien` varchar(50) NOT NULL UNIQUE,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `lop` varchar(50) DEFAULT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_hieu` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('sinh_vien') NOT NULL DEFAULT 'sinh_vien',
  PRIMARY KEY (`stt_sv`),
  FOREIGN KEY (`so_hieu`) REFERENCES `giang_vien`(`so_hieu_giang_vien`) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Lecturers table
CREATE TABLE `giang_vien` (
  `stt_gv` int(11) NOT NULL AUTO_INCREMENT,
  `so_hieu_giang_vien` varchar(50) NOT NULL UNIQUE,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `khoa` varchar(50) DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `role` enum('giang_vien') NOT NULL DEFAULT 'giang_vien',
  PRIMARY KEY (`stt_gv`)
);

-- Internship organizations table
CREATE TABLE `co_so_thuc_tap` (
  `stt_cstt` int(11) NOT NULL AUTO_INCREMENT,
  `ma_co_so` varchar(50) NOT NULL UNIQUE,
  `ten_co_so` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('co_so_thuc_tap') NOT NULL DEFAULT 'co_so_thuc_tap',
  PRIMARY KEY (`stt_cstt`)
);

-- Companies table
CREATE TABLE `cong_ty` (
  `stt_cty` int(11) NOT NULL AUTO_INCREMENT,
  `ten_cong_ty` varchar(255) NOT NULL UNIQUE,
  `dia_chi` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `gioi_thieu` text DEFAULT NULL,
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt',
  `logo` varchar(255) DEFAULT NULL,
   `anh_bia` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stt_cty`)
);

-- Internship registrations table
CREATE TABLE `dang_ky_thuc_tap` (
  `stt_dky` int(11) NOT NULL AUTO_INCREMENT,
  `ma_sinh_vien` varchar(50) NOT NULL,
  `ma_co_so` varchar(50) NOT NULL,
  `ngay_dang_ky` date DEFAULT curdate(),
  `trang_thai` enum('Chờ duyệt','Đã duyệt','Bị từ chối') DEFAULT 'Chờ duyệt',
  PRIMARY KEY (`stt_dky`),
  UNIQUE KEY (`ma_sinh_vien`,`ma_co_so`),
  FOREIGN KEY (`ma_sinh_vien`) REFERENCES `sinh_vien`(`ma_sinh_vien`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`ma_co_so`) REFERENCES `co_so_thuc_tap`(`ma_co_so`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Internship reports table
CREATE TABLE `bao_cao_thuc_tap` (
  `stt_baocao` int(11) NOT NULL AUTO_INCREMENT,
  `ma_dang_ky` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` date DEFAULT curdate(),
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  PRIMARY KEY (`stt_baocao`),
  FOREIGN KEY (`ma_dang_ky`) REFERENCES `dang_ky_thuc_tap`(`stt_dky`) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Recruitment/job postings table
CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT,
  `ma_tuyen_dung` varchar(50) NOT NULL UNIQUE,
  `tieu_de` varchar(255) NOT NULL,
  `stt_cty` int(11) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `yeu_cau` text DEFAULT NULL,
  `so_luong` int(11) DEFAULT 1 CHECK (`so_luong` > 0),
  `han_nop` date NOT NULL,
  `trang_thai` enum('Đang tuyển','Đã đóng') DEFAULT 'Đang tuyển',
  `hinh_anh` varchar(255) DEFAULT NULL,
  `quy_mo` varchar(255) DEFAULT NULL,
  `khoa` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stt_tuyendung`),
  FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty`(`stt_cty`) ON DELETE CASCADE ON UPDATE CASCADE
);