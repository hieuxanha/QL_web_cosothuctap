<?php
session_start();
require_once '../db.php'; // Kết nối CSDL

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['ma_tuyen_dung'])) {
    $ma_tuyen_dung = $_POST['ma_tuyen_dung'];
    $action = $_POST['action'];

    // Kiểm tra xem tin tuyển dụng có tồn tại và đang ở trạng thái "Đang chờ" không
    $sql_check = "SELECT trang_thai FROM tuyen_dung WHERE ma_tuyen_dung = ? AND trang_thai = 'Đang chờ'";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $ma_tuyen_dung);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        $_SESSION['error'] = "Tin tuyển dụng không tồn tại hoặc không ở trạng thái chờ duyệt!";
        header("Location: ../admin/ui_quanlytt.php");
        exit();
    }
    $stmt_check->close();

    // Xử lý hành động
    if ($action == 'approve') {
        // Cập nhật trạng thái thành "Đang tuyển"
        $sql = "UPDATE tuyen_dung SET trang_thai = 'Đang tuyển' WHERE ma_tuyen_dung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ma_tuyen_dung);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Duyệt tin tuyển dụng thành công! Tin đã được chuyển sang trạng thái 'Đang tuyển'.";
        } else {
            $_SESSION['error'] = "Lỗi khi duyệt tin: " . $stmt->error;
        }
    } elseif ($action == 'reject') {
        // Cập nhật trạng thái thành "Bị từ chối"
        $sql = "UPDATE tuyen_dung SET trang_thai = 'Bị từ chối' WHERE ma_tuyen_dung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ma_tuyen_dung);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Đã từ chối tin tuyển dụng! Tin đã được chuyển sang trạng thái 'Bị từ chối'.";
        } else {
            $_SESSION['error'] = "Lỗi khi từ chối tin: " . $stmt->error;
        }
    } else {
        $_SESSION['error'] = "Hành động không hợp lệ!";
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Yêu cầu không hợp lệ!";
}

header("Location: ../admin/ui_quanlytt.php");
exit();
?>