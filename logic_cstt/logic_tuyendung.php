<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['action']) || !isset($_POST['ma_tuyen_dung'])) {
    echo json_encode(['success' => false, 'error' => 'Yêu cầu không hợp lệ!']);
    exit;
}

$ma_tuyen_dung = $_POST['ma_tuyen_dung'];
$action = $_POST['action'];

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Không thể kết nối cơ sở dữ liệu!']);
    exit;
}

// Kiểm tra trạng thái hiện tại của tin tuyển dụng
$sql_check = "SELECT trang_thai FROM tuyen_dung WHERE ma_tuyen_dung = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $ma_tuyen_dung);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'Tin tuyển dụng không tồn tại!']);
    $stmt_check->close();
    $conn->close();
    exit;
}

$row = $result_check->fetch_assoc();
$current_status = trim($row['trang_thai']);
$stmt_check->close();

// Kiểm tra trạng thái hợp lệ cho hành động
if ($action === 'approve' || $action === 'reject') {
    if ($current_status !== 'Đang chờ' && $current_status !== 'Bị từ chối') {
        echo json_encode(['success' => false, 'error' => 'Tin tuyển dụng không ở trạng thái phù hợp để thực hiện hành động này!']);
        $conn->close();
        exit;
    }
} elseif ($action === 'restore') {
    if ($current_status !== 'Bị từ chối') {
        echo json_encode(['success' => false, 'error' => 'Tin tuyển dụng không ở trạng thái bị từ chối!']);
        $conn->close();
        exit;
    }
} elseif ($action === 'cancel') {
    if ($current_status !== 'Đã duyệt') {
        echo json_encode(['success' => false, 'error' => 'Tin tuyển dụng không ở trạng thái đã duyệt!']);
        $conn->close();
        exit;
    }
}

$trang_thai = '';
$message = '';

switch ($action) {
    case 'approve':
        $trang_thai = 'Đã duyệt';
        $message = "Duyệt tin tuyển dụng thành công!";
        break;
    case 'reject':
        $trang_thai = 'Bị từ chối';
        $message = "Đã từ chối tin tuyển dụng!";
        break;
    case 'restore':
        $trang_thai = 'Đang chờ';
        $message = "Đã khôi phục tin tuyển dụng!";
        break;
    case 'cancel':
        $trang_thai = 'Đang chờ';
        $message = "Đã hủy duyệt tin tuyển dụng!";
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Hành động không hợp lệ!']);
        $conn->close();
        exit;
}

$sql = "UPDATE tuyen_dung SET trang_thai = ? WHERE ma_tuyen_dung = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $trang_thai, $ma_tuyen_dung);

if ($stmt->execute()) {
    $_SESSION['message'] = $message;
    echo json_encode(['success' => true, 'message' => $message, 'trang_thai' => $trang_thai]);
} else {
    $_SESSION['error'] = "Lỗi khi cập nhật tin tuyển dụng: " . $stmt->error;
    echo json_encode(['success' => false, 'error' => "Lỗi khi cập nhật tin tuyển dụng: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>