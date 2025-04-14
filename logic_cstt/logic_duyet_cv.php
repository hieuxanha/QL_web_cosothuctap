<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['stt_sv']) || !isset($_POST['ma_tuyen_dung']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ hoặc thiếu']);
    exit;
}

$stt_sv = $_POST['stt_sv'];
$ma_tuyen_dung = $_POST['ma_tuyen_dung'];
$action = $_POST['action'];

switch ($action) {
    case 'approve':
        $trang_thai = 'Đồng ý'; // Đã đổi từ 'Đã duyệt' thành 'Đồng ý'
        break;
    case 'reject':
        $trang_thai = 'Không đồng ý'; // Đã đổi từ 'Bị từ chối' thành 'Không đồng ý'
        break;
    case 'cancel':
    case 'restore':
        $trang_thai = 'Chờ duyệt';
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Hành động không hợp lệ']);
        exit;
}

// Kiểm tra giá trị trước khi lưu
if (!in_array($trang_thai, ['Chờ duyệt', 'Đồng ý', 'Không đồng ý'])) {
    echo json_encode(['success' => false, 'error' => 'Trạng thái không hợp lệ']);
    exit;
}

$sql_update = "UPDATE ung_tuyen SET trang_thai = ? WHERE stt_sv = ? AND ma_tuyen_dung = ?";
$stmt = $conn->prepare($sql_update);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sis", $trang_thai, $stt_sv, $ma_tuyen_dung);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'trang_thai' => $trang_thai, 'message' => 'Cập nhật trạng thái thành công!']);
} else {
    echo json_encode(['success' => false, 'error' => 'Lỗi thực thi SQL: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>