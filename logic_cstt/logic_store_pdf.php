<?php
session_start();



// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Lấy dữ liệu từ POST
$stt_danhgia = filter_input(INPUT_POST, 'stt_danhgia', FILTER_SANITIZE_NUMBER_INT);
$filename = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
$filepath = filter_input(INPUT_POST, 'filepath', FILTER_SANITIZE_STRING);

// Kiểm tra dữ liệu hợp lệ
if (!$stt_danhgia || !$filename || !$filepath) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

// Kiểm tra xem stt_danhgia đã tồn tại trong pdf_nhan chưa
$stmt = $conn->prepare("SELECT id FROM pdf_nhan WHERE stt_danhgia = ?");
$stmt->bind_param("i", $stt_danhgia);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Gửi Thành công']);
    exit();
}
$stmt->close();

// Lưu thông tin PDF vào bảng pdf_nhan
$sql = "INSERT INTO pdf_nhan (stt_danhgia, filename, filepath, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
    exit();
}
$stmt->bind_param("iss", $stt_danhgia, $filename, $filepath);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'PDF đã được lưu thành công']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu PDF: ' . $conn->error]);
}

$conn->close();
exit();
