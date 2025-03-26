<?php
require '../db.php'; // Kết nối CSDL

header('Content-Type: application/json'); // Trả về JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stt_cty = $_POST['stt_cty'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $sql = "UPDATE cong_ty SET trang_thai = 'Đã duyệt' WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stt_cty);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'trang_thai' => 'Đã duyệt']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } elseif ($action == 'reject') {
        $sql = "UPDATE cong_ty SET trang_thai = 'Bị từ chối' WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stt_cty);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'trang_thai' => 'Bị từ chối']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } elseif ($action == 'restore' || $action == 'cancel') {
        // Khôi phục hoặc hủy duyệt: đặt lại trạng thái thành 'Chờ duyệt'
        $sql = "UPDATE cong_ty SET trang_thai = 'Chờ duyệt' WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stt_cty);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'trang_thai' => 'Chờ duyệt']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    }

    $stmt->close();
}

$conn->close();
?>