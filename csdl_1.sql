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



-- Cập nhật bảng cong_ty
CREATE TABLE `cong_ty` (
  `stt_cty` int(11) NOT NULL AUTO_INCREMENT,
  `ten_cong_ty` varchar(255) NOT NULL UNIQUE,
  `dia_chi` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `gioi_thieu` text DEFAULT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `logo` varchar(255) DEFAULT NULL,
  `anh_bia` varchar(255) DEFAULT NULL,
  `quy_mo` varchar(255) DEFAULT NULL,  -- Đã di chuyển từ bảng tuyen_dung
  `linh_vuc` varchar(255) DEFAULT NULL,  -- Đã di chuyển từ bảng tuyen_dung
  PRIMARY KEY (`stt_cty`)
);

-- Cập nhật bảng tuyen_dung (đã loại bỏ các cột không cần thiết)
CREATE TABLE `tuyen_dung` (
  `stt_tuyendung` int(11) NOT NULL AUTO_INCREMENT,
  `ma_tuyen_dung` varchar(50) NOT NULL UNIQUE,
  `tieu_de` varchar(255) NOT NULL,
  `stt_cty` int(11) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `yeu_cau` text DEFAULT NULL,
  `so_luong` int(11) DEFAULT 1 CHECK (`so_luong` > 0),
  `han_nop` date NOT NULL,
  `trang_thai` enum('Đang chờ','Đã duyệt','Bị từ chối') DEFAULT 'Đang chờ',
  `dia_chi` varchar(255) NOT NULL,
  `hinh_thuc` enum('Full-time', 'Part-time') NOT NULL,
  `gioi_tinh` enum('Nam', 'Nữ', 'Không giới hạn') NOT NULL,
  `noi_bat` TINYINT(1) DEFAULT 0, -- Cột nổi bật: 0 = không, 1 = có
  PRIMARY KEY (`stt_tuyendung`),
  FOREIGN KEY (`stt_cty`) REFERENCES `cong_ty`(`stt_cty`) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE ung_tuyen (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Khóa chính, tự động tăng
    ma_tuyen_dung VARCHAR(50) NOT NULL, -- Mã tin tuyển dụng
    stt_sv INT NOT NULL, -- Số thứ tự sinh viên
    ho_ten VARCHAR(100) NOT NULL, -- Họ và tên sinh viên
    email VARCHAR(100) NOT NULL, -- Email sinh viên
    so_dien_thoai VARCHAR(20) NOT NULL, -- Số điện thoại sinh viên
    thu_gioi_thieu TEXT, -- Thư giới thiệu (có thể để trống)
    cv_path VARCHAR(255) NOT NULL, -- Đường dẫn file CV
    ngay_ung_tuyen DATETIME NOT NULL, -- Ngày giờ ứng tuyển
    FOREIGN KEY (ma_tuyen_dung) REFERENCES tuyen_dung(ma_tuyen_dung), -- Khóa ngoại tới bảng tuyen_dung
    FOREIGN KEY (stt_sv) REFERENCES sinh_vien(stt_sv) -- Khóa ngoại tới bảng sinh_vien
);
ALTER TABLE ung_tuyen
ADD COLUMN trang_thai ENUM('Chờ duyệt', 'Đồng ý', 'Không đồng ý') DEFAULT 'Chờ duyệt';