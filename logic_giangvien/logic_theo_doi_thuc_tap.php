<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

if (!isset($_SESSION['so_hieu_giang_vien'])) {
    $response['error'] = 'Bạn chưa đăng nhập!';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Phương thức không hợp lệ!';
    echo json_encode($response);
    exit;
}

$ma_dang_ky = $_POST['ma_dang_ky'] ?? '';
$diem_so = $_POST['diem_so'] ?? '';
$ket_qua = $_POST['ket_qua'] ?? '';
$ghi_chu_giang_vien = $_POST['ghi_chu_giang_vien'] ?? '';

if (empty($ma_dang_ky) || empty($diem_so) || empty($ket_qua)) {
    $response['error'] = 'Thiếu thông tin bắt buộc!';
    echo json_encode($response);
    exit;
}

if (!is_numeric($diem_so) || $diem_so < 0 || $diem_so > 10) {
    $response['error'] = 'Điểm số phải từ 0 đến 10!';
    echo json_encode($response);
    exit;
}

if (!in_array($ket_qua, ['Đạt', 'Không đạt'])) {
    $response['error'] = 'Kết quả không hợp lệ!';
    echo json_encode($response);
    exit;
}

// Kiểm tra xem đã có đánh giá cho ma_dang_ky chưa
$sql_check = "SELECT stt_danhgia FROM danh_gia_thuc_tap WHERE ma_dang_ky = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $ma_dang_ky);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Cập nhật đánh giá hiện có
    $sql = "UPDATE danh_gia_thuc_tap SET diem_so = ?, ket_qua = ?, ghi_chu_giang_vien = ? WHERE ma_dang_ky = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $response['error'] = 'Lỗi prepare: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("dssi", $diem_so, $ket_qua, $ghi_chu_giang_vien, $ma_dang_ky);
} else {
    // Tạo đánh giá mới (giả sử doanh nghiệp chưa nhập)
    $sql = "INSERT INTO danh_gia_thuc_tap (ma_dang_ky, stt_sv, stt_cstt, ten_co_so, tieu_de_tuyen_dung, cong_ty, email_lien_he, giang_vien_huong_dan, ma_sinh_vien, lop_khoa, nganh_hoc, thoi_gian_thuc_tap, diem_so, ket_qua, ghi_chu_giang_vien, ngay_danh_gia, nguoi_danh_gia)
            SELECT ut.id, sv.stt_sv, 0, '', td.tieu_de, '', '', '', sv.ma_sinh_vien, sv.lop, sv.khoa, '', ?, ?, ?, CURDATE(), ?
            FROM ung_tuyen ut
            JOIN sinh_vien sv ON ut.stt_sv = sv.stt_sv
            JOIN tuyen_dung td ON ut.ma_tuyen_dung = td.ma_tuyen_dung
            WHERE ut.id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $response['error'] = 'Lỗi prepare: ' . $conn->error;
        echo json_encode($response);
        exit;
    }
    $nguoi_danh_gia = $_SESSION['name'] ?? 'Giảng viên';
    $stmt->bind_param("dsssi", $diem_so, $ket_qua, $ghi_chu_giang_vien, $nguoi_danh_gia, $ma_dang_ky);
}

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Đánh giá đã được lưu!';
} else {
    $response['error'] = 'Lỗi lưu đánh giá: ' . $stmt->error;
}
$stmt->close();
$conn->close();

echo json_encode($response);
