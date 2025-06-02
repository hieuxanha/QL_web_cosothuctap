<?php
session_start();
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Kiểm tra quyền truy cập (giả sử là giảng viên)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giang_vien') {
    $_SESSION['error'] = "Bạn không có quyền thực hiện thao tác này.";
    header('Location: ../giang_vien/ui_nhan_pdf.php');
    exit;
}

// Lấy dữ liệu từ form
$stt_danhgia = $_POST['stt_danhgia'] ?? null;
$ma_sinh_vien = $_POST['ma_sinh_vien'] ?? null;
$ket_qua_de_xuat = $_POST['ket_qua_de_xuat'] ?? null;

// Validate dữ liệu
$valid_results = ['A', 'B+', 'B', 'C', 'D', 'F'];
if (!$stt_danhgia || !$ma_sinh_vien || !in_array($ket_qua_de_xuat, $valid_results)) {
    $_SESSION['error'] = "Dữ liệu không hợp lệ.";
    header('Location: ../giang_vien/ui_nhan_pdf.php');
    exit;
}

// Cập nhật bản ghi trong bảng danh_gia_thuc_tap
$sql = "
    UPDATE danh_gia_thuc_tap
    SET ket_qua_de_xuat = ?
    WHERE stt_danhgia = ?
";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $_SESSION['error'] = "Lỗi prepare: " . $conn->error;
    header('Location: ../giang_vien/ui_nhan_pdf.php');
    exit;
}

$stmt->bind_param('si', $ket_qua_de_xuat, $stt_danhgia);

if ($stmt->execute()) {
    $_SESSION['message'] = "Chấm điểm thành công!";
} else {
    $_SESSION['error'] = "Lỗi khi chấm điểm: " . $stmt->error;
}

$stmt->close();
$conn->close();
header('Location: ../giang_vien/ui_nhan_pdf.php');
exit;
