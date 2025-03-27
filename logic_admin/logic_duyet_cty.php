<?php
session_start();
require '../db.php'; // Kết nối CSDL

header('Content-Type: application/json'); // Trả về JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stt_cty = $_POST['stt_cty'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$stt_cty || !$action) {
        echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    if ($action == 'approve') {
        $sql = "UPDATE cong_ty SET trang_thai = 'Đã duyệt' WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stt_cty);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Công ty đã được duyệt thành công!";
            echo json_encode(['success' => true, 'trang_thai' => 'Đã duyệt']);
        } else {
            $_SESSION['error'] = "Lỗi khi duyệt công ty: " . $stmt->error;
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } elseif ($action == 'reject') {
        $sql = "UPDATE cong_ty SET trang_thai = 'Bị từ chối' WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stt_cty);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Công ty đã bị từ chối!";
            echo json_encode(['success' => true, 'trang_thai' => 'Bị từ chối']);
        } else {
            $_SESSION['error'] = "Lỗi khi từ chối công ty: " . $stmt->error;
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } elseif ($action == 'restore' || $action == 'cancel') {
        // Khôi phục hoặc hủy duyệt: đặt lại trạng thái thành 'Đang chờ'
        $sql = "UPDATE cong_ty SET trang_thai = 'Đang chờ' WHERE stt_cty = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $stt_cty);

        if ($stmt->execute()) {
            $message = $action == 'restore' ? "Công ty đã được khôi phục!" : "Công ty đã bị hủy duyệt!";
            $_SESSION['message'] = $message;
            echo json_encode(['success' => true, 'trang_thai' => 'Đang chờ']);
        } else {
            $_SESSION['error'] = "Lỗi khi khôi phục/hủy duyệt công ty: " . $stmt->error;
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Hành động không hợp lệ']);
        exit;
    }

    $stmt->close();
}

$conn->close();
?>