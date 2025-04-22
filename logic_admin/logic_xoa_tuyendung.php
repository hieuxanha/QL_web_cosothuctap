<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ']);
    exit;
}

$ma_tuyen_dung = isset($_POST['ma_tuyen_dung']) ? trim($_POST['ma_tuyen_dung']) : '';

if (empty($ma_tuyen_dung)) {
    echo json_encode(['success' => false, 'error' => 'Mã tuyển dụng không hợp lệ']);
    exit;
}

try {
    // Xóa tin tuyển dụng
    $sql = "DELETE FROM tuyen_dung WHERE ma_tuyen_dung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ma_tuyen_dung);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Xóa tin tuyển dụng thành công']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa tin tuyển dụng']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Lỗi server: ' . $e->getMessage()]);
}

$conn->close();
?>