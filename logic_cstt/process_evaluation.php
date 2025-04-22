<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vui lòng đăng nhập!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Phương thức không hợp lệ!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = "Token CSRF không hợp lệ!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}

// Input sanitization
$stt_sv = filter_input(INPUT_POST, 'stt_sv', FILTER_SANITIZE_NUMBER_INT);
$ma_dang_ky = filter_input(INPUT_POST, 'ma_dang_ky', FILTER_SANITIZE_NUMBER_INT);
$stt_cstt = filter_input(INPUT_POST, 'stt_cstt', FILTER_SANITIZE_NUMBER_INT);
$ten_co_so = filter_input(INPUT_POST, 'ten_co_so', FILTER_SANITIZE_STRING);
$tieu_de_tuyen_dung = filter_input(INPUT_POST, 'tieu_de_tuyen_dung', FILTER_SANITIZE_STRING);
$cong_ty = filter_input(INPUT_POST, 'cong_ty', FILTER_SANITIZE_STRING);
$email_lien_he = filter_input(INPUT_POST, 'email_lien_he', FILTER_SANITIZE_EMAIL);
$giang_vien_huong_dan = filter_input(INPUT_POST, 'giang_vien_huong_dan', FILTER_SANITIZE_STRING);
$ma_sinh_vien = filter_input(INPUT_POST, 'ma_sinh_vien', FILTER_SANITIZE_STRING);
$lop_khoa = filter_input(INPUT_POST, 'lop_khoa', FILTER_SANITIZE_STRING);
$nganh_hoc = filter_input(INPUT_POST, 'nganh_hoc', FILTER_SANITIZE_STRING);
$thoi_gian_thuc_tap = filter_input(INPUT_POST, 'thoi_gian_thuc_tap', FILTER_SANITIZE_STRING);
$thai_do = filter_input(INPUT_POST, 'thai_do', FILTER_SANITIZE_STRING);
$thai_do_ghi_chu = filter_input(INPUT_POST, 'thai_do_ghi_chu', FILTER_SANITIZE_STRING) ?? '';
$ky_nang_chuyen_mon = filter_input(INPUT_POST, 'ky_nang_chuyen_mon', FILTER_SANITIZE_STRING);
$ky_nang_ghi_chu = filter_input(INPUT_POST, 'ky_nang_ghi_chu', FILTER_SANITIZE_STRING) ?? '';
$lam_viec_nhom = filter_input(INPUT_POST, 'lam_viec_nhom', FILTER_SANITIZE_STRING);
$lam_viec_nhom_ghi_chu = filter_input(INPUT_POST, 'lam_viec_nhom_ghi_chu', FILTER_SANITIZE_STRING) ?? '';
$ky_nang_giao_tiep = filter_input(INPUT_POST, 'ky_nang_giao_tiep', FILTER_SANITIZE_STRING);
$ky_nang_giao_tiep_ghi_chu = filter_input(INPUT_POST, 'ky_nang_giao_tiep_ghi_chu', FILTER_SANITIZE_STRING) ?? '';
$thich_nghi = filter_input(INPUT_POST, 'thich_nghi', FILTER_SANITIZE_STRING);
$thich_nghi_ghi_chu = filter_input(INPUT_POST, 'thich_nghi_ghi_chu', FILTER_SANITIZE_STRING) ?? '';
$tuan_thu = filter_input(INPUT_POST, 'tuan_thu', FILTER_SANITIZE_STRING);
$tuan_thu_ghi_chu = filter_input(INPUT_POST, 'tuan_thu_ghi_chu', FILTER_SANITIZE_STRING) ?? '';
$nhan_xet_chung = filter_input(INPUT_POST, 'nhan_xet_chung', FILTER_SANITIZE_STRING) ?? '';
$ket_qua_de_xuat = isset($_POST['ket_qua_de_xuat']) ? implode(',', $_POST['ket_qua_de_xuat']) : '';
$ngay_danh_gia = filter_input(INPUT_POST, 'ngay_danh_gia', FILTER_SANITIZE_STRING);
$nguoi_danh_gia = filter_input(INPUT_POST, 'nguoi_danh_gia', FILTER_SANITIZE_STRING);

// Required fields
$required_fields = [
    'ma_dang_ky' => $ma_dang_ky,
    'stt_sv' => $stt_sv,
    'stt_cstt' => $stt_cstt,
    'ten_co_so' => $ten_co_so,
    'tieu_de_tuyen_dung' => $tieu_de_tuyen_dung,
    'cong_ty' => $cong_ty,
    'email_lien_he' => $email_lien_he,
    'giang_vien_huong_dan' => $giang_vien_huong_dan,
    'ma_sinh_vien' => $ma_sinh_vien,
    'lop_khoa' => $lop_khoa,
    'nganh_hoc' => $nganh_hoc,
    'thoi_gian_thuc_tap' => $thoi_gian_thuc_tap,
    'thai_do' => $thai_do,
    'ky_nang_chuyen_mon' => $ky_nang_chuyen_mon,
    'lam_viec_nhom' => $lam_viec_nhom,
    'ky_nang_giao_tiep' => $ky_nang_giao_tiep,
    'thich_nghi' => $thich_nghi,
    'tuan_thu' => $tuan_thu,
    'ngay_danh_gia' => $ngay_danh_gia,
    'nguoi_danh_gia' => $nguoi_danh_gia
];

foreach ($required_fields as $field => $value) {
    if (empty($value)) {
        $_SESSION['error_message'] = "Vui lòng điền đầy đủ thông tin bắt buộc ($field)!";
        header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
        exit();
    }
}

// Email validation
if (!filter_var($email_lien_he, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = "Email liên hệ không hợp lệ!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}

// Validate ratings
$valid_ratings = ['Xuất sắc', 'Tốt', 'Trung bình', 'Yếu'];
$rating_fields = ['thai_do', 'ky_nang_chuyen_mon', 'lam_viec_nhom', 'ky_nang_giao_tiep', 'thich_nghi', 'tuan_thu'];
foreach ($rating_fields as $field) {
    if (!in_array($$field, $valid_ratings)) {
        $_SESSION['error_message'] = "Giá trị không hợp lệ cho tiêu chí $field!";
        header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
        exit();
    }
}

// Validate ma_dang_ky
$stmt = $conn->prepare("SELECT id FROM ung_tuyen WHERE id = ? AND trang_thai = 'Đồng ý'");
$stmt->bind_param("i", $ma_dang_ky);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $_SESSION['error_message'] = "Mã đăng ký không hợp lệ hoặc ứng tuyển chưa được duyệt!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}
$stmt->close();

// Validate stt_cstt
$stmt = $conn->prepare("SELECT stt_cstt FROM co_so_thuc_tap WHERE stt_cstt = ?");
$stmt->bind_param("i", $stt_cstt);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $_SESSION['error_message'] = "Cơ sở thực tập không tồn tại!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}
$stmt->close();

// Validate stt_sv
$stmt = $conn->prepare("SELECT stt_sv FROM sinh_vien WHERE stt_sv = ?");
$stmt->bind_param("i", $stt_sv);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $_SESSION['error_message'] = "Sinh viên không tồn tại!";
    header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
    exit();
}
$stmt->close();

// Insert into database
$sql = "INSERT INTO danh_gia_thuc_tap (
    ma_dang_ky, stt_sv, stt_cstt, ten_co_so, tieu_de_tuyen_dung, cong_ty,
    email_lien_he, giang_vien_huong_dan, ma_sinh_vien, lop_khoa, nganh_hoc,
    thoi_gian_thuc_tap, thai_do, thai_do_ghi_chu, ky_nang_chuyen_mon, ky_nang_ghi_chu,
    lam_viec_nhom, lam_viec_nhom_ghi_chu, ky_nang_giao_tiep, ky_nang_giao_tiep_ghi_chu,
    thich_nghi, thich_nghi_ghi_chu, tuan_thu, tuan_thu_ghi_chu, nhan_xet_chung,
    ket_qua_de_xuat, ngay_danh_gia, nguoi_danh_gia
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iiisssssssssssssssssssssssss",
    $ma_dang_ky,
    $stt_sv,
    $stt_cstt,
    $ten_co_so,
    $tieu_de_tuyen_dung,
    $cong_ty,
    $email_lien_he,
    $giang_vien_huong_dan,
    $ma_sinh_vien,
    $lop_khoa,
    $nganh_hoc,
    $thoi_gian_thuc_tap,
    $thai_do,
    $thai_do_ghi_chu,
    $ky_nang_chuyen_mon,
    $ky_nang_ghi_chu,
    $lam_viec_nhom,
    $lam_viec_nhom_ghi_chu,
    $ky_nang_giao_tiep,
    $ky_nang_giao_tiep_ghi_chu,
    $thich_nghi,
    $thich_nghi_ghi_chu,
    $tuan_thu,
    $tuan_thu_ghi_chu,
    $nhan_xet_chung,
    $ket_qua_de_xuat,
    $ngay_danh_gia,
    $nguoi_danh_gia
);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Đánh giá đã được gửi thành công!";
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo lại CSRF token
} else {
    error_log("Lỗi khi lưu đánh giá: " . $stmt->error, 3, "../logs/error.log");
    $_SESSION['error_message'] = "Lỗi khi lưu đánh giá: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: ../co_so_thuc_tap/ui_danh_gia_thuc_tap.php");
exit();
