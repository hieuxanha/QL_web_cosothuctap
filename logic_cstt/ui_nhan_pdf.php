<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

// Lấy dữ liệu từ yêu cầu POST
$stt_danhgia = filter_input(INPUT_POST, 'stt_danhgia', FILTER_SANITIZE_NUMBER_INT);
$filename = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
$filepath = filter_input(INPUT_POST, 'filepath', FILTER_SANITIZE_STRING);

if (!$stt_danhgia || !$filename || !$filepath) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit();
}

// Kiểm tra file PDF có tồn tại không
if (!file_exists($filepath)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File PDF không tồn tại']);
    exit();
}

// Lưu thông tin file PDF vào cơ sở dữ liệu (tùy chọn)
$stmt = $conn->prepare("
    INSERT INTO pdf_nhan (stt_danhgia, filename, filepath, created_at)
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iss", $stt_danhgia, $filename, $filepath);
$success = $stmt->execute();
$stmt->close();

// Đóng kết nối
$conn->close();

// Trả về phản hồi JSON
header('Content-Type: application/json');
if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'File PDF đã được nhận thành công',
        'filename' => $filename,
        'filepath' => $filepath
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu thông tin file PDF']);
}
exit();
