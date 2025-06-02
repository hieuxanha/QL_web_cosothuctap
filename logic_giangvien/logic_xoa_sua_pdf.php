<?php
session_start();

// Kiểm tra đăng nhập và vai trò giảng viên
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'giang_vien' || !isset($_SESSION['so_hieu_giang_vien'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập hoặc không có quyền truy cập']);
    exit();
}

// Kiểm tra CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'CSRF token không hợp lệ']);
    exit();
}

// Kết nối cơ sở dữ liệu
require_once '../db.php';
$conn->set_charset("utf8mb4");

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'delete') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!$id) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        exit();
    }

    // Lấy thông tin PDF để xóa file trên server
    $stmt = $conn->prepare("SELECT filepath FROM pdf_nhan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pdf = $result->fetch_assoc();
    $stmt->close();

    if (!$pdf) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy PDF']);
        exit();
    }

    // Xóa file trên server
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/Ql_web_cosothuctap/uploads/' . basename($pdf['filepath']);
    if (file_exists($fullPath)) {
        if (!unlink($fullPath)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không thể xóa file PDF trên server']);
            exit();
        }
    }

    // Xóa record trong bảng pdf_nhan
    $stmt = $conn->prepare("DELETE FROM pdf_nhan WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Xóa PDF thành công']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa PDF']);
    }

    $conn->close();
    exit();
}

if ($action === 'edit_grade') {
    $stt_danhgia = filter_input(INPUT_POST, 'stt_danhgia', FILTER_SANITIZE_NUMBER_INT);
    $ket_qua = filter_input(INPUT_POST, 'ket_qua', FILTER_SANITIZE_STRING); // Updated to match the form name 'ket_qua'

    if (!$stt_danhgia || !in_array($ket_qua, ['A', 'B+', 'B', 'C', 'D', 'F', ''])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit();
    }

    // Nếu ket_qua là rỗng, coi như thu hồi điểm
    $stmt = $conn->prepare("UPDATE pdf_nhan SET ket_qua = ? WHERE stt_danhgia = ?");
    $stmt->bind_param("si", $ket_qua, $stt_danhgia);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        $message = $ket_qua ? "Cập nhật điểm thành công" : "Thu hồi điểm thành công";
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $message, 'redirect' => 'http://localhost/QL_web_cosothuctap/giang_vien/ui_nhan_pdf.php']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật điểm']);
    }

    $conn->close();
    exit();
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
exit();
