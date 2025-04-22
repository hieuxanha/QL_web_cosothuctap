<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ']);
    exit;
}

$stt_cty = isset($_POST['stt_cty']) ? trim($_POST['stt_cty']) : '';

if (empty($stt_cty)) {
    echo json_encode(['success' => false, 'error' => 'Mã công ty không hợp lệ']);
    exit;
}

try {
    // Kiểm tra xem công ty có tin tuyển dụng nào không
    $sql_check = "SELECT COUNT(*) as count FROM tuyen_dung WHERE stt_cty = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $stt_cty);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        echo json_encode(['success' => false, 'error' => 'Không thể xóa công ty vì có tin tuyển dụng liên quan']);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Xóa công ty
    $sql = "DELETE FROM cong_ty WHERE stt_cty = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $stt_cty);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Xóa công ty thành công']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa công ty']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Lỗi server: ' . $e->getMessage()]);
}

$conn->close();
?>