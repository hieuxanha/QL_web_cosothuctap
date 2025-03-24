<?php
include '../db.php'; // Kết nối CSDL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stt_cty = $_POST['stt_cty'];
    $action = $_POST['action']; // "approve" hoặc "reject"

    // Xác định trạng thái mới
    $trang_thai_moi = ($action == "approve") ? "Đã duyệt" : "Bị từ chối";

    // Cập nhật trạng thái công ty trong CSDL
    $sql = "UPDATE cong_ty SET trang_thai = ? WHERE stt_cty = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $trang_thai_moi, $stt_cty);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Cập nhật thành công!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
